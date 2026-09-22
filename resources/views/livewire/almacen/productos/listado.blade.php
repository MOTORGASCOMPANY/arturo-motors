<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-7xl mx-auto px-4 py-4 space-y-4">

    @pushOnce('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endPushOnce

        <div class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-sm -mx-4 px-4 py-2 space-y-2">

            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-store text-indigo-600"></i>
                        Almacén
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Inventario y productos del almacén</p>
                </div>
                <a href="{{ route('almacen.recepciones.crear') }}"
                    class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    <i class="fas fa-plus text-xs"></i>
                    Agregar producto
                </a>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-2 flex flex-wrap items-center gap-2">

                <nav class="flex gap-1 bg-gray-100 rounded-lg p-1" x-data aria-label="Vistas del almacén">
                    @foreach ([
                        'inventario' => ['icon' => 'fa-boxes-stacked', 'label' => 'Inventario'],
                        'kits'       => ['icon' => 'fa-box',          'label' => 'Kits'],
                        'catalogo'   => ['icon' => 'fa-list',         'label' => 'Catálogo'],
                    ] as $key => $tab)
                        <button type="button" wire:click="$set('vistaActual', '{{ $key }}')"
                            class="px-3.5 py-1.5 text-sm font-semibold rounded-md transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            :class="$wire.vistaActual === '{{ $key }}' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                            <i class="fas {{ $tab['icon'] }} mr-1.5"></i>{{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>

                @if ($vistaActual === 'inventario' || $vistaActual === 'kits')
                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto sm:ml-auto">
                        <select wire:model.live="filtroSedeId" aria-label="Filtrar por sede"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 w-full sm:w-52">
                            <option value="">Todas las sedes</option>
                            @foreach ($sedes as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                        <label class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full sm:w-64 focus-within:ring-2 focus-within:ring-indigo-500">
                            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                            <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                                type="text" wire:model.live.debounce.400ms="busquedaInventario"
                                placeholder="Buscar por nombre...">
                        </label>
                    </div>

                @elseif ($vistaActual === 'catalogo')
                    <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto lg:ml-auto">
                        <label class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full sm:w-64 focus-within:ring-2 focus-within:ring-indigo-500">
                            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                            <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                                type="text" wire:model.live.debounce.400ms="buscar"
                                placeholder="Buscar por nombre o código...">
                        </label>
                        <select wire:model.live="filterStock" aria-label="Filtrar por stock"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 w-full sm:w-40">
                            <option value="todos">Todo el stock</option>
                            <option value="bajo">Stock bajo</option>
                            <option value="sin">Sin stock</option>
                        </select>
                        <input type="text" wire:model.live="filterProveedor" placeholder="Proveedor..."
                            aria-label="Filtrar por proveedor"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 w-full sm:w-44">
                        <button type="button" wire:click="resetFilters"
                            class="px-3 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
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
                        ['tipo' => 'sellado',              'label' => 'Sellados',             'hint' => 'En stock',         'valor' => $c['sellados'] ?? 0,             'icono' => 'fa-box',          'color' => 'text-amber-600',  'icono_color' => 'text-amber-500',  'hover' => 'hover:bg-amber-50'],
                        ['tipo' => 'incompleto',           'label' => 'Incompletos',          'hint' => 'Abiertos',         'valor' => $c['incompletos'] ?? 0,          'icono' => 'fa-box-open',     'color' => 'text-orange-600', 'icono_color' => 'text-orange-500', 'hover' => 'hover:bg-orange-50'],
                        ['tipo' => 'completado',           'label' => 'Completados',          'hint' => 'Kits completados', 'valor' => $c['completados'] ?? 0,          'icono' => 'fa-check-circle', 'color' => 'text-purple-600', 'icono_color' => 'text-purple-500', 'hover' => 'hover:bg-purple-50'],
                        ['tipo' => 'consumido',            'label' => 'Consumidos',           'hint' => 'En órdenes',       'valor' => $c['consumidos'] ?? 0,           'icono' => 'fa-fire',         'color' => 'text-red-600',    'icono_color' => 'text-red-500',    'hover' => 'hover:bg-red-50'],
                        ['tipo' => 'sueltosSerializados',  'label' => 'Sueltos con serie',    'hint' => 'Tipos de producto','valor' => $c['sueltosSerializados'] ?? 0,  'icono' => 'fa-barcode',      'color' => 'text-green-600',  'icono_color' => 'text-green-500',  'hover' => 'hover:bg-green-50'],
                        ['tipo' => 'sueltosCantidad',      'label' => 'Sueltos por cantidad', 'hint' => 'Tipos de producto','valor' => $c['sueltosCantidadTipos'] ?? 0, 'icono' => 'fa-cubes',        'color' => 'text-indigo-600', 'icono_color' => 'text-indigo-500', 'hover' => 'hover:bg-indigo-50'],
                    ];
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-px bg-gray-200 rounded-xl border border-gray-200 overflow-hidden">
                    @foreach ($stats as $st)
                        <button type="button" wire:click="verListadoInventario('{{ $st['tipo'] }}')"
                            class="bg-white p-3.5 text-left transition-colors {{ $st['hover'] }} focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                            <span class="flex items-center gap-2 text-sm font-medium text-gray-600">
                                <i class="fas {{ $st['icono'] }} {{ $st['icono_color'] }}"></i>
                                {{ $st['label'] }}
                            </span>
                            <p class="text-3xl font-black {{ $st['color'] }} mt-1 leading-none tabular-nums">{{ $st['valor'] }}</p>
                            <span class="text-xs text-gray-400 mt-1 block">{{ $st['hint'] }}</span>
                        </button>
                    @endforeach
                </div>

                @if ($incompletos->isNotEmpty())
                    <section class="bg-white rounded-xl border border-orange-200 overflow-hidden">
                        <x-almacen.section-header icon="fa-box-open" color="orange" title="Kits incompletos" :count="$incompletos->flatten()->count()" hint="Necesitan piezas para quedar completos" />
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-px bg-gray-100 max-h-80 overflow-y-auto">
                            @foreach ($incompletos as $productoId => $items)
                                @php $prod = $items->first()?->producto; @endphp
                                @foreach ($items as $kitItem)
                                    <x-almacen.kit-item
                                        :nombre="$prod?->nombre ?? 'Producto'"
                                        :sede="$kitItem->sede?->nombre ?? '—'"
                                        :id="$kitItem->id"
                                        icon="fa-box-open"
                                        icon-bg="bg-orange-50"
                                        icon-color="text-orange-500"
                                        action-route="abrirCompletarKit"
                                        :action-id="$kitItem->id"
                                        action-label="Completar"
                                    />
                                @endforeach
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($sellados->isNotEmpty())
                    <section class="bg-white rounded-xl border border-amber-200 overflow-hidden">
                        <x-almacen.section-header icon="fa-box" color="amber" title="Kits sellados" :count="$sellados->flatten()->count()" />
                        <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                            @foreach ($sellados as $productoId => $items)
                                @php $prod = $items->first()?->producto; @endphp
                                <li>
                                    <button type="button" wire:click="verListadoInventario('sellado')"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors hover:bg-amber-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center shrink-0"><i class="fas fa-box text-amber-500 text-sm"></i></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-gray-500">{{ $items->first()?->sede?->nombre ?? '—' }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full tabular-nums">{{ $items->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($completados->isNotEmpty())
                    <section class="bg-white rounded-xl border border-purple-200 overflow-hidden">
                        <x-almacen.section-header icon="fa-check-circle" color="purple" title="Kits completados" :count="$completados->flatten()->count()" />
                        <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                            @foreach ($completados as $productoId => $items)
                                @php $prod = $items->first()?->producto; @endphp
                                <li>
                                    <button type="button" wire:click="verListadoInventario('completado')"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors hover:bg-purple-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center shrink-0"><i class="fas fa-check-circle text-purple-500 text-sm"></i></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-gray-500">{{ $items->first()?->sede?->nombre ?? '—' }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-bold rounded-full tabular-nums">{{ $items->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($consumidos->isNotEmpty())
                    <section class="bg-white rounded-xl border border-red-200 overflow-hidden">
                        <x-almacen.section-header icon="fa-fire" color="red" title="Kits consumidos" :count="$consumidos->flatten()->count()" />
                        <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                            @foreach ($consumidos as $productoId => $items)
                                @php $prod = $items->first()?->producto; @endphp
                                <li>
                                    <button type="button" wire:click="verListadoInventario('consumido')"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center shrink-0"><i class="fas fa-fire text-red-500 text-sm"></i></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-gray-500">{{ $items->first()?->sede?->nombre ?? '—' }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full tabular-nums">{{ $items->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($sueltosS->isNotEmpty() || $sueltosC->isNotEmpty())
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
                        @if ($sueltosS->isNotEmpty())
                            <section class="bg-white rounded-xl border border-green-200 overflow-hidden">
                                <x-almacen.section-header icon="fa-barcode" color="green" title="Piezas sueltas con serie" :count="$sueltosS->flatten()->count()" />
                                <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                                    @foreach ($sueltosS as $productoId => $items)
                                        @php $prod = $items->first()?->producto; @endphp
                                        <li>
                                            <button type="button" wire:click="verListadoInventario('sueltosSerializados')"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors hover:bg-green-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center shrink-0"><i class="fas fa-barcode text-green-500 text-sm"></i></div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $items->first()?->sede?->nombre ?? '—' }}</p>
                                                </div>
                                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full tabular-nums">{{ $items->count() }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if ($sueltosC->isNotEmpty())
                            <section class="bg-white rounded-xl border border-indigo-200 overflow-hidden">
                                <x-almacen.section-header icon="fa-cubes" color="indigo" title="Piezas sueltas por cantidad" :count="$sueltosC->count()" />
                                <ul class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                                    @foreach ($sueltosC as $stock)
                                        <li class="flex items-center gap-3 px-4 py-2.5">
                                            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0"><i class="fas fa-cubes text-indigo-500 text-sm"></i></div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-bold text-gray-800 truncate">{{ $stock->producto?->nombre ?? 'Producto' }}</p>
                                                <p class="text-xs text-gray-500">{{ $stock->sede?->nombre ?? '—' }}</p>
                                            </div>
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full tabular-nums">{{ $stock->cantidad }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif
                    </div>
                @endif

                @if ($sellados->isEmpty() && $incompletos->isEmpty() && $completados->isEmpty() && $consumidos->isEmpty() && $sueltosS->isEmpty() && $sueltosC->isEmpty())
                    <x-almacen.empty-state icon="fa-boxes-stacked" message="No hay inventario para mostrar" />
                @endif
            @endif

            @php
                $colorMap = ['sellado' => 'amber', 'incompleto' => 'orange', 'completado' => 'purple', 'consumido' => 'red', 'sueltosSerializados' => 'green', 'sueltosCantidad' => 'indigo'];
                $tipoColor = $colorMap[$filtroTipoInventario] ?? 'gray';
            @endphp

            {{-- ═══ NIVEL 1: LISTADO POR TIPO (MODAL) ═══ --}}
            @if ($nivelInventario === 'listado')
                @php
                    $listaItems = $listadoInventario ?? collect();
                    $esKits = !in_array($filtroTipoInventario, ['sueltosSerializados', 'sueltosCantidad']);
                @endphp

                <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-900/60" wire:click="volverDashboard"></div>
                    <div class="relative flex w-full max-w-xl max-h-[92vh] flex-col overflow-hidden rounded-t-2xl sm:rounded-xl bg-white shadow-2xl border border-gray-200 z-10"
                         wire:click.away="volverDashboard">

                        <header class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
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
                        </header>

                        <div class="flex-1 overflow-y-auto px-1 py-1">
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
                                        class="w-full flex items-center gap-3 px-5 py-3 text-left border-b border-gray-100 transition-colors hover:bg-{{ $tipoColor }}-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="text-sm font-bold text-gray-800">{{ $item->producto?->nombre ?? 'Kit' }}</span>
                                                <span class="px-2 py-0.5 {{ $estadoMeta['chip'] }} text-[10px] font-bold rounded-full">{{ $estadoMeta['label'] }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $item->sede?->nombre ?? '—' }}
                                                <span class="text-gray-300 mx-1">|</span> #{{ $item->id }}
                                                @if ($item->serie)
                                                    <span class="text-gray-300 mx-1">|</span>
                                                    <span class="font-mono">{{ $item->serie }}</span>
                                                @endif
                                                @if ($item->serviceOrder)
                                                    <span class="text-gray-300 mx-1">|</span>
                                                    <i class="fas fa-file-alt text-gray-400 mr-0.5"></i>Orden #{{ $item->service_order_id }}
                                                @endif
                                            </p>
                                        </div>
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
                                            <span class="text-xs font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded tabular-nums">×{{ $item->cantidad }}</span>
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
                    </div>
                </div>
            @endif

            {{-- ═══ NIVEL 2: DETALLE DEL KIT (MODAL) ═══ --}}
            @if ($nivelInventario === 'detalle' && $kitDetalle)
                @php
                    $k = $kitDetalle;
                    $estadoKit = match($k->estado) {
                        'en_stock'   => ['label' => 'Sellado',    'chip' => 'bg-green-100 text-green-700',   'icon' => 'fa-box'],
                        'abierto'    => ['label' => 'Abierto',    'chip' => 'bg-orange-100 text-orange-700', 'icon' => 'fa-folder-open'],
                        'completado' => ['label' => 'Completado', 'chip' => 'bg-purple-100 text-purple-700', 'icon' => 'fa-check-circle'],
                        'asignado'   => ['label' => 'Asignado',   'chip' => 'bg-yellow-100 text-yellow-700', 'icon' => 'fa-user-check'],
                        'consumido'  => ['label' => 'Consumido',  'chip' => 'bg-red-100 text-red-700',       'icon' => 'fa-fire'],
                        default      => ['label' => $k->estado,   'chip' => 'bg-gray-100 text-gray-600',     'icon' => 'fa-circle'],
                    };
                    $pctCompletado = $k->totalEsperado > 0 ? round($k->totalPresente / $k->totalEsperado * 100) : 0;
                @endphp

                <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4" role="dialog" aria-modal="true">
                    <div class="fixed inset-0 bg-gray-900/60" wire:click="volverListado"></div>
                    <div class="relative flex w-full max-w-xl max-h-[92vh] flex-col overflow-hidden rounded-t-2xl sm:rounded-xl bg-white shadow-2xl border border-gray-200 z-10"
                         wire:click.away="volverListado">

                        <header class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
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
                                    <span class="px-2 py-0.5 {{ $estadoKit['chip'] }} text-[10px] font-bold rounded-full">{{ $estadoKit['label'] }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    {{ $k->sede?->nombre ?? '—' }}
                                    <span class="text-gray-300 mx-1">|</span> #{{ $k->id }}
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
                        </header>

                        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

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

                            {{-- Piezas asignadas --}}
                            @if ($k->piezasEnKit && $k->piezasEnKit->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Piezas asignadas ({{ $k->piezasEnKit->count() }})</p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->piezasEnKit as $pieza)
                                            @php
                                                $pEstado = match($pieza->estado) {
                                                    'en_stock' => ['label' => 'En stock', 'chip' => 'bg-green-100 text-green-700'],
                                                    'reemplazado' => ['label' => 'Asignado', 'chip' => 'bg-blue-100 text-blue-700'],
                                                    default => ['label' => $pieza->estado, 'chip' => 'bg-gray-100 text-gray-600'],
                                                };
                                            @endphp
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-700">{{ $pieza->producto?->nombre ?? '—' }}</p>
                                                    @if ($pieza->serie)
                                                        <p class="text-xs text-gray-500 mt-0.5 font-mono">#{{ $pieza->serie }}</p>
                                                    @endif
                                                </div>
                                                <span class="px-2 py-0.5 {{ $pEstado['chip'] }} text-[10px] font-bold rounded-full">{{ $pEstado['label'] }}</span>
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
                                            <p class="text-xs text-gray-500"><i class="fas fa-user text-gray-400 mr-1"></i>{{ $k->serviceOrder->cliente->name }}</p>
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

                        <footer class="flex items-center gap-3 px-5 py-3 border-t border-gray-200 bg-gray-50">
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
                        </footer>
                    </div>
                </div>
            @endif

        @endif

        @if ($vistaActual === 'kits')
            @php
                $kitsSell   = $kits['sellados']    ?? collect();
                $kitsIncomp = $kits['incompletos'] ?? collect();
                $kitsCons   = $kits['consumidos']  ?? collect();
                $kitsComp   = $kits['completados']  ?? collect();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start">

                {{-- Sellados: items individuales (consistente con otras columnas) --}}
                <section class="bg-white rounded-xl border border-amber-200 overflow-hidden">
                    <x-almacen.section-header icon="fa-box" color="amber" title="Sellados" :count="$kitsSell->flatten()->count()" />
                    <div class="bg-gray-50 p-2 space-y-2 max-h-[65vh] overflow-y-auto">
                        @forelse ($kitsSell as $productoId => $items)
                            @php $prod = $items->first()?->producto; @endphp
                            @foreach ($items as $kitItem)
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-gray-500">{{ $kitItem->sede?->nombre ?? '—' }} <span class="text-gray-300">|</span> #{{ $kitItem->id }}</p>
                                        </div>
                                        <button type="button"
                                            wire:click="verDetalleKit({{ $kitItem->id }})"
                                            class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-200 transition whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <x-almacen.empty-state icon="fa-box" message="Sin kits sellados" />
                        @endforelse
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-orange-200 overflow-hidden">
                    <x-almacen.section-header icon="fa-box-open" color="orange" title="Incompletos" :count="$kitsIncomp->flatten()->count()" :highlight="true" />
                    <div class="bg-gray-50 p-2 space-y-2 max-h-[65vh] overflow-y-auto">
                        @forelse ($kitsIncomp as $productoId => $items)
                            @php $prod = $items->first()?->producto; @endphp
                            @foreach ($items as $kitItem)
                                @php
                                    $receta = \App\Models\KitComponente::where('producto_kit_id', $kitItem->producto_id)->get();
                                    $piezasAct = $kitItem->piezasEnKit ?? collect();
                                    $countAct = $piezasAct->pluck('producto_id')->countBy()->toArray();
                                    $faltanComps = $receta->filter(fn($r) => ($countAct[$r->producto_componente_id] ?? 0) < $r->cantidad_esperada);
                                @endphp
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-gray-500">{{ $kitItem->sede?->nombre ?? '—' }} <span class="text-gray-300">|</span> #{{ $kitItem->id }}</p>
                                        </div>
                                        <button type="button"
                                            wire:click="abrirCompletarKit({{ $kitItem->id }})"
                                            class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                            <i class="fas fa-plus mr-1"></i> Completar
                                        </button>
                                    </div>
                                    @if ($faltanComps->isNotEmpty())
                                        <ul class="mt-2.5 flex flex-wrap gap-1.5" aria-label="Componentes que faltan">
                                            @foreach ($faltanComps as $f)
                                                <li class="inline-flex items-center gap-1.5 rounded-md bg-orange-50 border border-orange-200 px-2 py-1 text-xs">
                                                    <i class="fas fa-exclamation-circle text-orange-500"></i>
                                                    <span class="font-semibold text-gray-700">{{ $f->componente->nombre ?? '—' }}</span>
                                                    <span class="font-bold text-orange-700 tabular-nums">×{{ $f->cantidad_esperada - ($countAct[$f->producto_componente_id] ?? 0) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        @empty
                            <x-almacen.empty-state icon="fa-box-open" message="Sin kits incompletos" />
                        @endforelse
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <x-almacen.section-header icon="fa-check-circle" color="gray" title="Completados" :count="$kitsComp->flatten()->count()" />
                    <div class="bg-gray-50 p-2 space-y-2 max-h-[65vh] overflow-y-auto">
                        @forelse ($kitsComp as $productoId => $items)
                            @php $prod = $items->first()?->producto; @endphp
                            @foreach ($items as $kitItem)
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-gray-500">{{ $kitItem->sede?->nombre ?? '—' }} <span class="text-gray-300">|</span> #{{ $kitItem->id }}</p>
                                        </div>
                                        <button type="button"
                                            wire:click="verDetalleKit({{ $kitItem->id }})"
                                            class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-200 transition whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <x-almacen.empty-state icon="fa-check-circle" message="Sin kits completados" />
                        @endforelse
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <x-almacen.section-header icon="fa-fire" color="gray" title="Consumidos" :count="$kitsCons->flatten()->count()" />
                    <div class="bg-gray-50 p-2 space-y-2 max-h-[65vh] overflow-y-auto">
                        @forelse ($kitsCons as $productoId => $items)
                            @php $prod = $items->first()?->producto; @endphp
                            @foreach ($items as $kitItem)
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                                            <p class="text-xs text-gray-500">{{ $kitItem->sede?->nombre ?? '—' }} <span class="text-gray-300">|</span> #{{ $kitItem->id }}</p>
                                            @if ($kitItem->serviceOrder)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-file-alt text-gray-400 mr-1"></i>
                                                    Orden #{{ $kitItem->service_order_id }}
                                                    <span class="text-gray-300">|</span>
                                                    {{ $kitItem->serviceOrder->tecnico?->name ?? '—' }}
                                                </p>
                                            @endif
                                        </div>
                                        <button type="button"
                                            wire:click="verDetalleKit({{ $kitItem->id }})"
                                            class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-200 transition whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <x-almacen.empty-state icon="fa-fire" message="Sin kits consumidos" />
                        @endforelse
                    </div>
                </section>

            </div>
        @endif

        @if ($vistaActual === 'catalogo')
            @if ($productos->count())
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="hidden md:grid grid-cols-12 gap-4 px-4 py-2.5 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500">
                        <span class="col-span-6">Producto</span>
                        <span class="col-span-2">Disponible</span>
                        <span class="col-span-4 text-right">Acciones</span>
                    </div>
                    <ul class="divide-y divide-gray-100 max-h-[65vh] overflow-y-auto">
                        @foreach ($productos as $p)
                            @php
                                $stock = $p->categoria->es_kit ? $p->stockTotal() : $p->stock_disponible;
                                $stockColor = $stock > 0 ? 'text-green-600' : 'text-red-500';
                            @endphp
                            <li class="grid grid-cols-12 items-center gap-x-4 gap-y-2 px-4 py-3 hover:bg-gray-50 transition-colors">
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
                                <div class="col-span-12 md:col-span-4 flex flex-wrap items-center justify-end gap-1.5">
                                    <button type="button"
                                        wire:click="$dispatch('abrir-modal-entrada', { productoId: {{ $p->id }} })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                        <i class="fa-solid fa-plus"></i> Entrada
                                    </button>
                                    <button type="button"
                                        wire:click="$dispatch('abrir-modal-editar-producto', { productoId: {{ $p->id }} })"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        <i class="fa-solid fa-edit"></i> Editar
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-3">{{ $productos->links('pagination::tailwind') }}</div>
            @else
                <x-almacen.empty-state icon="fa-box-open" message="No hay productos registrados" />
            @endif
        @endif

    </div>

    <livewire:almacen.productos.crear />
    <livewire:almacen.productos.registrar-entrada />
    <livewire:almacen.productos.editar />

    @if ($modalCompletarKitAbierto)
        @php
            $faltantes = collect($completarKitComponentes)->filter(fn($c) => $c['faltan'] > 0);
            $seleccion = $completarKitSeleccion;
            $puedeCompletar = $faltantes->every(function ($c) use ($seleccion) {
                $elegidos = collect($seleccion[$c['producto_id']] ?? [])->filter()->count();
                return $elegidos >= $c['faltan'];
            });
            $completos = collect($completarKitComponentes)->filter(fn($c) => $c['faltan'] <= 0);
            $totalFaltan = $faltantes->sum('faltan');
            $totalElegidos = $faltantes->sum(fn ($c) => min(collect($seleccion[$c['producto_id']] ?? [])->filter()->count(), $c['faltan']));
            $pctElegidos = $totalFaltan > 0 ? round($totalElegidos / $totalFaltan * 100) : 100;
        @endphp

        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60" wire:click="cerrarCompletarKit"></div>
            <div class="relative flex w-full max-w-xl max-h-[92vh] flex-col overflow-hidden rounded-t-2xl sm:rounded-xl bg-white shadow-2xl border border-gray-200 z-10"
                 wire:click.away="cerrarCompletarKit">

                <header class="px-5 pt-4 pb-3 border-b border-gray-200">
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
                </header>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                    @error('general')
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $message }}
                        </div>
                    @enderror

                    @foreach ($faltantes as $comp)
                        @php
                            $idsElegidos = is_array($completarKitSeleccion[$comp['producto_id']] ?? null) ? $completarKitSeleccion[$comp['producto_id']] : [];
                            $elegidosComp = collect($idsElegidos)->filter()->count();
                            $listo = $elegidosComp >= $comp['faltan'];
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
                                </div>
                            @else
                                <div class="px-4 py-4 text-center text-sm text-red-600 font-semibold bg-white">
                                    <i class="fas fa-times-circle mr-1"></i>Sin items disponibles en almacén
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

                <footer class="flex items-center gap-3 px-5 py-3 border-t border-gray-200 bg-gray-50">
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
                </footer>
            </div>
        </div>
@endif

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
