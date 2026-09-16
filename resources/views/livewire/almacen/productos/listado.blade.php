<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-store text-indigo-600"></i>
                    Almacén
                </h2>
                <p class="text-sm text-gray-500 mt-1">Inventario y productos del almacén</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('almacen.recepciones.crear') }}"
                    class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-truck text-xs"></i>
                    Recepción de kit
                </a>
                <button wire:click="$dispatch('abrir-modal-producto')" type="button"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-plus text-xs"></i>
                    Agregar producto
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex gap-0" x-data>
                <button wire:click="$set('vistaActual', 'inventario')"
                    class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors"
                    :class="$wire.vistaActual === 'inventario' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                    <i class="fas fa-boxes-stacked mr-1.5"></i>
                    Inventario
                </button>
                <button wire:click="$set('vistaActual', 'catalogo')"
                    class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors"
                    :class="$wire.vistaActual === 'catalogo' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                    <i class="fas fa-list mr-1.5"></i>
                    Catálogo de productos
                </button>
            </nav>
        </div>

        {{-- ============================================================ --}}
        {{-- VISTA: INVENTARIO (Default)                                    --}}
        {{-- ============================================================ --}}
        @if ($vistaActual === 'inventario')
            {{-- Tarjetas resumen estilo dashboard --}}
            @php $c = $resumenInventario['conteos'] ?? []; @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Sellados</span>
                    <p class="text-2xl font-black text-amber-600 mt-1">{{ $c['sellados'] ?? 0 }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">En stock</span>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Utilizados</span>
                    <p class="text-2xl font-black text-orange-600 mt-1">{{ $c['utilizados'] ?? 0 }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">Kits abiertos</span>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Asignados</span>
                    <p class="text-2xl font-black text-blue-600 mt-1">{{ $c['asignados'] ?? 0 }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">En órdenes</span>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Instalados</span>
                    <p class="text-2xl font-black text-purple-600 mt-1">{{ $c['instalados'] ?? 0 }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">En vehículos</span>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Sueltos</span>
                    <p class="text-2xl font-black text-green-600 mt-1">{{ $c['sueltos'] ?? 0 }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">Piezas avulsas</span>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Por cantidad</span>
                    <p class="text-2xl font-black text-indigo-600 mt-1">{{ $c['porCantidad'] ?? 0 }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">No serializados</span>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total</span>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ $c['total'] ?? 0 }}</p>
                    <span class="text-[11px] text-gray-400 font-medium">Unidades</span>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Sede --}}
                    <div class="min-w-[180px]">
                        <select wire:model.live="filtroSedeId"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">Todas las sedes</option>
                            @foreach ($sedes as $s)
                                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Estado --}}
                    <div class="min-w-[160px]">
                        <select wire:model.live="filtroEstado"
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">Todos los estados</option>
                            <option value="en_stock">En stock</option>
                            <option value="asignado">Asignado</option>
                            <option value="abierto">Abierto</option>
                            <option value="instalado">Instalado</option>
                            <option value="defectuoso">Defectuoso</option>
                        </select>
                    </div>
                    {{-- Buscar --}}
                    <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 flex-1 min-w-[200px]">
                        <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                        <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0"
                            type="text" wire:model.live.debounce.400ms="busquedaInventario" placeholder="Buscar por nombre...">
                    </div>
                </div>
            </div>

            {{-- Resumen Kits: 3 columnas en una fila --}}
            @php
                $kitsSellados = $resumenInventario['kitsSellados'] ?? collect();
                $kitsUtilizados = $resumenInventario['kitsUtilizados'] ?? collect();
                $kitsAsignados = $resumenInventario['kitsAsignados'] ?? collect();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Sellados (VERDE) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-green-600 uppercase tracking-wider flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center">
                                <i class="fas fa-box text-green-600 text-xs"></i>
                            </div>
                            Sellados
                        </h3>
                        <span class="text-xl font-black text-green-600">{{ $kitsSellados->flatten()->count() }}</span>
                    </div>
                    <div class="space-y-2 max-h-[200px] overflow-y-auto">
                        @forelse ($kitsSellados as $productoId => $items)
                            @php $prod = $items->first()->producto; @endphp
                            <button type="button" wire:click="verDetalle({{ $productoId }}, 'sellado')"
                                class="w-full text-left bg-green-50 border border-green-200 rounded-lg p-2.5 hover:shadow-md hover:border-green-400 transition-all flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box text-green-600 text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $prod->nombre }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $items->first()->sede?->nombre ?? '—' }}</p>
                                </div>
                                <span class="px-2 py-0.5 bg-green-200 text-green-800 text-[10px] font-bold rounded-full">{{ $items->count() }}</span>
                            </button>
                        @empty
                            <p class="text-gray-400 text-xs text-center py-3">Sin kits sellados</p>
                        @endforelse
                    </div>
                </div>

                {{-- Utilizados (AMARILLO) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-amber-600 uppercase tracking-wider flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                                <i class="fas fa-box-open text-amber-600 text-xs"></i>
                            </div>
                            Utilizados
                        </h3>
                        <span class="text-xl font-black text-amber-600">{{ $kitsUtilizados->flatten()->count() }}</span>
                    </div>
                    <div class="space-y-2 max-h-[200px] overflow-y-auto">
                        @forelse ($kitsUtilizados as $productoId => $items)
                            @php $prod = $items->first()->producto; @endphp
                            <button type="button" wire:click="verDetalle({{ $productoId }}, 'utilizado')"
                                class="w-full text-left bg-amber-50 border border-amber-200 rounded-lg p-2.5 hover:shadow-md hover:border-amber-400 transition-all flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box-open text-amber-600 text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $prod->nombre }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $items->first()->sede?->nombre ?? '—' }}</p>
                                </div>
                                <span class="px-2 py-0.5 bg-amber-200 text-amber-800 text-[10px] font-bold rounded-full">{{ $items->count() }}</span>
                            </button>
                        @empty
                            <p class="text-gray-400 text-xs text-center py-3">Sin kits utilizados</p>
                        @endforelse
                    </div>
                </div>

                {{-- Asignados (AZUL) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-link text-blue-600 text-xs"></i>
                            </div>
                            Asignados
                        </h3>
                        <span class="text-xl font-black text-blue-600">{{ $kitsAsignados->flatten()->count() }}</span>
                    </div>
                    <div class="space-y-2 max-h-[200px] overflow-y-auto">
                        @forelse ($kitsAsignados as $productoId => $items)
                            @php $prod = $items->first()->producto; @endphp
                            <button type="button" wire:click="verDetalle({{ $productoId }}, 'asignado')"
                                class="w-full text-left bg-blue-50 border border-blue-200 rounded-lg p-2.5 hover:shadow-md hover:border-blue-400 transition-all flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-link text-blue-600 text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $prod->nombre }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $items->first()->sede?->nombre ?? '—' }}</p>
                                </div>
                                <span class="px-2 py-0.5 bg-blue-200 text-blue-800 text-[10px] font-bold rounded-full">{{ $items->count() }}</span>
                            </button>
                        @empty
                            <p class="text-gray-400 text-xs text-center py-3">Sin kits asignados</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Piezas sueltas --}}
            @php $sueltos = $resumenInventario['sueltos'] ?? collect(); @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-wrench text-blue-600"></i>
                    Piezas sueltas
                    <span class="text-xs font-normal text-gray-400">({{ $sueltos->flatten()->count() }} unidades)</span>
                </h3>
                @if ($sueltos->count())
                    <div class="flex flex-wrap gap-3">
                        @foreach ($sueltos as $productoId => $items)
                            @php
                                $prod = $items->first()->producto;
                                $esSerializado = $items->first()->producto->categoria->es_serializado ?? false;
                            @endphp
                            <button type="button" wire:click="verDetalle({{ $productoId }})"
                                class="text-left rounded-xl border-2 p-4 transition-all flex items-center gap-3 min-w-[200px] flex-1 max-w-[300px] hover:shadow-md {{ $esSerializado ? 'border-green-300 bg-green-50/50 hover:border-green-400' : 'border-blue-300 bg-blue-50/50 hover:border-blue-400' }}">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $esSerializado ? 'bg-green-100' : 'bg-blue-100' }}">
                                    <i class="fas {{ $esSerializado ? 'fa-barcode text-green-600' : 'fa-cubes text-blue-600' }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $prod->nombre }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $items->first()->sede?->nombre ?? '—' }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-bold rounded-full flex-shrink-0 {{ $esSerializado ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">{{ $items->count() }}</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-4">No hay piezas sueltas en esta vista.</p>
                @endif
            </div>

            {{-- Productos por cantidad --}}
            @php $porCantidad = $resumenInventario['porCantidad'] ?? collect(); @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-cubes text-purple-600"></i>
                    Productos por cantidad
                    <span class="text-xs font-normal text-gray-400">({{ $porCantidad->count() }} productos)</span>
                </h3>
                @if ($porCantidad->count())
                    <div class="flex flex-wrap gap-3">
                        @foreach ($porCantidad as $stock)
                            <div class="text-left rounded-xl border-2 border-purple-300 bg-purple-50/50 p-4 flex items-center gap-3 min-w-[200px] flex-1 max-w-[300px]">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-purple-100">
                                    <i class="fas fa-cube text-purple-600"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $stock->producto->nombre }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $stock->sede?->nombre ?? '—' }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-bold rounded-full flex-shrink-0 bg-purple-100 text-purple-800">{{ $stock->cantidad }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-4">No hay productos por cantidad en esta sede.</p>
                @endif
            </div>

            {{-- Modal de detalle de inventario --}}
            @if ($detalleProductoId)
                @php
                    $todosItems = $kitsSellados->get($detalleProductoId) ?? $kitsUtilizados->get($detalleProductoId) ?? $kitsAsignados->get($detalleProductoId) ?? $sueltos->get($detalleProductoId);
                    $prodDetalle = $todosItems?->first()->producto ?? null;
                    $tipo = $tipoKitDetalle ?? 'sellado';
                @endphp
                <div class="fixed inset-0 z-50 overflow-hidden" wire:key="modal-detalle-{{ $detalleProductoId }}">
                    <div class="fixed inset-0 bg-black/50 transition-opacity z-40" wire:click="$set('detalleProductoId', null)"></div>
                    <div class="relative z-50 flex min-h-full items-center justify-center p-4">
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col" style="max-height: 85vh;">
                            {{-- Header según tipo --}}
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between rounded-t-2xl flex-shrink-0
                                {{ $tipo === 'sellado' ? 'bg-gradient-to-r from-green-500 via-green-600 to-emerald-600' : ($tipo === 'utilizado' ? 'bg-gradient-to-r from-amber-500 via-amber-600 to-yellow-600' : 'bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600') }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm flex-shrink-0">
                                        <i class="fas {{ $tipo === 'sellado' ? 'fa-box' : ($tipo === 'utilizado' ? 'fa-box-open' : 'fa-link') }} text-white text-xl"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-lg font-bold text-white truncate">{{ $prodDetalle->nombre ?? 'Detalle' }}</h3>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 rounded-full text-xs text-white">
                                                <i class="fas fa-cubes"></i>
                                                {{ $detallesInventario->count() }} unidades
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 rounded-full text-xs text-white capitalize">
                                                <i class="fas fa-tag"></i>
                                                {{ $tipo }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button wire:click="$set('detalleProductoId', null)"
                                    class="text-white/80 hover:text-white transition-colors w-8 h-8 rounded-lg hover:bg-white/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            {{-- Contenido según tipo --}}
                            <div class="px-6 py-4 overflow-y-auto space-y-2" style="max-height: 60vh;">
                                @forelse ($detallesInventario as $item)
                                    @if ($tipo === 'sellado')
                                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] font-bold rounded-full">SELLADO</span>
                                                    @if ($item->serie)
                                                        <span class="font-mono text-sm font-bold text-gray-800">{{ $item->serie }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-green-700"><i class="fas fa-inbox mr-1"></i>{{ $item->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>{{ $item->sede?->nombre ?? '—' }}</div>
                                        </div>
                                    @elseif ($tipo === 'utilizado')
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">KIT ABIERTO</span>
                                                    @if ($item->serie)
                                                        <span class="font-mono text-sm font-bold text-gray-800">{{ $item->serie }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-amber-700"><i class="fas fa-calendar mr-1"></i>{{ $item->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="mt-2 text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>{{ $item->sede?->nombre ?? '—' }}</div>

                                            {{-- Piezas que salieron del kit --}}
                                            @if ($item->piezasEnKit && $item->piezasEnKit->count())
                                                <div class="mt-3 pt-3 border-t border-amber-200">
                                                    <p class="text-[10px] text-amber-700 font-bold uppercase mb-2">Piezas extraídas ({{ $item->piezasEnKit->count() }})</p>
                                                    <div class="space-y-2">
                                                        @foreach ($item->piezasEnKit as $pieza)
                                                            <div class="bg-white rounded-lg p-2.5 border border-amber-100 text-xs">
                                                                <div class="flex items-center justify-between">
                                                                    <div class="flex items-center gap-2">
                                                                        @if ($pieza->serie)
                                                                            <span class="font-mono text-amber-800 font-bold">{{ $pieza->serie }}</span>
                                                                        @endif
                                                                        <span class="text-gray-700">{{ $pieza->producto?->nombre ?? 'Pieza' }}</span>
                                                                    </div>
                                                                    @php
                                                                        $coloresEstado = [
                                                                            'en_stock' => 'bg-green-100 text-green-700',
                                                                            'asignado' => 'bg-blue-100 text-blue-700',
                                                                            'instalado' => 'bg-purple-100 text-purple-700',
                                                                            'abierto' => 'bg-amber-100 text-amber-700',
                                                                        ];
                                                                    @endphp
                                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $coloresEstado[$pieza->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                                                        {{ ucfirst($pieza->estado) }}
                                                                    </span>
                                                                </div>
                                                                @if ($pieza->serviceOrder)
                                                                    <div class="mt-1.5 pt-1.5 border-t border-gray-100 flex items-center gap-3 text-[10px] text-gray-500">
                                                                        <span><i class="fas fa-file-alt mr-1"></i>Orden #{{ $pieza->serviceOrder->id }}</span>
                                                                        @if ($pieza->serviceOrder?->cliente)
                                                                            <span><i class="fas fa-user mr-1"></i>{{ $pieza->serviceOrder->cliente->nombre }}</span>
                                                                        @endif
                                                                        @if ($pieza->vehiculoInstalado)
                                                                            <span><i class="fas fa-car mr-1"></i>{{ $pieza->vehiculoInstalado->placa }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <p class="mt-2 text-xs text-amber-600 italic">Sin piezas registradas aún</p>
                                            @endif
                                        </div>
                                    @elseif ($tipo === 'asignado')
                                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-full">ASIGNADO</span>
                                                    @if ($item->serie)
                                                        <span class="font-mono text-sm font-bold text-gray-800">{{ $item->serie }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-blue-700"><i class="fas fa-calendar mr-1"></i>{{ $item->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            @if ($item->serviceOrder)
                                                <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                                                    <div class="bg-white rounded-lg p-2 border border-blue-100">
                                                        <span class="text-[10px] text-gray-400 uppercase font-bold">Orden</span>
                                                        <p class="font-semibold text-gray-800">#{{ $item->serviceOrder->id }}</p>
                                                    </div>
                                                    <div class="bg-white rounded-lg p-2 border border-blue-100">
                                                        <span class="text-[10px] text-gray-400 uppercase font-bold">Cliente</span>
                                                        <p class="font-semibold text-gray-800">{{ $item->serviceOrder?->cliente?->nombre ?? '—' }}</p>
                                                    </div>
                                                </div>
                                                @if ($item->vehiculoInstalado)
                                                    <div class="mt-2 bg-white rounded-lg p-2 border border-blue-100 text-xs">
                                                        <span class="text-[10px] text-gray-400 uppercase font-bold">Vehículo</span>
                                                        <p class="font-semibold text-gray-800">{{ $item->vehiculoInstalado->placa ?? '—' }} — {{ $item->vehiculoInstalado->marca ?? '' }} {{ $item->vehiculoInstalado->modelo ?? '' }}</p>
                                                    </div>
                                                @endif
                                            @endif
                                            <div class="mt-2 text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>{{ $item->sede?->nombre ?? '—' }}</div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono text-gray-400">#{{ $item->id }}</span>
                                                    @if ($item->serie)
                                                        <span class="font-medium text-gray-700">{{ $item->serie }}</span>
                                                    @endif
                                                </div>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">{{ ucfirst($item->estado) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <p class="text-gray-400 text-sm text-center py-6">Sin unidades para mostrar.</p>
                                @endforelse
                            </div>
                            <div class="px-6 py-3 border-t border-gray-100 flex justify-end bg-gray-50 rounded-b-2xl flex-shrink-0">
                                <button wire:click="$set('detalleProductoId', null)"
                                    class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- ============================================================ --}}
        {{-- VISTA: CATÁLOGO                                               --}}
        {{-- ============================================================ --}}
        @if ($vistaActual === 'catalogo')
            {{-- Filtros --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
                <div class="px-5 py-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 flex-1 min-w-[200px]">
                            <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                            <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0"
                                type="text" wire:model.live.debounce.400ms="buscar" placeholder="Buscar por nombre o código...">
                        </div>
                        <button type="button" x-on:click="expandir = !expandir"
                            class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                            <i class="fas fa-filter"></i>
                            Más filtros
                            <i class="fas fa-chevron-down text-[10px] transition-transform" x-bind:class="expandir ? 'rotate-180' : ''"></i>
                        </button>
                        <button type="button" wire:click="resetFilters"
                            class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                            <i class="fas fa-eraser"></i>
                            Limpiar
                        </button>
                    </div>
                    <div x-show="expandir" x-collapse>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Categoría</label>
                                <select wire:model.live="categoriaId"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="todas">Todas las categorías</option>
                                    <option value="solo_kits">📦 Solo Kits</option>
                                    @foreach ($categorias as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Stock</label>
                                <select wire:model.live="filterStock"
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="todos">Todos</option>
                                    <option value="bajo">Stock bajo</option>
                                    <option value="sin">Sin stock</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Proveedor</label>
                                <input type="text" wire:model.live="filterProveedor" placeholder="Buscar proveedor..."
                                    class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tarjetas --}}
            @if ($productos->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($productos as $p)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow flex flex-col">
                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $p->nombre }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $p->categoria->nombre }}</p>
                                    @if($p->marca)
                                        <p class="text-xs text-gray-400">{{ $p->marca }}</p>
                                    @endif
                                </div>
                                @if($p->categoria->es_kit)
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full flex-shrink-0 ml-2">KIT</span>
                                @endif
                            </div>

                            {{-- Stock --}}
                            <div class="mb-4">
                                @php
                                    $stock = $p->categoria->es_kit ? $p->stockTotal() : $p->stock_disponible;
                                    $stockColor = $stock > 0 ? 'text-green-600' : 'text-red-500';
                                @endphp
                                <p class="text-2xl font-bold {{ $stockColor }}">{{ $stock }}</p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider">disponible</p>
                            </div>

                            {{-- Acciones --}}
                            <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    @if($p->categoria->es_kit)
                                        <button wire:click="$dispatch('ver-componentes-kit', { productoId: {{ $p->id }} })"
                                            class="w-8 h-8 flex items-center justify-center text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors" title="Ver componentes">
                                            <i class="fa-solid fa-puzzle-piece text-xs"></i>
                                        </button>
                                    @endif
                                    <button wire:click="$dispatch('abrir-modal-entrada', { productoId: {{ $p->id }} })"
                                        class="w-8 h-8 flex items-center justify-center text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors" title="Registrar entrada">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                    <button wire:click="$dispatch('abrir-modal-editar-producto', { productoId: {{ $p->id }} })"
                                        class="w-8 h-8 flex items-center justify-center text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" title="Editar">
                                        <i class="fa-solid fa-edit text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $productos->links('pagination::tailwind') }}
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box-open text-indigo-500 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">No hay productos registrados</p>
                        <p class="text-gray-400 text-xs mt-1">Crea un nuevo producto para comenzar</p>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Modals --}}
    <livewire:almacen.productos.crear />
    <livewire:almacen.productos.registrar-entrada />
    <livewire:almacen.productos.editar />

    {{-- Kit components modal --}}
    <div x-data="kitComponentsModal"
         x-on:ver-componentes-kit.window="abierto = true; productoId = event.detail.productoId; cargarComponentes();"
         x-on:keydown.escape.window="abierto = false"
         x-show="abierto" x-cloak
         class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <div class="fixed inset-0 bg-black/50 transition-opacity" x-show="abierto" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-on:click="abierto = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all" x-show="abierto"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-indigo-500 via-indigo-600 to-purple-600 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-puzzle-piece text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white" x-text="producto?.nombre || 'Kit'"></h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 rounded-full text-xs text-white">
                                    <i class="fas fa-cubes"></i>
                                    <span x-text="componentes.length + ' piezas'"></span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 rounded-full text-xs text-white">
                                    <i class="fas fa-check-circle"></i>
                                    <span x-text="componentes.filter(c => c.es_serializado).length + ' serial.'"></span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 rounded-full text-xs text-white">
                                    <i class="fas fa-hashtag"></i>
                                    <span x-text="componentes.filter(c => !c.es_serializado).length + ' cantidad'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button x-on:click="abierto = false" class="text-white/80 hover:text-white transition-colors w-8 h-8 rounded-lg hover:bg-white/20 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-4">
                    <template x-if="cargando">
                        <div class="text-center py-8">
                            <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto"></div>
                            <p class="text-gray-500 mt-3 text-sm">Cargando componentes...</p>
                        </div>
                    </template>
                    <template x-if="!cargando && componentes.length === 0">
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-puzzle-piece text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Sin componentes definidos</p>
                        </div>
                    </template>
                    <template x-if="!cargando && componentes.length > 0">
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="(comp, index) in componentes" :key="index">
                                <div class="flex items-center gap-2 p-2.5 rounded-xl transition-all duration-200 hover:shadow-md border min-h-[60px] relative"
                                     :class="getConfig(comp.nombre).bg">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm bg-gradient-to-br"
                                         :class="getConfig(comp.nombre).color">
                                        <i class="fas text-white text-sm" :class="getConfig(comp.nombre).icon"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800 text-xs leading-tight break-words" x-text="comp.nombre"></p>
                                    </div>
                                    <template x-if="comp.cantidad > 1">
                                        <div class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 rounded-full flex items-center justify-center text-[10px] font-bold bg-gray-800 text-white shadow"
                                             x-text="'×' + comp.cantidad"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="px-6 py-3 border-t border-gray-100 flex justify-end bg-gray-50 rounded-b-2xl">
                    <button x-on:click="abierto = false" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kitComponentsModal', () => ({
                abierto: false,
                productoId: null,
                producto: null,
                componentes: [],
                cargando: false,
                configComponentes: {
                    'vaporizador': { icon: 'fa-cloud', color: 'from-cyan-500 to-cyan-600', bg: 'bg-cyan-50 border-cyan-200' },
                    'toma': { icon: 'fa-gas-pump', color: 'from-amber-500 to-amber-600', bg: 'bg-amber-50 border-amber-200' },
                    'valvula': { icon: 'fa-circle-dot', color: 'from-red-500 to-red-600', bg: 'bg-red-50 border-red-200' },
                    'manometro': { icon: 'fa-gauge-high', color: 'from-emerald-500 to-emerald-600', bg: 'bg-emerald-50 border-emerald-200' },
                    'regulador': { icon: 'fa-sliders', color: 'from-orange-500 to-orange-600', bg: 'bg-orange-50 border-orange-200' },
                    'electrovalvula': { icon: 'fa-plug', color: 'from-violet-500 to-violet-600', bg: 'bg-violet-50 border-violet-200' },
                    'conmutador': { icon: 'fa-toggle-on', color: 'from-indigo-500 to-indigo-600', bg: 'bg-indigo-50 border-indigo-200' },
                    'terminal': { icon: 'fa-link', color: 'from-slate-500 to-slate-600', bg: 'bg-slate-50 border-slate-200' },
                    'relay': { icon: 'fa-bolt', color: 'from-yellow-500 to-yellow-600', bg: 'bg-yellow-50 border-yellow-200' },
                    'electronica': { icon: 'fa-microchip', color: 'from-pink-500 to-pink-600', bg: 'bg-pink-50 border-pink-200' },
                    'computadora': { icon: 'fa-laptop', color: 'from-blue-500 to-blue-600', bg: 'bg-blue-50 border-blue-200' },
                    'filtro': { icon: 'fa-filter', color: 'from-teal-500 to-teal-600', bg: 'bg-teal-50 border-teal-200' },
                    'sensor': { icon: 'fa-satellite-dish', color: 'from-fuchsia-500 to-fuchsia-600', bg: 'bg-fuchsia-50 border-fuchsia-200' },
                    'ramal': { icon: 'fa-bolt-lightning', color: 'from-lime-500 to-lime-600', bg: 'bg-lime-50 border-lime-200' },
                    'fusible': { icon: 'fa-shield-halved', color: 'from-rose-500 to-rose-600', bg: 'bg-rose-50 border-rose-200' },
                    'porta': { icon: 'fa-shield-halved', color: 'from-rose-500 to-rose-600', bg: 'bg-rose-50 border-rose-200' },
                    'inyector': { icon: 'fa-syringe', color: 'from-red-500 to-red-600', bg: 'bg-red-50 border-red-200' },
                    'manguera': { icon: 'fa-wave-square', color: 'from-sky-500 to-sky-600', bg: 'bg-sky-50 border-sky-200' },
                    'caneria': { icon: 'fa-grip-lines', color: 'from-gray-500 to-gray-600', bg: 'bg-gray-50 border-gray-200' },
                    'conos': { icon: 'fa-caret-up', color: 'from-stone-500 to-stone-600', bg: 'bg-stone-50 border-stone-200' },
                    'agua': { icon: 'fa-droplet', color: 'from-blue-400 to-blue-500', bg: 'bg-blue-50 border-blue-200' },
                    'niple': { icon: 'fa-screwdriver-wrench', color: 'from-zinc-500 to-zinc-600', bg: 'bg-zinc-50 border-zinc-200' },
                    'tapon': { icon: 'fa-plug-circle-xmark', color: 'from-neutral-500 to-neutral-600', bg: 'bg-neutral-50 border-neutral-200' },
                    'venteo': { icon: 'fa-wind', color: 'from-sky-400 to-sky-500', bg: 'bg-sky-50 border-sky-200' },
                    'abrazadera': { icon: 'fa-circle-nodes', color: 'from-amber-600 to-amber-700', bg: 'bg-amber-50 border-amber-200' },
                    'corrugado': { icon: 'fa-bezier-curve', color: 'from-gray-400 to-gray-500', bg: 'bg-gray-50 border-gray-200' },
                },
                getDefaultConfig() {
                    return { icon: 'fa-cube', color: 'from-gray-400 to-gray-500', bg: 'bg-gray-50 border-gray-200', text: 'text-gray-500' };
                },
                getConfig(nombre) {
                    if (!nombre) return this.getDefaultConfig();
                    const lower = nombre.toLowerCase().replace(/á/g, 'a').replace(/é/g, 'e').replace(/í/g, 'i').replace(/ó/g, 'o').replace(/ú/g, 'u').replace(/ñ/g, 'n');
                    for (const [key, config] of Object.entries(this.configComponentes)) {
                        const keyLower = key.toLowerCase().replace(/á/g, 'a').replace(/é/g, 'e').replace(/í/g, 'i').replace(/ó/g, 'o').replace(/ú/g, 'u').replace(/ñ/g, 'n');
                        if (lower.includes(keyLower)) return config;
                    }
                    return this.getDefaultConfig();
                },
                async cargarComponentes() {
                    if (!this.productoId) return;
                    this.cargando = true;
                    this.componentes = [];
                    try {
                        const response = await fetch(`/api/kit-componentes/${this.productoId}`);
                        const data = await response.json();
                        if (data && data.length > 0) {
                            this.componentes = data;
                            this.producto = { nombre: 'Kit' };
                        }
                    } catch (e) {
                        console.error('Error cargando componentes:', e);
                    } finally {
                        this.cargando = false;
                    }
                }
            }));
        });
    </script>
</div>