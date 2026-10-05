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
        ])->tipoConversion()
            ->whereIn('estado', ServiceOrder::ESTADOS_CONVERSION);

        $filtroSede = null;
        if ($request->filled('sede_id')) {
            $filtroSede = (int) $request->input('sede_id');
            $query->whereHas('items', fn ($q) => $q->where('sede_id', $filtroSede));
        }

        $filtroEstado = 'todos';
        if ($request->filled('estado') && $request->input('estado') !== 'todos') {
            $filtroEstado = $request->input('estado');
            $query->where('estado', $filtroEstado);
        }

        if ($request->filled('tecnico_id')) {
            $query->where('tecnico_id', (int) $request->input('tecnico_id'));
        }

        // Mismo criterio de fecha que la vista del reporte.
        if ($request->filled('desde')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('desde'))
                    ->orWhereDate('fecha_inicio_conversion', '>=', $request->input('desde'));
            });
        }

        if ($request->filled('hasta')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('hasta'))
                    ->orWhereDate('fecha_inicio_conversion', '<=', $request->input('hasta'));
            });
        }

        // Mismo orden que la tabla de la vista.
        $ordenes = $query->orderByDesc('created_at')->get();
        $ordenIds = $ordenes->pluck('id');

        $totalConversiones = $ordenes->count();
        $completadas = $ordenes->where('estado', 'conversion_completada')->count();
        $enProceso = $ordenes->where('estado', 'en_conversion')->count();

        // Items instalados en órdenes filtradas (incluye hijos de kit)
        $itemsInstalados = ItemSerializado::where('estado', 'instalado')
            ->where(function ($q) use ($ordenIds) {
                $q->whereIn('service_order_id', $ordenIds)
                    ->orWhereHas('kitPadre', fn ($qq) => $qq->whereIn('service_order_id', $ordenIds));
            })
            ->when($filtroSede, fn ($q) => $q->where(function ($qq) use ($filtroSede) {
                $qq->where('sede_id', $filtroSede)
                    ->orWhereHas('kitPadre', fn ($qqq) => $qqq->where('sede_id', $filtroSede));
            }))
            ->count();

        // Detalle por orden (mismo formato que la vista Livewire)
        $detalleOrdenes = $ordenes->map(function ($o) {
            $kitPadre = $o->items->first(fn ($i) => $i->kit_padre_id === null && ($i->producto->categoria->es_kit ?? false));
            $hijos = $o->items->where('kit_padre_id', '!=', null);
            $instalados = $hijos->where('estado', 'instalado');

            return [
                'orden' => $o,
                'cliente' => trim(($o->cliente?->nombre ?? '') . ' ' . ($o->cliente?->apellido ?? '')) ?: '—',
                'placa' => $o->vehiculo->placa ?? 'N/A',
                'vehiculo' => trim(($o->vehiculo->marca ?? '') . ' ' . ($o->vehiculo->modelo ?? '')),
                'tecnico' => $o->tecnico->name ?? 'N/A',
                'kit_nombre' => $kitPadre?->producto->nombre ?? 'N/A',
                'kit_generacion' => $kitPadre?->producto->atributos['generacion'] ?? '',
                'items_serializados' => $instalados
                    ->filter(fn ($i) => ($i->atributos['tipo'] ?? '') !== 'cantidad' && !empty($i->serie))
                    ->map(fn ($i) => [
                        'nombre' => $i->producto->nombre,
                        'serie' => $i->serie,
                    ])->values()->toArray(),
                'total_componentes' => $hijos->count(),
                'instalados' => $instalados->count(),
                'fecha_inicio' => $o->fecha_inicio_conversion?->format('d/m/Y H:i'),
                'fecha_fin' => $o->fecha_fin_conversion?->format('d/m/Y H:i'),
            ];
        });

        $sedeNombre = null;
        if ($filtroSede) {
            $sedeNombre = Sede::find($filtroSede)?->nombre;
        }

        $desde = $request->input('desde') ?? now()->startOfMonth()->format('Y-m-d');
        $hasta = $request->input('hasta') ?? now()->format('Y-m-d');

        $pdf = Pdf::loadView('pdfs.reporte-conversiones', [
            'ordenes' => $ordenes,
            'detalleOrdenes' => $detalleOrdenes,
            'totalConversiones' => $totalConversiones,
            'completadas' => $completadas,
            'enProceso' => $enProceso,
            'itemsInstalados' => $itemsInstalados,
            'filtroEstado' => $filtroEstado,
            'filtroSede' => $filtroSede,
            'sedeNombre' => $sedeNombre,
            'desde' => $desde,
            'hasta' => $hasta,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-conversiones-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}