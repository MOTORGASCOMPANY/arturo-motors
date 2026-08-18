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

        // Serie diaria para el gráfico
        $dias = min($desde->diffInDays($hasta) + 1, 90);
        $ventasPorDia = $this->baseQuery($desde, $hasta)
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $labels = [];
        $data = [];
        for ($i = 0; $i < $dias; $i++) {
            $fecha = $desde->copy()->addDays($i);
            $clave = $fecha->format('Y-m-d');
            $labels[] = $fecha->format('d/m');
            $data[] = (float) ($ventasPorDia[$clave] ?? 0);
        }

        return view('livewire.servicios.reporte', [
            'totalVentas' => $totalVentas,
            'totalOrdenes' => $totalOrdenes,
            'ticketPromedio' => $totalOrdenes > 0 ? $totalVentas / $totalOrdenes : 0,
            'ventasPorServicio' => $ventasPorServicio,
            'ventasPorTecnico' => $ventasPorTecnico,
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
