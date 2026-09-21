<?php

namespace App\Http\Controllers;

use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteCajaPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $desde = $request->desde ?? now()->startOfMonth()->format('Y-m-d');
        $hasta = $request->hasta ?? now()->format('Y-m-d');

        $sesiones = SesionCaja::with('abiertaPor')
            ->whereBetween('abierta_en', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderByDesc('abierta_en')
            ->get();

        $totalIngresos = MovimientoCaja::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where('tipo', 'ingreso')
            ->sum('monto');

        $totalEgresos = MovimientoCaja::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where('tipo', 'egreso')->sum('monto');

        $sesionesConDescuadre = $sesiones->filter(fn ($s) => $s->diferencia !== null && (float) $s->diferencia != 0);

        $ultimaSesion = SesionCaja::where('estado', 'cerrada')->orderByDesc('cerrada_en')->first();
        $efectivoAnterior = $ultimaSesion ? (float) $ultimaSesion->monto_cierre : 0;

        $ingresosPorMetodo = MovimientoCaja::where('tipo', 'ingreso')
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->whereNotNull('metodo_pago')
            ->selectRaw('metodo_pago, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');

        $pdf = Pdf::loadView('pdfs.reporte-caja', [
            'sesiones' => $sesiones,
            'totalIngresos' => $totalIngresos,
            'totalEgresos' => $totalEgresos,
            'neto' => $totalIngresos - $totalEgresos,
            'sesionesConDescuadre' => $sesionesConDescuadre,
            'efectivoAnterior' => $efectivoAnterior,
            'ingresosPorMetodo' => $ingresosPorMetodo,
            'desde' => $desde,
            'hasta' => $hasta,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-caja-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}
