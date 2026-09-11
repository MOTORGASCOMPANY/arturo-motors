<?php

namespace App\Livewire\Servicios;

use App\Models\Comprobante;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Reporte extends Component
{
    public string $desde;
    public string $hasta;
    public string $tipoServicio = 'todos';

    public function mount()
    {
        $this->desde = now()->startOfMonth()->format('Y-m-d');
        $this->hasta = now()->format('Y-m-d');
    }

    protected function rangoValido(): array
    {
        $desde = Carbon::parse($this->desde)->startOfDay();
        $hasta = Carbon::parse($this->hasta)->endOfDay();

        return $desde->gt($hasta) ? [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()] : [$desde, $hasta];
    }

    public function descargarPdf(): void
    {
        $this->dispatch('descargar-pdf', url: url('/reporte-servicios/pdf?' . http_build_query(array_filter([
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'tipoServicio' => $this->tipoServicio,
        ]))));
    }

    public function descargarExcel(): void
    {
        $this->dispatch('descargar-excel', url: url('/reporte-servicios/excel?' . http_build_query(array_filter([
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'tipoServicio' => $this->tipoServicio,
        ]))));
    }

    protected function baseQuery($desde, $hasta)
    {
        return Comprobante::whereBetween('created_at', [$desde, $hasta])
            ->when($this->tipoServicio !== 'todos', function ($q) {
                $q->whereHas('serviceOrder.service', fn ($s) => $s->where('tipo', $this->tipoServicio));
            });
    }

    public function render()
    {
        [$desde, $hasta] = $this->rangoValido();

        $totalVentas = $this->baseQuery($desde, $hasta)->sum('monto');
        $totalOrdenes = $this->baseQuery($desde, $hasta)->count();

        $comprobantes = $this->baseQuery($desde, $hasta)
            ->with(['serviceOrder.service', 'serviceOrder.tecnico'])
            ->get();

        $ventasPorServicio = $comprobantes
            ->groupBy(fn ($c) => $c->serviceOrder->service->nombre)
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('monto'),
            ])
            ->sortByDesc('total');

        $ventasPorTecnico = $comprobantes
            ->filter(fn ($c) => $c->serviceOrder->tecnico_id !== null)
            ->groupBy(fn ($c) => $c->serviceOrder->tecnico->name)
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('monto'),
            ])
            ->sortByDesc('total');

        // ─── Datos para gráfico de servicios por día ─────────────────
        $dias = min($desde->diffInDays($hasta) + 1, 90);

        // Contar órdenes por día y categoría
        $ordenesPorDia = \App\Models\ServiceOrder::whereBetween('created_at', [$desde, $hasta])
            ->with('service:id,nombre,tipo')
            ->get();

        $conversionPendientes = [];
        $conversionCompletadas = [];
        $simpleCompletadas = [];

        for ($i = 0; $i < $dias; $i++) {
            $fecha = $desde->copy()->addDays($i)->format('Y-m-d');
            $labels[] = $desde->copy()->addDays($i)->format('d/m');

            $diaOrdenes = $ordenesPorDia->filter(fn ($o) => $o->created_at->format('Y-m-d') === $fecha);

            // Conversión: pendiente = creada/evaluada/aprobado/en_conversion/completada (NO entregada)
            $conversionPendientes[] = $diaOrdenes
                ->filter(fn ($o) => $o->service && $o->service->tipo === 'conversion' && $o->estado !== 'entregada')
                ->count();

            // Conversión: completada = entregada
            $conversionCompletadas[] = $diaOrdenes
                ->filter(fn ($o) => $o->service && $o->service->tipo === 'conversion' && $o->estado === 'entregada')
                ->count();

            // Simple: completada = entregada (se resuelve al momento)
            $simpleCompletadas[] = $diaOrdenes
                ->filter(fn ($o) => $o->service && $o->service->tipo === 'simple' && $o->estado === 'entregada')
                ->count();
        }

        // Totales para KPIs
        $totalConversionesPendientes = \App\Models\ServiceOrder::where('estado', '!=', 'entregada')
            ->whereHas('service', fn ($s) => $s->where('tipo', 'conversion'))
            ->count();
        $totalConversionesCompletadas = \App\Models\ServiceOrder::where('estado', 'entregada')
            ->whereHas('service', fn ($s) => $s->where('tipo', 'conversion'))
            ->count();

        // Simple: completadas = entregada (se resuelven al momento)
        $totalSimplesCompletadas = \App\Models\ServiceOrder::where('estado', 'entregada')
            ->whereHas('service', fn ($s) => $s->where('tipo', 'simple'))
            ->count();

        $hayDatos = $totalOrdenes > 0;

        // Dispatch chart data to JS (wire:ignore prevents morph from updating data-* attrs)
        $this->dispatch('chart-data-updated',
            labels: $labels ?? [],
            conversionPendientes: $conversionPendientes,
            conversionCompletadas: $conversionCompletadas,
            simpleCompletadas: $simpleCompletadas
        );

        return view('livewire.servicios.reporte', [
            'totalVentas' => $totalVentas,
            'totalOrdenes' => $totalOrdenes,
            'ticketPromedio' => $totalOrdenes > 0 ? $totalVentas / $totalOrdenes : 0,
            'ventasPorServicio' => $ventasPorServicio,
            'ventasPorTecnico' => $ventasPorTecnico,
            'labels' => $labels ?? [],
            'conversionPendientes' => $conversionPendientes,
            'conversionCompletadas' => $conversionCompletadas,
            'simpleCompletadas' => $simpleCompletadas,
            'desde' => $desde,
            'hasta' => $hasta,
            'tipoServicio' => $this->tipoServicio,
            'totalConversionesPendientes' => $totalConversionesPendientes,
            'totalConversionesCompletadas' => $totalConversionesCompletadas,
            'totalSimplesCompletadas' => $totalSimplesCompletadas,
            'hayDatos' => $hayDatos,
        ]);
    }
}
