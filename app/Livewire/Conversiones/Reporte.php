<?php

namespace App\Livewire\Conversiones;

use App\Models\ItemSerializado;
use App\Models\ProductoStockSede;
use App\Models\ServiceOrder;
use App\Models\Sede;
use Livewire\Component;

class Reporte extends Component
{
    /** Sede por defecto del reporte: Callao (también al limpiar filtros). */
    public const SEDE_DEFAULT = 1;

    /** Etiquetas canónicas de los estados del pipeline de conversión. */
    private const ESTADO_LABELS = [
        'en_evaluacion' => 'En evaluación',
        'aprobado_conversion' => 'Aprobadas',
        'en_conversion' => 'En proceso',
        'conversion_completada' => 'Completadas',
        'listo_para_entrega' => 'Listas entrega',
        'entregado' => 'Entregadas',
    ];

    /**
     * Color hex de cada estado (gráfico de dona).
     * Debe coincidir con los badges de reporte-tabla.blade.php.
     */
    private const ESTADO_COLORS = [
        'en_evaluacion' => '#64748b',         // slate
        'aprobado_conversion' => '#6366f1',   // indigo
        'en_conversion' => '#f59e0b',         // amber
        'conversion_completada' => '#2563eb', // brand (azul)
        'listo_para_entrega' => '#06b6d4',    // cyan
        'entregado' => '#10b981',             // emerald
    ];

    /** Orden de las celdas del desglose por tipo de conversión (GNV / GLP). */
    private const ORDEN_TIPO_CONVERSION = [
        'GNV' => 0,
        'GLP' => 1,
    ];

    // Sin tipo int: el <select> de "Todas" envía '' y rompía ?int.
    public $filtroSede = self::SEDE_DEFAULT;
    public string $filtroEstado = 'todos';
    public ?string $filtroFechaDesde = null;
    public ?string $filtroFechaHasta = null;
    public ?int $filtroTecnico = null;  // NUEVO: filtro técnico opcional

    public function updatedFiltroSede($value): void
    {
        $this->filtroSede = ($value === '' || $value === null) ? null : (int) $value;

        // El cambio de sede puede dejar un estado sin órdenes: vuelve a "todos".
        if ($this->filtroEstado !== 'todos' && ! array_key_exists($this->filtroEstado, $this->estadosDisponibles())) {
            $this->filtroEstado = 'todos';
        }
    }

    public function updatedFiltroEstado($value): void
    {
        $this->filtroEstado = (string) ($value ?: 'todos');
    }

    public function limpiarFiltros(): void
    {
        $this->filtroSede = self::SEDE_DEFAULT;
        $this->filtroEstado = 'todos';
        $this->filtroFechaDesde = null;
        $this->filtroFechaHasta = null;
    }

    public function filtroBadge(): string
    {
        $sede = $this->filtroSede
            ? (Sede::find($this->filtroSede)?->nombre ?? 'Sede')
            : 'Todas las sedes';

        $estado = self::ESTADO_LABELS[$this->filtroEstado] ?? 'Todos estados';

        $fechas = [];
        if ($this->filtroFechaDesde) {
            $fechas[] = 'desde ' . $this->filtroFechaDesde;
        }
        if ($this->filtroFechaHasta) {
            $fechas[] = 'hasta ' . $this->filtroFechaHasta;
        }

        return $sede . ' · ' . $estado . ($fechas ? ' · ' . implode(' / ', $fechas) : '');
    }

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
            'tecnico_id' => $this->filtroTecnico,
        ]);
    }

    /** Mismos estados de /ordenes + pipeline de conversión (compartidos con PDF y Excel). */
    private function estadosConversion(): array
    {
        return ServiceOrder::ESTADOS_CONVERSION;
    }

    /**
     * Estados del pipeline que tienen órdenes reales (respeta el filtro de sede).
     * Evita ofrecer estados sin datos en el <select>.
     */
    public function estadosDisponibles(): array
    {
        $existentes = ServiceOrder::query()
            ->tipoConversion()
            ->whereIn('estado', $this->estadosConversion())
            ->when($this->filtroSede, fn ($q) => $q->whereHas('items', fn ($iq) => $iq->where('sede_id', $this->filtroSede)))
            ->distinct()
            ->pluck('estado')
            ->all();

        return collect($this->estadosConversion())
            ->filter(fn ($e) => in_array($e, $existentes, true))
            ->mapWithKeys(fn ($e) => [$e => self::ESTADO_LABELS[$e]])
            ->all();
    }

    /** Rendimiento por técnico (órdenes asignadas / completadas / items / duración / reportes). */
    public function getRendimientoTecnicosProperty()
    {
        $tecnicoId = $this->filtroTecnico;

        // Órdenes base con los filtros aplicados (sede, estado, fecha)
        $query = ServiceOrder::with(['tecnico', 'items.producto.categoria'])
            ->whereIn('estado', ['en_conversion', 'conversion_completada']);

        if ($tecnicoId) {
            $query->where('tecnico_id', $tecnicoId);
        }

        // Aplicar sede y estado/fecha igual que el query principal
        $sedeId = $this->filtroSede;
        if ($sedeId) {
            $query->whereHas('items', fn ($q) => $q->where('sede_id', $sedeId));
        }

        if ($this->filtroEstado !== 'todos') {
            $query->where('estado', $this->filtroEstado);
        }

        if ($this->filtroFechaDesde) {
            $query->where(function ($q) {
                $q->whereDate('created_at', '>=', $this->filtroFechaDesde)
                    ->orWhereDate('fecha_inicio_conversion', '>=', $this->filtroFechaDesde);
            });
        }

        if ($this->filtroFechaHasta) {
            $query->where(function ($q) {
                $q->whereDate('created_at', '<=', $this->filtroFechaHasta)
                    ->orWhereDate('fecha_inicio_conversion', '<=', $this->filtroFechaHasta);
            });
        }

        $ordenes = $query->orderByDesc('created_at')->get();
        $ordenIds = $ordenes->pluck('id');

        // Traer todos los técnicos únicos que aparecen en las órdenes filtradas
        $tecnicosIds = $ordenes->pluck('tecnico_id')->unique()->values()->toArray();
        $tecnicos = User::whereIn('id', $tecnicosIds)->get();

        $ranking = [];
        foreach ($tecnicos as $t) {
            $tecnicoOrdenes = $ordenes->where('tecnico_id', $t->id);
            $asignadas = $tecnicoOrdenes->count();
            $completadas = $tecnicoOrdenes->where('estado', 'conversion_completada')->count();
            $pendientePct = $asignadas > 0 ? round(($completadas / $asignadas) * 100, 1) : 0;

            // Items instalados en las órdenes de este técnico (mismo criterio que el reporte)
            $itemsInstalados = ItemSerializado::where('estado', 'instalado')
                ->where(function ($q) use ($ordenIds) {
                    $q->whereIn('service_order_id', $ordenIds)
                        ->orWhereHas('kitPadre', fn ($qq) => $qq->whereIn('service_order_id', $ordenIds));
                })
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->whereIn('service_order_id', $ordenIds)
                ->count();

            // Duración promedio solo de las completadas
            $duracionProm = $tecnicoOrdenes->whereNotNull('fecha_inicio_conversion')
                ->whereNotNull('fecha_fin_conversion')
                ->avg(fn ($o) => $o->fecha_inicio_conversion->diffInHours($o->fecha_fin_conversion));
            $duracionProm = round($duracionProm ?? 0, 1);

            // Reportes pendientes en las órdenes de este técnico (subquery simple)
            $reportesPend = ServiceOrder::whereIn('id', $ordenIds)
                ->where('tecnico_id', $t->id)
                ->sum(DB::raw('(SELECT COUNT(*) FROM reportes_pendientes WHERE servicio_order_id = service_orders.id)'));

            $ranking[] = [
                'tecnico' => $t,
                'asignadas' => $asignadas,
                'completadas' => $completadas,
                '%' => $pendientePct,
                'items_instalados' => $itemsInstalados,
                'duracion_prom' => $duracionProm,
                'reportes_p' => $reportesPend,
            ];
        }

        // Ordenar por completadas descendente y tomar top 5
        usort($ranking, fn ($a, $b) => $b['completadas'] <=> $a['completadas']);
        $ranking = array_slice($ranking, 0, 5);

        return collect($ranking);
    }

    /**
     * Tipo de conversión a partir del servicio contratado: "Conversión GNV" → GNV.
     * Alimenta los sub-datos de la tarjeta "Kits instalados".
     */
    private static function tipoConversionLabel(?string $servicio): string
    {
        if ($servicio === null || trim($servicio) === '') {
            return 'Sin tipo';
        }
        if (stripos($servicio, 'GNV') !== false) {
            return 'GNV';
        }
        if (stripos($servicio, 'GLP') !== false) {
            return 'GLP';
        }

        return $servicio;
    }

    public function render()
    {
        $sedeId = $this->filtroSede;
        $sedes = Sede::activas()->orderBy('id')->get();
        $filtroBadge = $this->filtroBadge();

        // ─── Órdenes: misma lógica que /ordenes (tipoConversion) ───
        $query = ServiceOrder::with([
            'cliente',
            'vehiculo',
            'service',
            'tecnico',
            'comprobante',
            'items.producto.categoria',
            'items.kitPadre.producto',
            'items.sede',
        ])->tipoConversion()
            ->whereIn('estado', $this->estadosConversion());

        if ($sedeId) {
            $query->whereHas('items', fn ($q) => $q->where('sede_id', $sedeId));
        }

        if ($this->filtroEstado !== 'todos') {
            $query->where('estado', $this->filtroEstado);
        }

        // Mismo criterio de fecha que /ordenes: created_at; fallback a inicio de conversión.
        if ($this->filtroFechaDesde) {
            $query->where(function ($q) {
                $q->whereDate('created_at', '>=', $this->filtroFechaDesde)
                    ->orWhereDate('fecha_inicio_conversion', '>=', $this->filtroFechaDesde);
            });
        }

        if ($this->filtroFechaHasta) {
            $query->where(function ($q) {
                $q->whereDate('created_at', '<=', $this->filtroFechaHasta)
                    ->orWhereDate('fecha_inicio_conversion', '<=', $this->filtroFechaHasta);
            });
        }

        $ordenes = $query->orderByDesc('created_at')->get();
        $ordenIds = $ordenes->pluck('id');

        // ─── KPIs de conversión (dependen de filtros) ───
        $totalConversiones = $ordenes->count();
        $completadas = $ordenes->where('estado', 'conversion_completada')->count();
        $enProceso = $ordenes->where('estado', 'en_conversion')->count();
        $tasaCompletado = $totalConversiones > 0 ? round(($completadas / $totalConversiones) * 100, 1) : 0;

        $duracionPromedio = $ordenes
            ->filter(fn ($o) => $o->fecha_inicio_conversion && $o->fecha_fin_conversion)
            ->avg(fn ($o) => $o->fecha_inicio_conversion->diffInHours($o->fecha_fin_conversion));
        $duracionPromedio = round($duracionPromedio ?? 0, 1);

        // ─── Instalados / despachados en órdenes filtradas ───
        // Incluye hijos de kit aunque service_order_id sea NULL (historial).
        // Sede: filtra por sede del item O por kit padre en esa sede.
        $instaladosQuery = ItemSerializado::where('estado', 'instalado')
            ->where(function ($q) use ($ordenIds) {
                $q->whereIn('service_order_id', $ordenIds)
                    ->orWhereHas('kitPadre', fn ($qq) => $qq->whereIn('service_order_id', $ordenIds));
            });

        if ($sedeId) {
            $instaladosQuery->where(function ($q) use ($sedeId) {
                $q->where('sede_id', $sedeId)
                    ->orWhereHas('kitPadre', fn ($qq) => $qq->where('sede_id', $sedeId));
            });
        }

        $itemsInstaladosCollection = $instaladosQuery->get();
        $itemsInstalados = $itemsInstaladosCollection->count();
        $instaladosCantidad = $itemsInstaladosCollection
            ->filter(fn ($i) => ($i->atributos['tipo'] ?? '') === 'cantidad')
            ->count();
        $instaladosSerializados = $itemsInstaladosCollection
            ->filter(fn ($i) => ($i->atributos['tipo'] ?? '') !== 'cantidad')
            ->count();

        // ─── Kits instalados en las órdenes filtradas ───
        // Un kit por orden: es el padre (kit_padre_id NULL) ya instalado o consumido.
        // Sede y fechas ya vienen aplicadas en $ordenes (=> $ordenIds).
        $kitsInstaladosRows = ItemSerializado::whereNull('kit_padre_id')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->whereIn('estado', ['instalado', 'consumido'])
            ->whereIn('service_order_id', $ordenIds)
            ->with('serviceOrder.service')
            ->get();

        $kitsInstalados = $kitsInstaladosRows->count();

        // Sub-datos: esos kits agrupados por el tipo de conversión (GNV / GLP).
        // Si sólo hay un tipo se muestra una sola celda; si hay varios, se distribuyen.
        $instaladosPorCombustible = $kitsInstaladosRows
            ->countBy(fn ($k) => self::tipoConversionLabel($k->serviceOrder?->service?->nombre))
            ->toArray();
        uksort(
            $instaladosPorCombustible,
            fn ($a, $b) => (self::ORDEN_TIPO_CONVERSION[$a] ?? 2) <=> (self::ORDEN_TIPO_CONVERSION[$b] ?? 2)
                ?: strcmp($a, $b)
        );

        // ─── Balance de almacén (misma lógica que /almacen/productos) ───
        // Solo el filtro de sede aplica al stock (estado/fecha no inventan stock).
        $kitsDisponibles = ItemSerializado::kitDisponible()
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $kitsSellados = ItemSerializado::where('estado', 'en_stock')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $kitsCompletados = ItemSerializado::where('estado', 'completado')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        // Piezas sueltas con serie (productos: sueltosSerializados).
        $sueltosSerializados = ItemSerializado::whereHas(
            'producto.categoria',
            fn ($q) => $q->where('es_kit', false)->where('es_serializado', true)
        )
            ->whereNull('kit_padre_id')
            ->where('estado', 'en_stock')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        // Piezas sueltas por cantidad REAL (ProductoStockSede − componentes en kits de esa sede).
        $sueltosCantidadRows = ProductoStockSede::with('producto.categoria')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->where('cantidad', '>', 0)
            ->get()
            ->map(function ($stock) use ($sedeId) {
                $enKits = ItemSerializado::where('producto_id', $stock->producto_id)
                    ->whereNotNull('kit_padre_id')
                    ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                    ->where('sede_id', $stock->sede_id)
                    ->count();
                $stock->cantidad_suelta_real = max(0, $stock->cantidad - $enKits);
                return $stock;
            })
            ->filter(fn ($s) => $s->cantidad_suelta_real > 0);

        $sueltosCantidadTotal = (int) $sueltosCantidadRows->sum('cantidad_suelta_real');
        $piezasSueltas = $sueltosSerializados + $sueltosCantidadTotal;

        // ─── Charts ───

        // 1. Conversiones por mes (usa created_at como /ordenes)
        $porMes = $ordenes->groupBy(
            fn ($o) => $o->created_at?->format('Y-m') ?? ($o->fecha_inicio_conversion?->format('Y-m') ?? 'Sin fecha')
        )->map(fn ($group) => [
            'total' => $group->count(),
            'completadas' => $group->where('estado', 'conversion_completada')->count(),
            'en_proceso' => $group->where('estado', 'en_conversion')->count(),
            'otras' => $group->count()
                - $group->where('estado', 'conversion_completada')->count()
                - $group->where('estado', 'en_conversion')->count(),
        ])->sortKeys()->toArray();

        // 2. Distribución por estado
        $porEstado = $ordenes->countBy('estado')->toArray();
        $porEstado = collect($porEstado)
            ->mapWithKeys(fn ($c, $k) => [self::ESTADO_LABELS[$k] ?? $k => $c])
            ->toArray();

        // 3. Estado de kits en almacén: sellados / completados / asignados a clientes
        // Asignados = consumido o con orden/vehículo (no son "disponibles" en stock).
        $kitsEstadoQuery = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId));
        $kitsAll = $kitsEstadoQuery->get();
        $kitsSelladosChart = $kitsAll->where('estado', 'en_stock')->count();
        $kitsCompletadosChart = $kitsAll->where('estado', 'completado')->count();
        $kitsAsignadosChart = $kitsAll
            ->filter(fn ($k) => $k->estado === 'consumido'
                || $k->estado === 'asignado'
                || $k->estado === 'instalado'
                || $k->service_order_id
                || $k->vehiculo_instalado_id)
            ->reject(fn ($k) => in_array($k->estado, ['en_stock', 'completado'], true))
            ->count();
        // Kits abiertos/incompletos que no entran en los 3 anteriores
        $kitsOtrosChart = max(0, $kitsAll->count() - $kitsSelladosChart - $kitsCompletadosChart - $kitsAsignadosChart);
        $kitsTotalChart = $kitsAll->count();

        $kitsEstadoChart = array_filter([
            'Sellados' => $kitsSelladosChart,
            'Completados' => $kitsCompletadosChart,
            'Asignados a clientes' => $kitsAsignadosChart,
            'En armado' => $kitsOtrosChart,
        ], fn ($v) => $v > 0);

        // Kits más utilizados en conversiones filtradas (tabla/detail, no chart principal)
        $kitsPorTipo = $ordenes->flatMap(function ($o) {
            return $o->items
                ->filter(fn ($item) => $item->kit_padre_id === null && ($item->producto->categoria->es_kit ?? false))
                ->map(fn ($kit) => $kit->producto->nombre);
        })->countBy()->sortDesc()->toArray();

        // 4. Componentes más instalados (productos de items instalados en órdenes filtradas)
        $componentesInstalados = $itemsInstaladosCollection
            ->groupBy(fn ($i) => $i->producto->nombre ?? 'Sin producto')
            ->map->count()
            ->sortDesc()
            ->take(12)
            ->toArray();

        // 5. Balance de almacén (kits disponibles vs sueltos) por sede o global
        $stockPorSede = $sedes->map(function ($s) use ($sedeId) {
            if ($sedeId && $s->id !== $sedeId) {
                return null;
            }
            $kits = ItemSerializado::kitDisponible()
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
                ->where('sede_id', $s->id)
                ->count();
            $serie = ItemSerializado::whereHas(
                'producto.categoria',
                fn ($q) => $q->where('es_kit', false)->where('es_serializado', true)
            )
                ->whereNull('kit_padre_id')
                ->where('estado', 'en_stock')
                ->where('sede_id', $s->id)
                ->count();
            $cantidad = (int) ProductoStockSede::with('producto.categoria')
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
                ->where('sede_id', $s->id)
                ->where('cantidad', '>', 0)
                ->get()
                ->map(function ($stock) {
                    $enKits = ItemSerializado::where('producto_id', $stock->producto_id)
                        ->whereNotNull('kit_padre_id')
                        ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                        ->where('sede_id', $stock->sede_id)
                        ->count();
                    return max(0, $stock->cantidad - $enKits);
                })
                ->sum();

            return [
                'sede' => $s->nombre,
                'kits' => $kits,
                'serie' => $serie,
                'cantidad' => $cantidad,
            ];
        })->filter()->values()->toArray();

        // 6. Despachados/instalados: con serie vs por cantidad (reemplaza "no calzan")
        $despachadosPorTipo = [
            'Con serie' => $instaladosSerializados,
            'Por cantidad' => $instaladosCantidad,
        ];

        // Detalle por orden (tabla)
        $detalleOrdenes = $ordenes->map(function ($o) {
            $kitPadre = $o->items->first(fn ($i) => $i->kit_padre_id === null && ($i->producto->categoria->es_kit ?? false));
            $hijos = $o->items->where('kit_padre_id', '!=', null);
            $instalados = $hijos->where('estado', 'instalado');

            return [
                'orden' => $o,
                'cliente' => trim(($o->cliente->nombre ?? '') . ' ' . ($o->cliente->apellido ?? '')) ?: '—',
                'placa' => $o->vehiculo->placa ?? 'N/A',
                'vehiculo' => trim(($o->vehiculo->marca ?? '') . ' ' . ($o->vehiculo->modelo ?? '')) ?: 'N/A',
                'tecnico' => $o->tecnico->name ?? 'N/A',
                // service_orders no tiene sede_id: la sede vive en los items serializados.
                'sede' => $o->items->first(fn ($i) => $i->sede_id)?->sede?->nombre,
                'precio' => $o->precio_final ?: null,
                'folio' => $o->comprobante?->folio,
                'kit_nombre' => $kitPadre?->producto->nombre ?? 'N/A',
                'kit_generacion' => $kitPadre?->producto->atributos['generacion'] ?? '',
                'total_componentes' => $hijos->count() ?: $o->items->count(),
                'instalados' => $instalados->count() ?: $o->items->where('estado', 'instalado')->count(),
                'items_serializados' => $o->items
                    ->filter(fn ($i) => $i->estado === 'instalado'
                        && ($i->atributos['tipo'] ?? '') !== 'cantidad'
                        && !empty($i->serie))
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
                'duracion_min' => $o->fecha_inicio_conversion && $o->fecha_fin_conversion
                    ? (int) round($o->fecha_inicio_conversion->diffInMinutes($o->fecha_fin_conversion))
                    : null,
                'estado' => $o->estado,
            ];
        });

        $charts = [
            'labelsMes' => array_keys($porMes),
            'dataMesCompletadas' => array_column($porMes, 'completadas'),
            'dataMesProceso' => array_column($porMes, 'en_proceso'),
            'dataMesOtras' => array_column($porMes, 'otras'),
            'labelsEstado' => array_keys($porEstado),
            'dataEstado' => array_values($porEstado),
            // Color por estado, en el MISMO orden que labelsEstado (evita colores posicionales malos)
            'coloresEstado' => array_map(
                fn ($etiqueta) => self::ESTADO_COLORS[array_search($etiqueta, self::ESTADO_LABELS, true)] ?? '#94a3b8',
                array_keys($porEstado)
            ),
            'labelsKits' => array_keys($kitsEstadoChart),
            'dataKits' => array_values($kitsEstadoChart),
            'kitsTotal' => $kitsTotalChart,
            'kitsSelladosChart' => $kitsSelladosChart,
            'kitsCompletadosChart' => $kitsCompletadosChart,
            'kitsAsignadosChart' => $kitsAsignadosChart,
            'labelsComponentes' => array_keys($componentesInstalados),
            'dataComponentes' => array_values($componentesInstalados),
            'labelsStockSedes' => array_column($stockPorSede, 'sede'),
            'dataStockKits' => array_column($stockPorSede, 'kits'),
            'dataStockSerie' => array_column($stockPorSede, 'serie'),
            'dataStockCantidad' => array_column($stockPorSede, 'cantidad'),
            'labelsDespachados' => array_keys($despachadosPorTipo),
            'dataDespachados' => array_values($despachadosPorTipo),
            'filtroBadge' => $filtroBadge,
            'exportPdfUrl' => $this->exportPdfUrl(),
            'exportExcelUrl' => $this->exportExcelUrl(),
        ];

        return view('livewire.conversiones.reporte', [
            'ordenes' => $ordenes,
            'detalleOrdenes' => $detalleOrdenes,
            'totalConversiones' => $totalConversiones,
            'completadas' => $completadas,
            'enProceso' => $enProceso,
            'tasaCompletado' => $tasaCompletado,
            'duracionPromedio' => $duracionPromedio,
            'itemsInstalados' => $itemsInstalados,
            'instaladosCantidad' => $instaladosCantidad,
            'instaladosSerializados' => $instaladosSerializados,
            'kitsInstalados' => $kitsInstalados,
            'instaladosPorCombustible' => $instaladosPorCombustible,
            'kitsDisponibles' => $kitsDisponibles,
            'kitsSellados' => $kitsSellados,
            'kitsCompletados' => $kitsCompletados,
            'sueltosSerializados' => $sueltosSerializados,
            'sueltosCantidadTotal' => $sueltosCantidadTotal,
            'piezasSueltas' => $piezasSueltas,
            'porMes' => $porMes,
            'porEstado' => $porEstado,
            'kitsPorTipo' => $kitsPorTipo,
            'kitsEstadoChart' => $kitsEstadoChart,
            'kitsTotal' => $kitsTotalChart,
            'kitsSelladosChart' => $kitsSelladosChart,
            'kitsCompletadosChart' => $kitsCompletadosChart,
            'kitsAsignadosChart' => $kitsAsignadosChart,
            'componentesInstalados' => $componentesInstalados,
            'stockPorSede' => $stockPorSede,
            'despachadosPorTipo' => $despachadosPorTipo,
            'sedes' => $sedes,
            'estadosDisponibles' => $this->estadosDisponibles(),
            'filtroBadge' => $filtroBadge,
            'charts' => $charts,
        ]);
    }
}
