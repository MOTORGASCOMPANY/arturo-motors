<div>
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    @pushOnce('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endPushOnce

        <div class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-sm -mx-4 px-4 pt-2 pb-3 space-y-3 border-b border-gray-200/70">

            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex items-center gap-3">
                    <span class="hidden sm:flex w-11 h-11 rounded-xl bg-indigo-50 items-center justify-center shrink-0">
                        <i class="fas fa-store text-indigo-600"></i>
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                            Almacén
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">Inventario y productos del almacén</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('almacen.reporte') }}"
                        class="bg-white hover:bg-indigo-50 text-indigo-600 border border-indigo-200 text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors flex items-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        <i class="fas fa-chart-pie text-xs"></i>
                        Reporte
                    </a>
                    <a href="{{ route('almacen.recepciones.crear') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm shadow-indigo-600/10 transition-colors flex items-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        <i class="fas fa-plus text-xs"></i>
                        Agregar producto
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-2.5 flex flex-wrap items-center gap-2.5">

                <nav class="flex gap-1 bg-gray-100 rounded-lg p-1" x-data aria-label="Vistas del almacén">
                    @foreach ([
                        'inventario' => ['icon' => 'fa-boxes-stacked', 'label' => 'Inventario'],
                        'catalogo'   => ['icon' => 'fa-list',         'label' => 'Catálogo'],
                    ] as $key => $tab)
                        <button type="button" wire:click="$set('vistaActual', '{{ $key }}')"
                            class="px-3.5 h-9 text-sm font-semibold rounded-md transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            :class="$wire.vistaActual === '{{ $key }}' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            <i class="fas {{ $tab['icon'] }} mr-1.5"></i>{{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>

                @if ($vistaActual === 'inventario')
                    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto sm:ml-auto">
                        <select wire:model.live="filtroSedeId" aria-label="Filtrar por sede"
                            class="h-10 text-sm border border-gray-200 rounded-lg px-3 bg-white transition-colors hover:border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-52">
                            <option value="">Todas las sedes</option>
                            @foreach ($sedes as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                        <label class="flex items-center h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 w-full sm:w-64 transition-colors hover:border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                            <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                                type="text" wire:model.live.debounce.400ms="busquedaInventario"
                                placeholder="Buscar por nombre...">
                        </label>
                    </div>

                @elseif ($vistaActual === 'catalogo')
                    <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto lg:ml-auto">
                        <label class="flex items-center h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 w-full sm:w-64 transition-colors hover:border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                            <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                                type="text" wire:model.live.debounce.400ms="buscar"
                                placeholder="Buscar por nombre o código...">
                        </label>
                        <select wire:model.live="filterStock" aria-label="Filtrar por stock"
                            class="h-10 text-sm border border-gray-200 rounded-lg px-3 bg-white transition-colors hover:border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-40">
                            <option value="todos">Todo el stock</option>
                            <option value="bajo">Stock bajo</option>
                            <option value="sin">Sin stock</option>
                        </select>
                        <input type="text" wire:model.live="filterProveedor" placeholder="Proveedor..."
                            aria-label="Filtrar por proveedor"
                            class="h-10 text-sm border border-gray-200 rounded-lg px-3 bg-white transition-colors hover:border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-44">
                        <button type="button" wire:click="resetFilters"
                            class="h-10 px-3.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if ($vistaActual === 'inventario')
            {{-- ═══ NIVEL 0: DASHBOARD ═══ --}}
            @if ($nivelInventario === 'dashboard')
                @php
                    $inv  = $resumenInventario ?? [];
                    $c    = $inv['conteos'] ?? [];
                    $sellados     = $inv['kitsSellados']       ?? collect();
                    $incompletos  = $inv['kitsIncompletos']    ?? collect();
                    $completados  = $inv['kitsCompletados']    ?? collect();
                    $consumidos   = $inv['kitsConsumidos']     ?? collect();
                    $sueltosS     = $inv['sueltosSerializados']?? collect();
                    $sueltosC     = $inv['sueltosCantidad']    ?? collect();

                    $stats = [
                        ['tipo' => 'sellado',              'label' => 'Sellados',             'hint' => 'En stock',         'valor' => $c['sellados'] ?? 0,             'icono' => 'fa-box',          'color' => 'text-amber-600',  'icono_color' => 'text-amber-500',  'bg' => 'bg-amber-50',  'hover' => 'hover:bg-amber-50'],
                        ['tipo' => 'incompleto',           'label' => 'Incompletos',          'hint' => 'Abiertos',         'valor' => $c['incompletos'] ?? 0,          'icono' => 'fa-box-open',     'color' => 'text-orange-600', 'icono_color' => 'text-orange-500', 'bg' => 'bg-orange-50', 'hover' => 'hover:bg-orange-50'],
                        ['tipo' => 'completado',           'label' => 'Completados',          'hint' => 'Kits completados', 'valor' => $c['completados'] ?? 0,          'icono' => 'fa-check-circle', 'color' => 'text-purple-600', 'icono_color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'hover' => 'hover:bg-purple-50'],
                        ['tipo' => 'consumido',            'label' => 'Consumidos',           'hint' => 'En órdenes',       'valor' => $c['consumidos'] ?? 0,           'icono' => 'fa-fire',         'color' => 'text-red-600',    'icono_color' => 'text-red-500',    'bg' => 'bg-red-50',    'hover' => 'hover:bg-red-50'],
                        ['tipo' => 'sueltosSerializados',  'label' => 'Sueltos con serie',    'hint' => 'Tipos de producto','valor' => $c['sueltosSerializados'] ?? 0,  'icono' => 'fa-barcode',      'color' => 'text-green-600',  'icono_color' => 'text-green-500',  'bg' => 'bg-green-50',  'hover' => 'hover:bg-green-50'],
                        ['tipo' => 'sueltosCantidad',      'label' => 'Sueltos por cantidad', 'hint' => 'Tipos de producto','valor' => $c['sueltosCantidadTipos'] ?? 0, 'icono' => 'fa-cubes',        'color' => 'text-indigo-600', 'icono_color' => 'text-indigo-500', 'bg' => 'bg-indigo-50', 'hover' => 'hover:bg-indigo-50'],
                    ];
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-px bg-gray-200 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    @foreach ($stats as $st)
                        <button type="button" wire:click="verListadoInventario('{{ $st['tipo'] }}')"
                            class="bg-white p-4 text-left transition-colors duration-150 {{ $st['hover'] }} focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg {{ $st['bg'] }} flex items-center justify-center shrink-0">
                                    <i class="fas {{ $st['icono'] }} {{ $st['icono_color'] }} text-sm"></i>
                                </span>
                                <span class="text-sm font-medium text-gray-600">{{ $st['label'] }}</span>
                            </div>
                            <p class="text-3xl font-black {{ $st['color'] }} mt-3 leading-none tabular-nums">{{ $st['valor'] }}</p>
                            <span class="text-xs text-gray-400 mt-1.5 block">{{ $st['hint'] }}</span>
                        </button>
                    @endforeach
                </div>

                @php
                    // Aplana todos los kits en tarjetas uniformes (incompletos primero).
                    $kitsCards = collect();
                    $estadosKitMeta = [
                        'incompleto'  => ['items' => $incompletos,  'chip' => 'Incompleto',  'chipClass' => 'bg-orange-100 text-orange-700', 'icon' => 'fa-box-open',     'iconBg' => 'bg-orange-50', 'iconColor' => 'text-orange-500', 'accion' => 'completar'],
                        'sellado'     => ['items' => $sellados,     'chip' => 'Sellado',     'chipClass' => 'bg-amber-100 text-amber-700',   'icon' => 'fa-box',          'iconBg' => 'bg-amber-50',  'iconColor' => 'text-amber-500',  'accion' => 'detalle'],
                        'completado'  => ['items' => $completados,  'chip' => 'Completado',  'chipClass' => 'bg-purple-100 text-purple-700', 'icon' => 'fa-check-circle', 'iconBg' => 'bg-purple-50', 'iconColor' => 'text-purple-500', 'accion' => 'detalle'],
                        'consumido'   => ['items' => $consumidos,   'chip' => 'Consumido',   'chipClass' => 'bg-red-100 text-red-700',       'icon' => 'fa-fire',         'iconBg' => 'bg-red-50',    'iconColor' => 'text-red-500',    'accion' => 'detalle'],
                    ];
                    foreach ($estadosKitMeta as $meta) {
                        foreach ($meta['items'] as $productoId => $items) {
                            $prod = $items->first()?->producto;
                            foreach ($items as $kitItem) {
                                $kitsCards->push([
                                    'item'      => $kitItem,
                                    'nombre'    => $prod?->nombre ?? 'Producto',
                                    'chip'      => $meta['chip'],
                                    'chipClass' => $meta['chipClass'],
                                    'icon'      => $meta['icon'],
                                    'iconBg'    => $meta['iconBg'],
                                    'iconColor' => $meta['iconColor'],
                                    'accion'    => $meta['accion'],
                                ]);
                            }
                        }
                    }

                    // Piezas sueltas: serie y cantidad juntas en tarjetas planas.
                    $sueltosCards = collect();
                    foreach ($sueltosS as $productoId => $items) {
                        $prod = $items->first()?->producto;
                        foreach ($items as $item) {
                            $sueltosCards->push([
                                'nombre'     => $prod?->nombre ?? 'Producto',
                                'sede'       => $item->sede?->nombre ?? '—',
                                'badge'      => $item->serie ?? '—',
                                'badgeClass' => 'bg-gray-100 text-gray-700 font-mono',
                                'tipo'       => 'sueltosSerializados',
                                'icono'      => 'fa-barcode',
                            ]);
                        }
                    }
                    foreach ($sueltosC as $stock) {
                        $sueltosCards->push([
                            'nombre'     => $stock->producto?->nombre ?? 'Producto',
                            'sede'       => $stock->sede?->nombre ?? '—',
                            'badge'      => '×' . ($stock->cantidad_suelta_real ?? $stock->cantidad),
                            'badgeClass' => 'bg-indigo-100 text-indigo-700',
                            'tipo'       => 'sueltosCantidad',
                            'icono'      => 'fa-cubes',
                        ]);
                    }
                @endphp

                @if ($kitsCards->isNotEmpty())
                    <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <x-almacen.section-header icon="fa-boxes-stacked" color="indigo" title="Kits" :count="$kitsCards->count()" />
                        @foreach ($kitsCards->groupBy('chip') as $grupoLabel => $grupoCards)
                            <div class="px-3 {{ $loop->first ? 'pt-1' : 'pt-3' }} pb-1 border-t border-gray-100 {{ $loop->first ? 'border-t-0' : '' }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">{{ $grupoLabel }}</span>
                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full tabular-nums">{{ $grupoCards->count() }}</span>
                                    <div class="flex-1 h-px bg-gray-100"></div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2.5">
                                    @foreach ($grupoCards as $kc)
                                        @php $kitItem = $kc['item']; @endphp
                                        @php
                                            // Solo componentes serializados (con o sin serie).
                                            // Los de cantidad se ven en el modal de detalle.
                                            $seriesPreview = $kitItem->piezasEnKit
                                                ->filter(fn ($p) => (bool) ($p->producto?->categoria?->es_serializado))
                                                ->sortBy('id')
                                                ->values();
                                        @endphp
                                        @if ($kc['accion'] === 'completar')
                                            <div class="bg-white border border-orange-200 rounded-lg px-3 py-2.5">
                                                <div class="flex items-center gap-3">
                                                    <span class="w-8 h-8 rounded-lg {{ $kc['iconBg'] }} flex items-center justify-center shrink-0">
                                                        <i class="fas {{ $kc['icon'] }} {{ $kc['iconColor'] }} text-sm"></i>
                                                    </span>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $kc['nombre'] }}</p>
                                                        <p class="text-xs text-gray-500 truncate">#{{ $kitItem->id }} · {{ $kitItem->sede?->nombre ?? '—' }}</p>
                                                    </div>
                                                    <button type="button" wire:click="abrirCompletarKit({{ $kitItem->id }})"
                                                        class="shrink-0 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-1">
                                                        Completar
                                                    </button>
                                                </div>
                                                @if ($seriesPreview->isNotEmpty())
                                                    {{-- Sub-tarjetas por tipo de pieza: Computadora / Tanque / Reductor... --}}
                                                    @php
                                                        $porTipo = $seriesPreview->groupBy(
                                                            fn ($p) => $p->producto?->nombre ?? 'Sin nombre'
                                                        )->sortKeys();
                                                    @endphp
                                                    <div class="mt-2 grid grid-cols-1 gap-1.5">
                                                        @foreach ($porTipo as $tipoNombre => $piezasTipo)
                                                            <div class="border border-gray-100 bg-gray-50/80 rounded-md px-2 py-1.5">
                                                                <div class="flex items-center justify-between gap-2 mb-1">
                                                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide truncate">
                                                                        {{ $tipoNombre }}
                                                                    </span>
                                                                    <span class="px-1.5 py-0.5 bg-white border border-gray-200 text-gray-500 text-[10px] font-bold rounded-full tabular-nums shrink-0">
                                                                        {{ $piezasTipo->count() }}
                                                                    </span>
                                                                </div>
                                                                <div class="space-y-0.5">
                                                                    @foreach ($piezasTipo as $pieza)
                                                                        @php
                                                                            $esReemplazado = in_array($pieza->estado, ['defectuoso', 'devuelta_por_no_calzar'], true);
                                                                            $esNuevoInstalado = !$esReemplazado && $seriesPreview->contains(
                                                                                fn ($o) => $o->producto_id === $pieza->producto_id
                                                                                    && in_array($o->estado, ['defectuoso', 'devuelta_por_no_calzar'], true)
                                                                            );
                                                                        @endphp
                                                                        <div class="flex items-center justify-between gap-2 text-xs bg-white border border-gray-100 rounded px-1.5 py-1">
                                                                            <span class="truncate {{ $esReemplazado ? 'text-red-500 line-through' : ($esNuevoInstalado ? 'text-green-700 font-semibold' : 'text-gray-600') }}">
                                                                                {{ $pieza->producto?->nombre ?? '—' }}
                                                                            </span>
                                                                            <span class="font-mono shrink-0 {{ $esReemplazado ? 'text-red-400 line-through' : ($esNuevoInstalado ? 'text-green-600 font-bold' : ($pieza->serie ? 'text-gray-800 font-semibold' : 'text-gray-400')) }}">
                                                                                {{ $pieza->serie ?: 'sin serie' }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" wire:click="verDetalleKit({{ $kitItem->id }})"
                                                        class="mt-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                                        Ver detalle completo →
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <div class="bg-white border border-gray-200 rounded-lg px-3 py-2.5 transition hover:border-indigo-300">
                                                <button type="button" wire:click="verDetalleKit({{ $kitItem->id }})"
                                                    class="flex items-center gap-3 w-full text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                                    <span class="w-8 h-8 rounded-lg {{ $kc['iconBg'] }} flex items-center justify-center shrink-0">
                                                        <i class="fas {{ $kc['icon'] }} {{ $kc['iconColor'] }} text-sm"></i>
                                                    </span>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $kc['nombre'] }}</p>
                                                        <p class="text-xs text-gray-500 truncate">
                                                            #{{ $kitItem->id }} · {{ $kitItem->sede?->nombre ?? '—' }}
                                                            @if ($kitItem->serie)
                                                                · <span class="font-mono">{{ $kitItem->serie }}</span>
                                                            @endif
                                                            @if ($kitItem->serviceOrder)
                                                                · Ord #{{ $kitItem->service_order_id }}
                                                                @if ($kitItem->serviceOrder->cliente)
                                                                    · {{ $kitItem->serviceOrder->cliente->nombre . ' ' . $kitItem->serviceOrder->cliente->apellido }}
                                                                @endif
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0"></i>
                                                </button>
                                                @if ($seriesPreview->isNotEmpty())
                                                    {{-- Sub-tarjetas por tipo de pieza: Computadora / Tanque / Reductor... --}}
                                                    @php
                                                        $porTipo = $seriesPreview->groupBy(
                                                            fn ($p) => $p->producto?->nombre ?? 'Sin nombre'
                                                        )->sortKeys();
                                                    @endphp
                                                    <div class="mt-2 grid grid-cols-1 gap-1.5">
                                                        @foreach ($porTipo as $tipoNombre => $piezasTipo)
                                                            <div class="border border-gray-100 bg-gray-50/80 rounded-md px-2 py-1.5">
                                                                <div class="flex items-center justify-between gap-2 mb-1">
                                                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide truncate">
                                                                        {{ $tipoNombre }}
                                                                    </span>
                                                                    <span class="px-1.5 py-0.5 bg-white border border-gray-200 text-gray-500 text-[10px] font-bold rounded-full tabular-nums shrink-0">
                                                                        {{ $piezasTipo->count() }}
                                                                    </span>
                                                                </div>
                                                                <div class="space-y-0.5">
                                                                    @foreach ($piezasTipo as $pieza)
                                                                        @php
                                                                            $esReemplazado = in_array($pieza->estado, ['defectuoso', 'devuelta_por_no_calzar'], true);
                                                                            $esNuevoInstalado = !$esReemplazado && $seriesPreview->contains(
                                                                                fn ($o) => $o->producto_id === $pieza->producto_id
                                                                                    && in_array($o->estado, ['defectuoso', 'devuelta_por_no_calzar'], true)
                                                                            );
                                                                        @endphp
                                                                        <div class="flex items-center justify-between gap-2 text-xs bg-white border border-gray-100 rounded px-1.5 py-1">
                                                                            <span class="truncate {{ $esReemplazado ? 'text-red-500 line-through' : ($esNuevoInstalado ? 'text-green-700 font-semibold' : 'text-gray-600') }}">
                                                                                {{ $pieza->producto?->nombre ?? '—' }}
                                                                            </span>
                                                                            <span class="font-mono shrink-0 {{ $esReemplazado ? 'text-red-400 line-through' : ($esNuevoInstalado ? 'text-green-600 font-bold' : ($pieza->serie ? 'text-gray-800 font-semibold' : 'text-gray-400')) }}">
                                                                                {{ $pieza->serie ?: 'sin serie' }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" wire:click="verDetalleKit({{ $kitItem->id }})"
                                                        class="mt-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                                                        Ver detalle completo →
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif

                @if ($sueltosCards->isNotEmpty())
                    <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <x-almacen.section-header icon="fa-cubes" color="green" title="Piezas sueltas" :count="$sueltosCards->count()" />
                        @php $labelsSueltos = ['sueltosSerializados' => 'Con serie', 'sueltosCantidad' => 'Por cantidad']; @endphp
                        @foreach ($sueltosCards->groupBy('tipo') as $tipo => $grupo)
                            <div class="px-3 {{ $loop->first ? 'pt-1' : 'pt-3' }} pb-1 border-t border-gray-100 {{ $loop->first ? 'border-t-0' : '' }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">{{ $labelsSueltos[$tipo] ?? $tipo }}</span>
                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full tabular-nums">{{ $grupo->count() }}</span>
                                    <div class="flex-1 h-px bg-gray-100"></div>
                                </div>
                                {{-- Sub-tarjetas agrupadas por tipo de producto: Reductor, Tanque, Computadora... --}}
                                @php $porProducto = $grupo->groupBy('nombre')->sortKeys(); @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2.5">
                                    @foreach ($porProducto as $nombreProd => $piezasProd)
                                        <div class="bg-white border border-gray-200 rounded-lg px-3 py-2.5">
                                            <div class="flex items-center justify-between gap-2 mb-2 pb-1.5 border-b border-gray-100">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                                                        <i class="fas {{ $piezasProd->first()['icono'] ?? 'fa-cubes' }} text-green-500 text-xs"></i>
                                                    </span>
                                                    <span class="text-sm font-bold text-gray-800 truncate">{{ $nombreProd }}</span>
                                                </div>
                                                <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full tabular-nums shrink-0">
                                                    {{ $piezasProd->count() }}
                                                </span>
                                            </div>
                                            <div class="space-y-1">
                                                @foreach ($piezasProd as $sc)
                                                    <button type="button" wire:click="verListadoInventario('{{ $sc['tipo'] }}')"
                                                        class="w-full flex items-center justify-between gap-2 text-xs bg-gray-50 border border-gray-100 rounded px-2 py-1.5 text-left hover:border-green-300 hover:bg-green-50/60 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-green-500">
                                                        <span class="truncate text-gray-600">{{ $sc['sede'] }}</span>
                                                        <span class="shrink-0 px-1.5 py-0.5 text-[11px] font-bold rounded {{ $sc['badgeClass'] }}">{{ $sc['badge'] }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif

                @if ($sellados->isEmpty() && $incompletos->isEmpty() && $completados->isEmpty() && $consumidos->isEmpty() && $sueltosS->isEmpty() && $sueltosC->isEmpty())
                    <x-almacen.empty-state icon="fa-boxes-stacked" message="No hay inventario para mostrar" />
                @endif
            @endif

            @php
                $colorMap = ['sellado' => 'amber', 'incompleto' => 'orange', 'completado' => 'purple', 'consumido' => 'red', 'sueltosSerializados' => 'green', 'sueltosCantidad' => 'indigo'];
                $tipoColor = $colorMap[$filtroTipoInventario] ?? 'gray';
            @endphp

            {{-- ═══ NIVEL 1 y NIVEL 2: sin cambios (modales) ═══ --}}
            @php
                $listaItems = ($modalListadoAbierto ? ($listadoInventario ?? collect()) : collect());
                $esKits = !in_array($filtroTipoInventario, ['sueltosSerializados', 'sueltosCantidad'], true);
            @endphp

            <x-modal wire:model.live="modalListadoAbierto" maxWidth="xl">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
                    <button type="button" wire:click="volverDashboard"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="w-10 h-10 rounded-lg bg-{{ $tipoColor }}-100 flex items-center justify-center shrink-0">
                        <i class="fas {{ $listadoInventarioIcono }} text-{{ $tipoColor }}-600"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-gray-800">{{ $listadoInventarioTitulo }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $listaItems->count() }} item{{ $listaItems->count() !== 1 ? 's' : '' }}</p>
                    </div>
                    <button type="button" wire:click="volverDashboard" aria-label="Cerrar"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto px-1 py-1">
                    @forelse ($listaItems as $item)
                        @php
                            $estadoMeta = match($item->estado ?? null) {
                                'en_stock'   => ['label' => 'Sellado',    'chip' => 'bg-green-100 text-green-700'],
                                'abierto'    => ['label' => 'Abierto',    'chip' => 'bg-orange-100 text-orange-700'],
                                'completado' => ['label' => 'Completado', 'chip' => 'bg-purple-100 text-purple-700'],
                                'asignado'   => ['label' => 'Asignado',   'chip' => 'bg-yellow-100 text-yellow-700'],
                                'consumido'  => ['label' => 'Consumido',  'chip' => 'bg-red-100 text-red-700'],
                                default      => ['label' => $item->estado ?? '—', 'chip' => 'bg-gray-100 text-gray-600'],
                            };
                        @endphp
                        @if ($esKits)
                            <button type="button" wire:click="verDetalleKit({{ $item->id }})"
                                class="w-full flex items-center gap-3 px-5 py-3.5 text-left border-b border-gray-100 transition-colors hover:bg-{{ $tipoColor }}-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                <div class="w-10 h-10 rounded-lg bg-{{ $tipoColor }}-50 flex items-center justify-center shrink-0">
                                    <i class="fas {{ $listadoInventarioIcono }} text-{{ $tipoColor }}-500 text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="text-sm font-bold text-gray-800">{{ $item->producto?->nombre ?? 'Kit' }}</span>
                                        <span class="px-1.5 py-0.5 bg-gray-900 text-white text-[10px] font-black rounded tabular-nums">#{{ $item->id }}</span>
                                        <span class="px-2 py-0.5 {{ $estadoMeta['chip'] }} text-[10px] font-bold rounded-full">{{ $estadoMeta['label'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $item->sede?->nombre ?? '—' }}
                                        @if ($item->serie)
                                            <span class="text-gray-300 mx-1">|</span>
                                            <span class="font-mono">{{ $item->serie }}</span>
                                        @endif
                                        @if ($item->serviceOrder)
                                            <span class="text-gray-300 mx-1">|</span>
                                            <i class="fas fa-file-alt text-gray-400 mr-0.5"></i>Orden #{{ $item->service_order_id }}
                                            @if ($item->serviceOrder->cliente)
                                                · {{ $item->serviceOrder->cliente->nombre . ' ' . $item->serviceOrder->cliente->apellido }}
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                <span class="text-[10px] font-bold text-{{ $tipoColor }}-600 uppercase tracking-wide shrink-0">Ver kit</span>
                                <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0"></i>
                            </button>
                        @else
                            <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-100">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800">{{ $item->producto?->nombre ?? 'Producto' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item->sede?->nombre ?? '—' }}</p>
                                </div>
                                @if ($filtroTipoInventario === 'sueltosSerializados' && $item->serie)
                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->serie }}</span>
                                @endif
                                @if ($filtroTipoInventario === 'sueltosCantidad')
                                    <span class="text-xs font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded tabular-nums">×{{ $item->cantidad_suelta_real ?? $item->cantidad }}</span>
                                @endif
                            </div>
                        @endif
                    @empty
                        <div class="px-5 py-12 text-center">
                            <i class="fas {{ $listadoInventarioIcono }} text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-400 text-sm">Sin items para mostrar</p>
                        </div>
                    @endforelse
                </div>
            </x-modal>

            @php
                $k = $mostrarDetalleKit ? $kitDetalle : null;
                $estadoKit = $k ? (match($k->estado) {
                    'en_stock'   => ['label' => 'Sellado',    'chip' => 'bg-green-100 text-green-700',   'icon' => 'fa-box'],
                    'abierto'    => ['label' => 'Abierto',    'chip' => 'bg-orange-100 text-orange-700', 'icon' => 'fa-folder-open'],
                    'completado' => ['label' => 'Completado', 'chip' => 'bg-purple-100 text-purple-700', 'icon' => 'fa-check-circle'],
                    'asignado'   => ['label' => 'Asignado',   'chip' => 'bg-yellow-100 text-yellow-700', 'icon' => 'fa-user-check'],
                    'consumido'  => ['label' => 'Consumido',  'chip' => 'bg-red-100 text-red-700',       'icon' => 'fa-fire'],
                    default      => ['label' => $k->estado,   'chip' => 'bg-gray-100 text-gray-600',     'icon' => 'fa-circle'],
                }) : null;
                $pctCompletado = ($k && $k->totalEsperado > 0) ? round($k->totalPresente / $k->totalEsperado * 100) : 0;
            @endphp

            <x-modal wire:model.live="mostrarDetalleKit" maxWidth="xl">
                @if ($k)
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
                        <button type="button" wire:click="volverListado"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="w-10 h-10 rounded-lg bg-{{ $tipoColor }}-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-box text-{{ $tipoColor }}-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-gray-800 truncate">{{ $k->producto?->nombre ?? 'Kit' }}</h3>
                                <span class="px-1.5 py-0.5 bg-gray-900 text-white text-[10px] font-black rounded tabular-nums">#{{ $k->id }}</span>
                                <span class="px-2 py-0.5 {{ $estadoKit['chip'] }} text-[10px] font-bold rounded-full">{{ $estadoKit['label'] }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $k->sede?->nombre ?? '—' }}
                                @if ($k->serie)
                                    <span class="text-gray-300 mx-1">|</span>
                                    <span class="font-mono">{{ $k->serie }}</span>
                                @endif
                            </p>
                        </div>
                        <button type="button" wire:click="volverListado" aria-label="Cerrar"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="max-h-[70vh] overflow-y-auto px-5 py-4 space-y-5">

                            {{-- Progreso --}}
                            @if ($k->totalEsperado > 0)
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1.5">
                                        <span class="font-semibold text-gray-600">Componentes</span>
                                        <span class="font-bold tabular-nums {{ $pctCompletado >= 100 ? 'text-green-600' : 'text-gray-700' }}">{{ $k->totalPresente }}/{{ $k->totalEsperado }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full transition-all {{ $pctCompletado >= 100 ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $pctCompletado }}%"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- Receta --}}
                            @if ($k->recetaDetalles && $k->recetaDetalles->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Receta del kit</p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->recetaDetalles as $r)
                                            <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ $r['completo'] ? 'bg-green-50 border border-green-200' : 'bg-orange-50 border border-orange-200' }}">
                                                <i class="fas {{ $r['es_serializado'] ? 'fa-microchip text-indigo-500' : 'fa-cubes text-amber-500' }} text-xs"></i>
                                                <span class="flex-1 text-sm font-medium text-gray-700">{{ $r['nombre'] }}</span>
                                                <span class="text-xs font-bold tabular-nums {{ $r['completo'] ? 'text-green-700' : 'text-orange-700' }}">{{ $r['presente'] }}/{{ $r['cantidad_esperada'] }}</span>
                                                @if ($r['completo'])
                                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                                @else
                                                    <i class="fas fa-exclamation text-orange-500 text-xs"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Piezas asignadas (activas, resumidas por producto) --}}
                            @if ($k->piezasResumidas && $k->piezasResumidas->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Equipos del kit ({{ $k->totalPresente }})</p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->piezasResumidas as $pr)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                                                <i class="fas fa-microchip text-indigo-500 text-xs shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $pr['nombre'] }}</p>
                                                    @if ($pr['series']->isNotEmpty())
                                                        <p class="text-xs text-gray-400 mt-0.5 font-mono truncate">
                                                            {{ $pr['series']->take(3)->implode(', ') }}{{ $pr['series']->count() > 3 ? ' +' . ($pr['series']->count() - 3) : '' }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($pr['estados']) && $pr['estados']->isNotEmpty())
                                                        <p class="text-[10px] text-gray-400 mt-0.5 capitalize">{{ $pr['estados']->implode(' · ') }}</p>
                                                    @endif
                                                </div>
                                                @if ($pr['cantidad'] > 1)
                                                    <span class="shrink-0 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-black rounded-full tabular-nums">×{{ $pr['cantidad'] }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Reemplazados / defectuosos (histórico de la conversión) --}}
                            @if ($k->reemplazados && $k->reemplazados->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-2">
                                        <i class="fas fa-exchange-alt mr-1"></i>Reemplazados / fuera de servicio ({{ $k->reemplazados->count() }})
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->reemplazados as $rep)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-amber-50 rounded-lg border border-amber-200">
                                                <i class="fas fa-triangle-exclamation text-amber-500 text-xs shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-amber-900 truncate">{{ $rep->producto?->nombre ?? '—' }}</p>
                                                    @if ($rep->serie)
                                                        <p class="text-xs text-amber-600 mt-0.5 font-mono">{{ $rep->serie }}</p>
                                                    @endif
                                                </div>
                                                <span class="shrink-0 px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">{{ $rep->estado }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Cantidad extra / repuestos de la orden (azul) --}}
                            @if ($k->itemsExtraOrden && $k->itemsExtraOrden->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-2">
                                        <i class="fas fa-plus-circle mr-1"></i>Cantidad extra asignada ({{ $k->itemsExtraOrden->count() }})
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->itemsExtraOrden as $extra)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-blue-50 rounded-lg border border-blue-200">
                                                <i class="fas fa-cubes text-blue-500 text-xs shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-blue-800 truncate">{{ $extra->producto?->nombre ?? '—' }}</p>
                                                    @if ($extra->serie)
                                                        <p class="text-xs text-blue-500 mt-0.5 font-mono">#{{ $extra->serie }}</p>
                                                    @elseif (($extra->atributos['cantidad_solicitada'] ?? null))
                                                        <p class="text-xs text-blue-500 mt-0.5">Cantidad: {{ $extra->atributos['cantidad_solicitada'] }}</p>
                                                    @endif
                                                </div>
                                                <span class="shrink-0 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">{{ $extra->estado }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Movimientos de stock de la conversión (kits consumidos) --}}
                            @if ($k->movimientosConversion && $k->movimientosConversion->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        <i class="fas fa-exchange-alt mr-1"></i>Movimientos de la conversión ({{ $k->movimientosConversion->count() }})
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->movimientosConversion as $mov)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-white rounded-lg border border-gray-200">
                                                <span class="shrink-0 w-14 text-center text-[10px] font-black uppercase rounded {{ $mov->tipo === 'entrada' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $mov->tipo }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $mov->producto?->nombre ?? '—' }}</p>
                                                    @if ($mov->motivo)
                                                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $mov->motivo }}</p>
                                                    @endif
                                                </div>
                                                <span class="shrink-0 text-xs font-bold tabular-nums text-gray-700">×{{ $mov->cantidad }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Orden de servicio --}}
                            @if ($k->serviceOrder)
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Orden de servicio</p>
                                    <div class="bg-gray-50 rounded-lg border border-gray-100 px-3 py-2.5 space-y-1">
                                        <p class="text-sm font-bold text-gray-800">
                                            <i class="fas fa-file-alt text-gray-400 mr-1"></i>Orden #{{ $k->service_order_id }}
                                        </p>
                                        @if ($k->serviceOrder->cliente)
                                            <p class="text-xs text-gray-500"><i class="fas fa-user text-gray-400 mr-1"></i>{{ $k->serviceOrder->cliente->nombre . ' ' . $k->serviceOrder->cliente->apellido }}</p>
                                        @endif
                                        @if ($k->serviceOrder->vehiculo)
                                            <p class="text-xs text-gray-500"><i class="fas fa-car text-gray-400 mr-1"></i>{{ $k->serviceOrder->vehiculo->marca }} {{ $k->serviceOrder->vehiculo->modelo }} — {{ $k->serviceOrder->vehiculo->placa }}</p>
                                        @endif
                                        @if ($k->serviceOrder->tecnico)
                                            <p class="text-xs text-gray-500"><i class="fas fa-wrench text-gray-400 mr-1"></i>{{ $k->serviceOrder->tecnico->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Datos del item --}}
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Datos del item</p>
                                <div class="bg-gray-50 rounded-lg border border-gray-100 px-3 py-2.5">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-gray-400">Creado</span>
                                            <p class="font-medium text-gray-700">{{ $k->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Sede</span>
                                            <p class="font-medium text-gray-700">{{ $k->sede?->nombre ?? '—' }}</p>
                                        </div>
                                        @if ($k->atributos)
                                            @foreach ($k->atributos as $key => $val)
                                                @if ($val)
                                                    @php
                                                        $displayKey = ucfirst(str_replace('_', ' ', $key));
                                                        $displayVal = $val;
                                                        // Formatear campos especiales
                                                        if ($key === 'abierto_por' && is_numeric($val)) {
                                                            $user = \App\Models\User::find($val);
                                                            $displayVal = $user?->name ?? "Usuario #{$val}";
                                                        } elseif (in_array($key, ['abierto_en', 'recepcion_fecha', 'fecha_recepcion']) && $val) {
                                                            try {
                                                                $displayVal = \Carbon\Carbon::parse($val)->format('d/m/Y H:i');
                                                            } catch (\Throwable) {}
                                                        } elseif ($key === 'motivo_apertura') {
                                                            $displayKey = 'Motivo apertura';
                                                        }
                                                    @endphp
                                                    <div>
                                                        <span class="text-gray-400">{{ $displayKey }}</span>
                                                        <p class="font-medium text-gray-700">{{ $displayVal }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center gap-3 px-5 py-3 border-t border-gray-200 bg-gray-50">
                            @if ($k->estado === 'abierto')
                                <button type="button" wire:click="abrirEditarItem({{ $k->id }})"
                                    class="px-4 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </button>
                            @endif
                            @if ($k->estado === 'abierto' && $k->producto->categoria->es_kit)
                                <div class="ml-auto">
                                    @if ($k->tieneFaltantes && !$k->todosConStock)
                                        <button type="button" disabled
                                            class="px-5 py-2 text-sm font-bold text-white bg-gray-400 rounded-lg cursor-not-allowed"
                                            title="Faltan componentes y no hay stock disponible">
                                            <i class="fas fa-clock mr-1"></i> Espera stock para completar
                                        </button>
                                    @else
                                        <button type="button" wire:click="abrirCompletarKit({{ $k->id }})"
                                            class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                            <i class="fas fa-plus mr-1"></i> Completar kit
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </x-modal>

        @endif

        @if ($vistaActual === 'catalogo')
            @if ($productos->count())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="hidden md:grid grid-cols-12 gap-4 px-4 py-3 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500">
                        <span class="col-span-6">Producto</span>
                        <span class="col-span-2">Disponible</span>
                        <span class="col-span-4 text-right">Acciones</span>
                    </div>
                    <ul class="divide-y divide-gray-100 max-h-[65vh] overflow-y-auto">
                        @foreach ($productos as $p)
                            @php
                                $stock = $p->categoria->es_kit ? $p->stockSueltoEnSede(\App\Models\Sede::activas()->orderBy('id')->first()?->id ?? 1) : $p->stock_disponible;
                                $stockColor = $stock > 0 ? 'text-green-600' : 'text-red-500';
                            @endphp
                            <li class="grid grid-cols-12 items-center gap-x-4 gap-y-2 px-4 py-3.5 transition-colors duration-150 hover:bg-gray-50">
                                <div class="col-span-8 md:col-span-6 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $p->nombre }}</p>
                                        @if ($p->categoria->es_kit)
                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-bold rounded-full shrink-0">Kit</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                        {{ $p->categoria->nombre }}@if ($p->marca) <span class="text-gray-300">|</span> {{ $p->marca }}@endif
                                    </p>
                                </div>
                                <div class="col-span-4 md:col-span-2 text-right md:text-left">
                                    <p class="text-2xl font-bold leading-none tabular-nums {{ $stockColor }}">{{ $stock }}</p>
                                    <p class="text-xs text-gray-400 mt-1">disponible</p>
                                </div>
                                <div class="col-span-12 md:col-span-4 flex flex-wrap items-center justify-end gap-2">
                                    <button type="button"
                                        wire:click="$dispatch('abrir-modal-entrada', { productoId: {{ $p->id }} })"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                        <i class="fa-solid fa-plus"></i> Entrada
                                    </button>
                                    <button type="button"
                                        wire:click="$dispatch('abrir-modal-editar-producto', { productoId: {{ $p->id }} })"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        <i class="fa-solid fa-edit"></i> Editar
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-4">{{ $productos->links('pagination::tailwind') }}</div>
            @else
                <x-almacen.empty-state icon="fa-box-open" message="No hay productos registrados" />
            @endif
        @endif

    </div>

    <livewire:almacen.productos.crear />
    <livewire:almacen.productos.registrar-entrada />
    <livewire:almacen.productos.editar />

    {{-- ═══ A partir de aquí: modales — sin cambios de UI, tal como estaban ═══ --}}

    @php
        $faltantes = collect($completarKitComponentes)->filter(fn($c) => $c['faltan'] > 0);
        $seleccion = $completarKitSeleccion;
        // Stock disponible para componentes por cantidad (['stock' => N] | collect()).
        $stockDe = function ($c): int {
            $d = $c['disponibles'] ?? null;
            return is_array($d) ? (int) ($d['stock'] ?? 0) : 0;
        };
        // Serializados: requieren selección exacta de items.
        // Cantidad: no hay selección — alcanza con stock suelto >= faltan.
        $puedeCompletar = $faltantes->isEmpty() || $faltantes->every(function ($c) use ($seleccion, $stockDe) {
            if (!($c['es_serializado'] ?? false)) {
                return $stockDe($c) >= $c['faltan'];
            }
            $elegidos = collect($seleccion[$c['producto_id']] ?? [])->filter()->count();
            return $elegidos >= $c['faltan'];
        });
        $completos = collect($completarKitComponentes)->filter(fn($c) => $c['faltan'] <= 0);
        $totalFaltan = $faltantes->sum('faltan');
        $totalElegidos = $faltantes->sum(function ($c) use ($seleccion, $stockDe) {
            if (!($c['es_serializado'] ?? false)) {
                return min($stockDe($c), $c['faltan']);
            }
            return min(collect($seleccion[$c['producto_id']] ?? [])->filter()->count(), $c['faltan']);
        });
        $pctElegidos = $totalFaltan > 0 ? round($totalElegidos / $totalFaltan * 100) : 100;
    @endphp

    <x-modal wire:model.live="modalCompletarKitAbierto" maxWidth="xl">
                <div class="px-5 pt-4 pb-3 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-puzzle-piece text-indigo-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-gray-800">Completar kit</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $completarKitNombre }}</p>
                        </div>
                        <button type="button" wire:click="cerrarCompletarKit" aria-label="Cerrar"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @if ($totalFaltan > 0)
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-600">Piezas elegidas</span>
                                <span class="font-semibold tabular-nums {{ $puedeCompletar ? 'text-green-600' : 'text-gray-700' }}">
                                    {{ $totalElegidos }} de {{ $totalFaltan }}
                                </span>
                            </div>
                            <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $puedeCompletar ? 'bg-green-500' : 'bg-indigo-500' }}"
                                     style="width: {{ $pctElegidos }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="max-h-[65vh] overflow-y-auto px-5 py-4 space-y-4">
                    @error('general')
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $message }}
                        </div>
                    @enderror

                    @foreach ($faltantes as $comp)
                        @php
                            $idsElegidos = is_array($completarKitSeleccion[$comp['producto_id']] ?? null) ? $completarKitSeleccion[$comp['producto_id']] : [];
                            $esSerialComp = $comp['es_serializado'] ?? false;
                            $stockComp = is_array($comp['disponibles']) ? (int) ($comp['disponibles']['stock'] ?? 0) : 0;
                            if ($esSerialComp) {
                                $elegidosComp = collect($idsElegidos)->filter()->count();
                                $listo = $elegidosComp >= $comp['faltan'];
                            } else {
                                // Cantidad: "listo" según stock suelto, no hay selección de items.
                                $elegidosComp = min($stockComp, $comp['faltan']);
                                $listo = $stockComp >= $comp['faltan'];
                            }
                            $disponibles = $comp['disponibles'] instanceof \Illuminate\Support\Collection ? $comp['disponibles'] : collect($comp['disponibles']);
                        @endphp
                        <section class="rounded-xl border {{ $listo ? 'border-green-200' : 'border-amber-200' }} overflow-hidden">
                            <div class="flex items-center gap-2 px-4 py-2.5 {{ $listo ? 'bg-green-50' : 'bg-amber-50' }}">
                                <i class="fas {{ $listo ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-amber-600' }} text-sm"></i>
                                <span class="flex-1 min-w-0 truncate text-sm font-bold text-gray-800">{{ $comp['nombre'] }}</span>
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-xs font-bold tabular-nums {{ $listo ? 'bg-green-200 text-green-800' : 'bg-amber-200 text-amber-800' }}">
                                    {{ $elegidosComp }}/{{ $comp['faltan'] }}
                                </span>
                            </div>
                            @if ($disponibles->isNotEmpty())
                                <div class="p-2.5 space-y-1.5 bg-white">
                                    @if ($esSerialComp)
                                        @foreach ($comp['disponibles'] as $item)
                                            @php
                                                $elegido = in_array($item['id'], $idsElegidos);
                                                $produce = $item['atributos']['produce'] ?? $item->atributos['produce'] ?? null;
                                                $fechaItem = $item['atributos']['fecha_recepcion'] ?? $item['atributos']['recepcion_fecha'] ?? $item['atributos']['fecha'] ?? '';
                                            @endphp
                                            <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors focus-within:ring-2 focus-within:ring-indigo-500 {{ $elegido ? 'bg-indigo-50 border-indigo-300' : 'bg-white border-gray-200 hover:border-gray-300' }}">
                                                <input type="checkbox"
                                                    wire:model.live="completarKitSeleccion.{{ $comp['producto_id'] }}"
                                                    value="{{ $item['id'] }}"
                                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="flex-1 min-w-0 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                                    <span class="font-mono text-sm font-bold text-gray-800">{{ $item['serie'] }}</span>
                                                    @if ($produce)
                                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded">{{ $produce }}</span>
                                                    @endif
                                                    @if ($fechaItem)
                                                        <span class="text-xs text-gray-400">{{ $fechaItem }}</span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    @else
                                        {{-- Cantidad: sin checkboxes — solo se muestra el stock suelto disponible. --}}
                                        <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-indigo-100 bg-indigo-50/50 text-sm">
                                            <i class="fas fa-cubes text-indigo-500"></i>
                                            <span class="text-slate-700">
                                                Disponible en almacén:
                                                <strong class="tabular-nums">{{ $stockComp }}</strong>
                                                — se tomarán <strong class="tabular-nums">{{ $comp['faltan'] }}</strong> al confirmar.
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="px-4 py-4 text-center text-sm text-red-600 font-semibold bg-white">
                                    <i class="fas fa-times-circle mr-1"></i>{{ $esSerialComp ? 'Sin items disponibles en almacén' : 'Sin stock suelto disponible' }}
                                </div>
                            @endif
                        </section>
                    @endforeach

                    @if ($completos->isNotEmpty())
                        <div class="pt-1">
                            <p class="text-xs text-gray-500 mb-1.5">Ya completos</p>
                            <ul class="flex flex-wrap gap-1.5">
                                @foreach ($completos as $comp)
                                    <li class="inline-flex items-center gap-1.5 rounded-md bg-green-50 border border-green-200 px-2 py-1 text-xs">
                                        <i class="fas fa-check text-green-500"></i>
                                        <span class="font-medium text-gray-700">{{ $comp['nombre'] }}</span>
                                        <span class="text-green-700 font-semibold tabular-nums">{{ $comp['cantidad_esperada'] }}/{{ $comp['cantidad_esperada'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 px-5 py-3 border-t border-gray-200 bg-gray-50">
                    <button type="button" wire:click="cerrarCompletarKit"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        Cancelar
                    </button>
                    <div class="ml-auto flex items-center gap-3">
                        @if ($puedeCompletar)
                            <button type="button" wire:click="completarKit" wire:loading.attr="disabled"
                                class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                <span wire:loading.remove wire:target="completarKit"><i class="fas fa-check mr-1"></i> Completar kit</span>
                                <span wire:loading wire:target="completarKit">Guardando...</span>
                            </button>
                        @else
                            <span class="text-xs text-amber-700 font-medium">Elegí {{ $totalFaltan - $totalElegidos }} más</span>
                            <button type="button" disabled
                                class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg opacity-40 cursor-not-allowed">
                                <i class="fas fa-check mr-1"></i> Completar kit
                            </button>
                        @endif
                    </div>
                </div>
        </x-modal>

    <script src="{{ asset('js/components/livewire-swal-listener.js') }}"></script>

    @if ($modalEditarItemAbierto)
        @php
            $item = \App\Models\ItemSerializado::with('producto.categoria')->find($editarItemId);
            $esquema = $item?->producto->categoria->esquema_atributos ?? ['serie'];
            $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
        @endphp

        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60" wire:click="cerrarEditarItem"></div>
            <div class="relative flex w-full max-w-md max-h-[85vh] flex-col overflow-hidden rounded-xl bg-white shadow-2xl border border-gray-200">
                <header class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-edit text-amber-600"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-gray-800">Completar datos</h3>
                        <p class="text-sm text-gray-500 truncate">
                            {{ $item?->producto?->nombre ?? 'Item' }}
                            @if ($editarItemId) <span class="text-gray-400">#{{ $editarItemId }}</span> @endif
                        </p>
                        @if ($editarItemKitInfo)
                            <div class="mt-1.5 pt-1.5 border-t border-gray-100 text-xs">
                                <p class="text-gray-600">
                                    <i class="fas fa-puzzle-piece mr-1 text-indigo-500"></i>
                                    Kit: <span class="font-medium text-gray-800">{{ $editarItemKitInfo['kit_nombre'] }}</span>
                                    @if ($editarItemKitInfo['kit_serie']) <span class="text-gray-400">#{{ $editarItemKitInfo['kit_serie'] }}</span> @endif
                                </p>
                                <p class="text-gray-600 mt-0.5">
                                    <i class="fas fa-cube mr-1 text-amber-500"></i>
                                    Componente: <span class="font-medium text-gray-800">{{ $editarItemKitInfo['componente_nombre'] }}</span>
                                    <span class="text-gray-400 ml-1">({{ $editarItemKitInfo['componente_presentes'] }}/{{ $editarItemKitInfo['componente_esperados'] }})</span>
                                </p>
                            </div>
                        @endif
                    </div>
                    <button type="button" wire:click="cerrarEditarItem" aria-label="Cerrar"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                        <i class="fas fa-times"></i>
                    </button>
                </header>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                    @foreach ($campos as $campo)
                        <div>
                            <label for="editar-item-{{ $campo }}" class="block text-sm font-semibold text-gray-700 mb-1">
                                {{ $campo === 'capacidad' ? 'Capacidad' : ucfirst($campo) }}
                            </label>
                            <input type="text"
                                id="editar-item-{{ $campo }}"
                                wire:model="editarItemData.{{ $campo }}"
                                wire:keydown.enter="guardarEditarItem"
                                @if ($loop->first) autofocus @endif
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 {{ $campo === 'serie' ? 'font-mono' : '' }}"
                                placeholder="{{ ucfirst($campo) }}"
                                maxlength="50">
                        </div>
                    @endforeach
                </div>

                <footer class="flex items-center gap-3 px-5 py-3 border-t border-gray-200 bg-gray-50">
                    <button type="button" wire:click="cerrarEditarItem"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                        Cancelar
                    </button>
                    <button type="button" wire:click="guardarEditarItem" wire:loading.attr="disabled"
                        class="ml-auto px-5 py-2 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </footer>
            </div>
        </div>
    @endif

</div>