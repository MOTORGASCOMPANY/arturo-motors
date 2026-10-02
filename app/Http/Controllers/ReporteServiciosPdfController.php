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

        // Mismo criterio que la pantalla: día completo e intercambio si el rango viene invertido.
        $desde = Carbon::parse($desdeStr)->startOfDay();
        $hasta = Carbon::parse($hastaStr)->endOfDay();

        if ($desde->gt($hasta)) {
            [$desde, $hasta] = [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()];
        }

        $desdeStr = $desde->format('Y-m-d');
        $hastaStr = $hasta->format('Y-m-d');

        $tipoServicio = $request->input('tipoServicio', 'todos');

        $comprobantes = Comprobante::whereBetween('created_at', [$desde, $hasta])
            ->when($tipoServicio !== 'todos', function ($q) use ($request) {
                $q->whereHas('serviceOrder.service', fn ($s) => $s->where('tipo', $request->tipoServicio));
            })
            ->with(['serviceOrder.service', 'serviceOrder.tecnico'])
            ->get();

        $totalVentas = $comprobantes->sum('monto');
        // Órdenes distintas cobradas, igual que en pantalla: varios comprobantes
        // de la misma orden no deben contar dos veces.
        $totalOrdenes = $comprobantes->pluck('service_order_id')->filter()->unique()->count();

        $ventasPorServicio = $comprobantes
            ->groupBy(fn ($c) => $c->serviceOrder?->service?->nombre ?? 'Sin servicio')
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('monto'),
            ])
            ->sortByDesc('total');

        $ventasPorTecnico = $comprobantes
            ->filter(fn ($c) => $c->serviceOrder?->tecnico_id !== null)
            ->groupBy(fn ($c) => $c->serviceOrder?->tecnico?->name ?? 'Sin técnico')
            ->map(fn ($grupo) => [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('monto'),
            ])
            ->sortByDesc('total');

        // Serie diaria para el gráfico
        // Carbon 3 devuelve diffInDays() como FLOAT: sin el cast a int el bucle
        // corre una iteración extra y agrega un día fantasma más allá de `hasta`.
        $dias = (int) $desde->diffInDays($hasta) + 1;
        $ventasPorDia = Comprobante::whereBetween('created_at', [$desde, $hasta])
            ->when($tipoServicio !== 'todos', function ($q) use ($request) {
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
            'tipoServicio' => $tipoServicio,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-servicios-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}