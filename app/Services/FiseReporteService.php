<?php

namespace App\Services;

use App\Models\FisePago;
use App\Models\FiseSolicitud;
use App\Models\MovimientoCaja;
use Illuminate\Support\Carbon;

class FiseReporteService
{
    public function datos(?string $desde = null, ?string $hasta = null): array
    {
        $desde ??= now()->subDays(29)->format('Y-m-d');
        $hasta ??= now()->format('Y-m-d');

        [$desde, $hasta] = $this->rangoValido($desde, $hasta);

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
        // Periodo + créditos con saldo abierto: la deuda viva no expira con el calendario (coincide con Control FISE)
        $pagos = FisePago::where(function ($q) use ($desde, $hasta) {
                $q->whereBetween('created_at', [$desde, $hasta])
                  ->orWhereColumn('monto_pagado', '<', 'monto_total');
            })
            ->with(['serviceOrder.cliente', 'serviceOrder.vehiculo', 'pagadoPor'])
            ->orderByDesc('created_at')
            ->get();

        $totalPagos = $pagos->count();
        $montoTotalFise = (float) $pagos->sum('monto_total');
        $montoPagadoFise = (float) $pagos->sum('monto_pagado');
        $saldoPendiente = $montoTotalFise - $montoPagadoFise;
        $tasaPago = $montoTotalFise > 0 ? round(($montoPagadoFise / $montoTotalFise) * 100, 1) : 0;

        $pagosPagados = $pagos->where('estado', 'pagado')->count();
        $pagosParciales = $pagos->where('estado', 'parcial')->count();
        $pagosPendientes = $pagos->where('estado', 'pendiente')->count();

        // ─── Ingresos FISE por caja ───
        $ingresosCajaFise = MovimientoCaja::where('tipo', 'ingreso')
            ->where('metodo_pago', 'fise')
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('monto');

        // ─── Datos para gráficos por día ───
        $dias = min((int) $desde->diffInDays($hasta) + 1, 90);
        $labels = [];
        $pagosPagadosPorDia = [];
        $pagosParcialesPorDia = [];
        $pagosPendientesPorDia = [];
        $tasaPagoPorDia = [];
        $montoTotalPorDia = [];
        $montoPagadoPorDia = [];
        $saldoPendientePorDia = [];

        for ($i = 0; $i < $dias; $i++) {
            $finDia = $desde->copy()->addDays($i)->endOfDay();
            $labels[] = $desde->copy()->addDays($i)->format('d/m');

            // Estado del portafolio al cierre de cada día (con estado y montos actuales)
            $hastaDia = $pagos->filter(fn ($p) => $p->created_at->lte($finDia));
            $pagosPagadosPorDia[] = $hastaDia->where('estado', 'pagado')->count();
            $pagosParcialesPorDia[] = $hastaDia->where('estado', 'parcial')->count();
            $pagosPendientesPorDia[] = $hastaDia->where('estado', 'pendiente')->count();

            $diaMontoTotal = (float) $hastaDia->sum('monto_total');
            $diaMontoPagado = (float) $hastaDia->sum('monto_pagado');
            $montoTotalPorDia[] = $diaMontoTotal;
            $montoPagadoPorDia[] = $diaMontoPagado;
            $saldoPendientePorDia[] = $diaMontoTotal - $diaMontoPagado;
            $tasaPagoPorDia[] = $diaMontoTotal > 0 ? round(($diaMontoPagado / $diaMontoTotal) * 100, 1) : 0;
        }

        // ─── Pagos por técnico ───
        $pagosPorTecnico = $pagos->filter(fn ($p) => $p->pagadoPor)
            ->groupBy(fn ($p) => $p->pagadoPor->name ?? 'N/A')
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'monto_total' => $grupo->sum('monto_total'),
                'monto_pagado' => $grupo->sum('monto_pagado'),
            ])->sortByDesc('monto_total');

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            // Solicitudes
            'solicitudes' => $solicitudes,
            'totalSolicitudes' => $totalSolicitudes,
            'solicitudesAprobadas' => $solicitudesAprobadas,
            'solicitudesRechazadas' => $solicitudesRechazadas,
            'solicitudesPendientes' => $solicitudesPendientes,
            'tasaAprobacion' => $tasaAprobacion,
            // Pagos
            'pagos' => $pagos,
            'totalPagos' => $totalPagos,
            'montoTotalFise' => $montoTotalFise,
            'montoPagadoFise' => $montoPagadoFise,
            'saldoPendiente' => $saldoPendiente,
            'pagosPagados' => $pagosPagados,
            'pagosParciales' => $pagosParciales,
            'pagosPendientes' => $pagosPendientes,
            'tasaPago' => $tasaPago,
            // Caja
            'ingresosCajaFise' => $ingresosCajaFise,
            // Por técnico
            'pagosPorTecnico' => $pagosPorTecnico,
            // Charts
            'labels' => $labels,
            'pagosPagadosPorDia' => $pagosPagadosPorDia,
            'pagosParcialesPorDia' => $pagosParcialesPorDia,
            'pagosPendientesPorDia' => $pagosPendientesPorDia,
            'tasaPagoPorDia' => $tasaPagoPorDia,
            'montoTotalPorDia' => $montoTotalPorDia,
            'montoPagadoPorDia' => $montoPagadoPorDia,
            'saldoPendientePorDia' => $saldoPendientePorDia,
        ];
    }

    private function rangoValido(string $desde, string $hasta): array
    {
        $desde = Carbon::parse($desde)->startOfDay();
        $hasta = Carbon::parse($hasta)->endOfDay();

        return $desde->gt($hasta) ? [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()] : [$desde, $hasta];
    }
}
