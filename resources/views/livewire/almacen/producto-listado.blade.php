<div>
    <div class="max-w-7xl mx-auto px-4 py-5 space-y-4">

        <div class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-sm -mx-4 px-4 pt-1.5 pb-2.5 space-y-2.5 border-b border-gray-200/70">

            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex items-center gap-3">
                    <span class="hidden sm:flex w-11 h-11 rounded-xl bg-indigo-50 items-center justify-center shrink-0">
                        <i class="fas fa-store text-indigo-600"></i>
                    </span>
                    <div class="min-w-0">
                        <h2 class="text-xl font-bold text-gray-800 tracking-tight">
                            Almacén
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Inventario y productos del almacén</p>
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

            <div class="bg-white rounded-xl border border-gray-200/70 p-2 flex flex-wrap items-center gap-2">

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
            <x-almacen.inventario-dashboard />

            <x-almacen.inventario-listado-modal />

            <x-almacen.detalle-kit-modal />

            <livewire:almacen.producto-detalle-pieza />

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
                <x-ui.empty-state icon="fa-box-open" message="No hay productos registrados" />
            @endif
        @endif

    </div>

    <livewire:almacen.producto-alta />
    <livewire:almacen.producto-entrada />
    <livewire:almacen.producto-modificacion />

    <livewire:almacen.producto-completar-kit />

    <livewire:almacen.producto-modificacion-item />


</div>