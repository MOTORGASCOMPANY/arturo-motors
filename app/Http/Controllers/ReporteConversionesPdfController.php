<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use App\Models\Sede;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteConversionesPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = ServiceOrder::with([
            'cliente',
            'vehiculo',
            'service',
            'tecnico',
            'items.producto.categoria',
            'items.kitPadre.producto',
        ])->whereIn('estado', ['en_conversion', 'conversion_completada']);

        if ($request->filled('sede_id')) {
            $query->whereHas('items', fn ($q) => $q->where('sede_id', (int) $request->input('sede_id')));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('desde')) {
            $query->where('fecha_inicio_conversion', '>=', $request->input('desde'));
        }

        if ($request->filled('hasta')) {
            $query->where('fecha_inicio_conversion', '<=', $request->input('hasta') . ' 23:59:59');
        }

        $ordenes = $query->orderByDesc('fecha_inicio_conversion')->get();

        $totalConversiones = $ordenes->count();
        $completadas = $ordenes->where('estado', 'conversion_completada')->count();
        $enProceso = $ordenes->where('estado', 'en_conversion')->count();
        $itemsInstalados = ItemSerializado::where('estado', 'instalado')
            ->whereNotNull('kit_padre_id')
            ->count();

        // Stock (kits sellados o completados a mano)
        $kitsEnStock = ItemSerializado::kitDisponible()
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->count();

        $stockPiezasSueltas = ItemSerializado::where('estado', 'en_stock')
            ->whereNull('kit_padre_id')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false))
            ->count();

        // Detalle por orden
        $detalleOrdenes = $ordenes->map(function ($o) {
            $kitPadre = $o->items->first(fn ($i) => $i->kit_padre_id === null && ($i->producto->categoria->es_kit ?? false));
            $hijos = $o->items->where('kit_padre_id', '!=', null);
            $instalados = $hijos->where('estado', 'instalado');

            return [
                'orden' => $o,
                'cliente' => $o->cliente->nombre . ' ' . $o->cliente->apellido,
                'placa' => $o->vehiculo->placa ?? 'N/A',
                'vehiculo' => trim(($o->vehiculo->marca ?? '') . ' ' . ($o->vehiculo->modelo ?? '')),
                'tecnico' => $o->tecnico->name ?? 'N/A',
                'kit' => $kitPadre?->producto->nombre ?? 'N/A',
                'generacion' => $kitPadre?->producto->atributos['generacion'] ?? '',
                'total_componentes' => $hijos->count(),
                'instalados' => $instalados->count(),
                'items_serializados' => $instalados
                    ->filter(fn ($i) => ($i->atributos['tipo'] ?? '') !== 'cantidad' && !empty($i->serie))
                    ->map(fn ($i) => [
                        'nombre' => $i->producto->nombre,
                        'serie' => $i->serie,
                    ])->values()->toArray(),
                'reportes' => $o->reportesPendientes()->count(),
                'fecha_inicio' => $o->fecha_inicio_conversion?->format('d/m/Y H:i'),
                'fecha_fin' => $o->fecha_fin_conversion?->format('d/m/Y H:i'),
            ];
        });

        $pdf = Pdf::loadView('pdfs.reporte-conversiones', [
            'ordenes' => $ordenes,
            'detalleOrdenes' => $detalleOrdenes,
            'totalConversiones' => $totalConversiones,
            'completadas' => $completadas,
            'enProceso' => $enProceso,
            'itemsInstalados' => $itemsInstalados,
            'kitsEnStock' => $kitsEnStock,
            'stockPiezasSueltas' => $stockPiezasSueltas,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-conversiones-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}
