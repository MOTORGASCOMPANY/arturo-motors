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

            {{-- Kits sellados --}}
            @php $kits = $resumenInventario['kits'] ?? collect(); @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-box text-amber-600"></i>
                    Kits sellados
                    <span class="text-xs font-normal text-gray-400">({{ $kits->flatten()->count() }} unidades)</span>
                </h3>
                @if ($kits->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($kits as $productoId => $items)
                            @php $prod = $items->first()->producto; @endphp
                            <button type="button" wire:click="verDetalle({{ $productoId }})"
                                class="text-left bg-white rounded-xl border-2 border-amber-200 bg-amber-50/40 p-4 hover:shadow-md hover:border-amber-400 transition-all flex flex-col">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-cube text-indigo-600"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod->nombre }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $items->first()->sede?->nombre ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full flex-shrink-0 ml-2">KIT</span>
                                </div>
                                <div class="mt-auto pt-3 border-t border-amber-100 flex items-center justify-between">
                                    <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                                        {{ $items->count() }} unidades
                                    </span>
                                    <span class="text-xs text-indigo-600 font-semibold">
                                        Ver detalle →
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-4">No hay kits en esta vista.</p>
                @endif
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($sueltos as $productoId => $items)
                            @php
                                $prod = $items->first()->producto;
                                $esSerializado = $items->first()->producto->categoria->es_serializado ?? false;
                            @endphp
                            <button type="button" wire:click="verDetalle({{ $productoId }})"
                                class="text-left rounded-xl border-2 p-4 transition-all flex flex-col hover:shadow-md {{ $esSerializado ? 'border-green-300 bg-green-50/50 hover:border-green-400' : 'border-blue-300 bg-blue-50/50 hover:border-blue-400' }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $esSerializado ? 'bg-green-100' : 'bg-blue-100' }}">
                                            <i class="fas {{ $esSerializado ? 'fa-barcode text-green-600' : 'fa-cubes text-blue-600' }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-800 truncate">{{ $prod->nombre }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $items->first()->sede?->nombre ?? '—' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $esSerializado ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800' }}">
                                        {{ $esSerializado ? 'Pieza suelta serializada' : 'Pieza suelta' }}
                                    </span>
                                </div>
                                <div class="mt-auto pt-3 border-t {{ $esSerializado ? 'border-green-100' : 'border-blue-100' }} flex items-center justify-between">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $esSerializado ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $items->count() }} unidades
                                    </span>
                                    <span class="text-xs text-indigo-600 font-semibold">
                                        Ver detalle →
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-sm text-center py-4">No hay piezas sueltas en esta vista.</p>
                @endif
            </div>

            {{-- Modal de detalle de inventario --}}
            @if ($detalleProductoId)
                @php
                    $todosItems = $kits->get($detalleProductoId) ?? $sueltos->get($detalleProductoId);
                    $prodDetalle = $todosItems?->first()->producto ?? null;
                    $esKitDetalle = $kits->has($detalleProductoId);
                @endphp
                <div class="fixed inset-0 z-50 overflow-hidden" wire:key="modal-detalle-{{ $detalleProductoId }}">
                    <div class="fixed inset-0 bg-black/50 transition-opacity z-40" wire:click="$set('detalleProductoId', null)"></div>
                    <div class="relative z-50 flex min-h-full items-center justify-center p-4">
                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col" style="max-height: 85vh;">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-indigo-500 via-indigo-600 to-purple-600 rounded-t-2xl flex-shrink-0">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm flex-shrink-0">
                                        <i class="fas {{ $esKitDetalle ? 'fa-box' : 'fa-wrench' }} text-white text-xl"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-lg font-bold text-white truncate">{{ $prodDetalle->nombre ?? 'Detalle' }}</h3>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-white/20 rounded-full text-xs text-white">
                                                <i class="fas fa-cubes"></i>
                                                {{ $detallesInventario->count() }} unidades
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button wire:click="$set('detalleProductoId', null)"
                                    class="text-white/80 hover:text-white transition-colors w-8 h-8 rounded-lg hover:bg-white/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="px-6 py-4 overflow-y-auto space-y-1.5">
                                @forelse ($detallesInventario as $item)
                                    <div class="flex items-center justify-between text-xs bg-gray-50 rounded-lg px-3 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-gray-400">#{{ $item->id }}</span>
                                            @if ($item->serie)
                                                <span class="font-medium text-gray-700">{{ $item->serie }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-500">{{ $item->sede?->nombre }}</span>
                                            @php
                                                $colors = ['en_stock' => 'bg-green-100 text-green-700', 'asignado' => 'bg-blue-100 text-blue-700', 'abierto' => 'bg-amber-100 text-amber-700', 'instalado' => 'bg-purple-100 text-purple-700', 'defectuoso' => 'bg-red-100 text-red-700'];
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $colors[$item->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ ucfirst($item->estado) }}
                                            </span>
                                        </div>
                                    </div>
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
                    {{ $productos->links() }}
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