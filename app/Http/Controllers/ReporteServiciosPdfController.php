<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReporteServiciosPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $desdeStr = $request->desde ?? now()->startOfMonth()->format('Y-m-d');
        $hastaStr = $request->hasta ?? now()->format('Y-m-d');
        $desde = Carbon::parse($desdeStr);
        $hasta = Carbon::parse($hastaStr);

        $comprobantes = Comprobante::whereBetween('created_at', [$desdeStr . ' 00:00:00', $hastaStr . ' 23:59:59'])
            ->when($request->tipoServicio !== 'todos', function ($q) {
                $q->whereHas('serviceOrder.service', fn ($s) => $s->where('tipo', $request->tipoServicio));
            })
            ->with(['serviceOrder.service', 'serviceOrder.tecnico'])
            ->get();

        $totalVentas = $comprobantes->sum('monto');
        $totalOrdenes = $comprobantes->count();

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
        $dias = min(($desde->diffInDays($hasta) + 1), 90);
        $ventasPorDia = Comprobante::whereBetween('created_at', [$desdeStr . ' 00:00:00', $hastaStr . ' 23:59:59'])
            ->when($request->tipoServicio !== 'todos', function ($q) {
                $q->whereHas('serviceOrder.service', fn ($s) => $s->where('tipo', $request->tipoServicio));
            })
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

        $pdf = Pdf::loadView('pdfs.reporte-servicios', [
            'comprobantes' => $comprobantes,
            'totalVentas' => $totalVentas,
            'totalOrdenes' => $totalOrdenes,
            'ticketPromedio' => $totalOrdenes > 0 ? $totalVentas / $totalOrdenes : 0,
            'ventasPorServicio' => $ventasPorServicio,
            'ventasPorTecnico' => $ventasPorTecnico,
            'labels' => $labels,
            'data' => $data,
            'desde' => $desde,
            'hasta' => $hasta,
            'tipoServicio' => $request->tipoServicio ?? 'todos',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-servicios-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}