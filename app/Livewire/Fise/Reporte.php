<?php

namespace App\Livewire\Fise;

use App\Models\FiseSolicitud;
use App\Models\FisePago;
use App\Models\MovimientoCaja;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Reporte extends Component
{
    public string $desde = '';
    public string $hasta = '';

    public function mount(): void
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

    public function render()
    {
        [$desde, $hasta] = $this->rangoValido();

        // ─── Solicitudes FISE ───
        $solicitudes = FiseSolicitud::whereBetween('created_at', [$desde, $hasta])
            ->with(['cliente', 'vehiculo'])
            ->orderByDesc('created_at')
            ->get();

        $totalSolicitudes = $solicitudes->count();
        $solicitudesAprobadas = $solicitudes->where('estado', 'aprobado')->count();
        $solicitudesRechazadas = $solicitudes->where('estado', 'rechazado')->count();
        $solicitudesPendientes = $solicitudes->where('estado', 'pendiente')->count();
        $tasaAprobacion = $totalSolicitudes > 0 ? round(($solicitudesAprobadas / $totalSolicitudes) * 100, 1) : 0;

        // ─── Pagos FISE ───
        $pagos = FisePago::whereBetween('created_at', [$desde, $hasta])
            ->with(['serviceOrder.cliente', 'serviceOrder.vehiculo', 'pagadoPor'])
            ->orderByDesc('created_at')
            ->get();

        $totalPagos = $pagos->count();
        $montoTotalFise = (float) $pagos->sum('monto_total');
        $montoPagadoFise = (float) $pagos->sum('monto_pagado');
        $saldoPendiente = $montoTotalFise - $montoPagadoFise;

        $pagosPagados = $pagos->where('estado', 'pagado')->count();
        $pagosParciales = $pagos->where('estado', 'parcial')->count();
        $pagosPendientes = $pagos->where('estado', 'pendiente')->count();

        // ─── Ingresos FISE por caja ───
        $ingresosCajaFise = MovimientoCaja::where('tipo', 'ingreso')
            ->where('metodo_pago', 'fise')
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('monto');

        // ─── Datos para gráfico de solicitudes por día ───
        $dias = min($desde->diffInDays($hasta) + 1, 90);
        $labels = [];
        $aprobadasPorDia = [];
        $rechazadasPorDia = [];
        $pendientesPorDia = [];
        $montoTotalPorDia = [];
        $montoPagadoPorDia = [];
        $saldoPendientePorDia = [];

        for ($i = 0; $i < $dias; $i++) {
            $fecha = $desde->copy()->addDays($i)->format('Y-m-d');
            $labels[] = $desde->copy()->addDays($i)->format('d/m');
            
            // Solicitudes por día
            $diaSol = $solicitudes->filter(fn ($s) => $s->created_at->format('Y-m-d') === $fecha);
            $aprobadasPorDia[] = $diaSol->where('estado', 'aprobado')->count();
            $rechazadasPorDia[] = $diaSol->where('estado', 'rechazado')->count();
            $pendientesPorDia[] = $diaSol->where('estado', 'pendiente')->count();

            // Pagos por día (montos)
            $diaPag = $pagos->filter(fn ($p) => $p->created_at->format('Y-m-d') === $fecha);
            $montoTotalPorDia[] = (float) $diaPag->sum('monto_total');
            $montoPagadoPorDia[] = (float) $diaPag->sum('monto_pagado');
            $saldoPendientePorDia[] = (float) $diaPag->sum('monto_total') - (float) $diaPag->sum('monto_pagado');
        }

        // ─── Pagos por técnico ───
        $pagosPorTecnico = $pagos->filter(fn ($p) => $p->pagadoPor)
            ->groupBy(fn ($p) => $p->pagadoPor->name ?? 'N/A')
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'monto_total' => $grupo->sum('monto_total'),
                'monto_pagado' => $grupo->sum('monto_pagado'),
            ])->sortByDesc('monto_total');

        $this->dispatch('chart-data-fise',
            labels: $labels,
            aprobadas: $aprobadasPorDia,
            rechazadas: $rechazadasPorDia,
            pendientes: $pendientesPorDia,
        );

        $this->dispatch('chart-pagos-fise',
            labels: $labels,
            montoTotal: $montoTotalPorDia,
            montoPagado: $montoPagadoPorDia,
            saldoPendiente: $saldoPendientePorDia,
        );

        return view('livewire.fise.reporte', [
            'desde' => $desde,
            'hasta' => $hasta,
            // Solicitudes
            'solicitudes' => $solicitudes,
            'totalSolicitudes' => $totalSolicitudes,
            'solicitudesAprobadas' => $solicitudesAprobadas,
            'solicitudesRechazadas' => $solicitudesRechazadas,
            'solicitudesPendientes' => $solicitudesPendientes,
            'tasaAprobacion' => $tasaAprobacion,
            // Pagos (totales para tarjetas si se usan en otro lado)
            'pagos' => $pagos,
            'totalPagos' => $totalPagos,
            'montoTotalFise' => $montoTotalFise,
            'montoPagadoFise' => $montoPagadoFise,
            'saldoPendiente' => $saldoPendiente,
            'pagosPagados' => $pagosPagados,
            'pagosParciales' => $pagosParciales,
            'pagosPendientes' => $pagosPendientes,
            // Caja
            'ingresosCajaFise' => $ingresosCajaFise,
            // Por técnico
            'pagosPorTecnico' => $pagosPorTecnico,
            // Charts
            'labels' => $labels,
            'montoTotalPorDia' => $montoTotalPorDia,
            'montoPagadoPorDia' => $montoPagadoPorDia,
            'saldoPendientePorDia' => $saldoPendientePorDia,
        ]);
    }

    public function descargarPdf(): void
    {
        $this->dispatch('descargar-pdf', url: '#');
    }

    public function descargarExcel(): void
    {
        $this->dispatch('descargar-excel', url: '#');
    }
}
