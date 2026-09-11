<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\Sede;
use Livewire\Component;

class Reporte extends Component
{
    // Filtros
    public ?int $filtroSede = null;
    public string $filtroEstado = 'todos'; // todos, completada, en_conversion
    public ?string $filtroFechaDesde = null;
    public ?string $filtroFechaHasta = null;

    public function exportPdfUrl(): string
    {
        return route('ReporteConversiones.Pdf', $this->getFiltros());
    }

    public function exportExcelUrl(): string
    {
        return route('ReporteConversiones.Excel', $this->getFiltros());
    }

    private function getFiltros(): array
    {
        return array_filter([
            'sede_id' => $this->filtroSede,
            'estado' => $this->filtroEstado !== 'todos' ? $this->filtroEstado : null,
            'desde' => $this->filtroFechaDesde,
            'hasta' => $this->filtroFechaHasta,
        ]);
    }

    public function render()
    {
        $query = ServiceOrder::with([
            'cliente',
            'vehiculo',
            'service',
            'tecnico',
            'items.producto.categoria',
            'items.kitPadre.producto',
        ])->whereIn('estado', ['en_conversion', 'conversion_completada']);

        // Filtros
        if ($this->filtroSede) {
            $query->whereHas('items', fn ($q) => $q->where('sede_id', $this->filtroSede));
        }

        if ($this->filtroEstado !== 'todos') {
            $query->where('estado', $this->filtroEstado);
        }

        if ($this->filtroFechaDesde) {
            $query->where('fecha_inicio_conversion', '>=', $this->filtroFechaDesde);
        }

        if ($this->filtroFechaHasta) {
            $query->where('fecha_inicio_conversion', '<=', $this->filtroFechaHasta . ' 23:59:59');
        }

        $ordenes = $query->orderByDesc('fecha_inicio_conversion')->get();

        // ─── KPIs ───
        $totalConversiones = $ordenes->count();
        $completadas = $ordenes->where('estado', 'conversion_completada')->count();
        $enProceso = $ordenes->where('estado', 'en_conversion')->count();

        // Items instalados (todos los hijos de kits en estado instalado)
        $itemsInstalados = ItemSerializado::where('estado', 'instalado')
            ->whereNotNull('kit_padre_id')
            ->whereHas('serviceOrder', fn ($q) => $q->whereIn('estado', ['en_conversion', 'conversion_completada']))
            ->count();

        // Piezas por cantidad instaladas (CANT- o tipo cantidad)
        $piezasCantidad = ItemSerializado::where('estado', 'instalado')
            ->whereNotNull('kit_padre_id')
            ->where(function ($q) {
                $q->where('serie', 'like', 'CANT-%')
                  ->orWhereRaw("JSON_EXTRACT(atributos, '$.tipo') = 'cantidad'");
            })
            ->count();

        // Balance del almacén — piezas sueltas en stock (no kits)
        $stockPiezasSueltas = ItemSerializado::where('estado', 'en_stock')
            ->whereNull('kit_padre_id')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false))
            ->count();

        // Kits en stock
        $kitsEnStock = ItemSerializado::where('estado', 'en_stock')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->count();

        // ─── Datos para gráficos ───

        // Conversiones por mes (últimos 6 meses)
        $porMes = $ordenes->groupBy(function ($o) {
            return $o->fecha_inicio_conversion ? $o->fecha_inicio_conversion->format('Y-m') : 'Sin fecha';
        })->map(fn ($group) => [
            'total' => $group->count(),
            'completadas' => $group->where('estado', 'conversion_completada')->count(),
            'en_proceso' => $group->where('estado', 'en_conversion')->count(),
        ])->sortKeys()->toArray();

        // Kits más utilizados
        $kitsPorTipo = $ordenes->flatMap(function ($o) {
            return $o->items->filter(fn ($item) => $item->kit_padre_id === null && $item->producto->categoria->es_kit ?? false)
                ->map(fn ($kit) => $kit->producto->nombre);
        })->countBy()->sortDesc()->toArray();

        // Piezas más reportadas (que no calzan)
        $reportesPorPieza = \App\Models\ReportePiezaNoEncajada::whereIn('estado', ['resuelto', 'kit_abierto'])
            ->with('itemNoEncajado.producto')
            ->get()
            ->groupBy(fn ($r) => $r->itemNoEncajado->producto->nombre ?? 'Desconocido')
            ->map(fn ($g) => $g->count())
            ->sortDesc()
            ->take(10)
            ->toArray();

        // Detalle por orden (para la tabla)
        $detalleOrdenes = $ordenes->map(function ($o) {
            $kitPadre = $o->items->first(fn ($i) => $i->kit_padre_id === null && ($i->producto->categoria->es_kit ?? false));
            $hijos = $o->items->where('kit_padre_id', '!=', null);
            $instalados = $hijos->where('estado', 'instalado');
            $reportados = $o->reportesPendientes ?? collect();

            return [
                'orden' => $o,
                'cliente' => $o->cliente->nombre . ' ' . $o->cliente->apellido,
                'placa' => $o->vehiculo->placa ?? 'N/A',
                'vehiculo' => $o->vehiculo->marca . ' ' . $o->vehiculo->modelo ?? 'N/A',
                'tecnico' => $o->tecnico->name ?? 'N/A',
                'kit_nombre' => $kitPadre?->producto->nombre ?? 'N/A',
                'kit_generacion' => $kitPadre?->producto->atributos['generacion'] ?? '',
                'total_componentes' => $hijos->count(),
                'instalados' => $instalados->count(),
                // Items seriales instalados: Vaporizador, Tanque, etc.
                'items_serializados' => $instalados
                    ->filter(fn ($i) => !str_starts_with($i->serie ?? '', 'CANT-') && !empty($i->serie))
                    ->map(fn ($i) => [
                        'nombre' => $i->producto->nombre,
                        'serie' => $i->serie,
                    ])->values()->toArray(),
                'reportes' => $o->reportesPendientes()->count(),
                'fecha_inicio' => $o->fecha_inicio_conversion?->format('d/m/Y H:i'),
                'fecha_fin' => $o->fecha_fin_conversion?->format('d/m/Y H:i'),
                'duracion_horas' => $o->fecha_inicio_conversion && $o->fecha_fin_conversion
                    ? round($o->fecha_inicio_conversion->diffInHours($o->fecha_fin_conversion), 1)
                    : null,
            ];
        });

        return view('livewire.conversiones.reporte', [
            'ordenes' => $ordenes,
            'detalleOrdenes' => $detalleOrdenes,
            // KPIs
            'totalConversiones' => $totalConversiones,
            'completadas' => $completadas,
            'enProceso' => $enProceso,
            'itemsInstalados' => $itemsInstalados,
            'piezasCantidad' => $piezasCantidad,
            'stockPiezasSueltas' => $stockPiezasSueltas,
            'kitsEnStock' => $kitsEnStock,
            // Charts
            'porMes' => $porMes,
            'kitsPorTipo' => $kitsPorTipo,
            'reportesPorPieza' => $reportesPorPieza,
            // Filters
            'sedes' => Sede::activas()->orderBy('id')->get(),
        ]);
    }
}
