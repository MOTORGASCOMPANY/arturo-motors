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

        $desdeDt = $desde . ' 00:00:00';
        $hastaDt = $hasta . ' 23:59:59';

        $sesiones = SesionCaja::with('abiertaPor')
            ->whereBetween('abierta_en', [$desdeDt, $hastaDt])
            ->orderByDesc('abierta_en')
            ->get();

        $totalIngresos = MovimientoCaja::ingresos()->enRango($desdeDt, $hastaDt)->sum('monto');
        $totalEgresos = MovimientoCaja::egresos()->enRango($desdeDt, $hastaDt)->sum('monto');

        $sesionesConDescuadre = $sesiones->filter(fn ($s) => $s->diferencia !== null && (float) $s->diferencia != 0);

        $efectivoAnterior = SesionCaja::getLastCierre() ?? 0;

        $ingresosPorMetodo = MovimientoCaja::ingresos()
            ->enRango($desdeDt, $hastaDt)
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
