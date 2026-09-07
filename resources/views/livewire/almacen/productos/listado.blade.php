<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-indigo-600"></i>
                Productos de almacén
            </h2>
            <p class="text-sm text-gray-500 mt-1">Gestión de inventario y productos del almacén</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
            <div class="px-5 py-4">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Buscar -->
                    <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 flex-1 min-w-[200px]">
                        <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                        <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 focus:outline-none"
                            type="text" wire:model.live.debounce.400ms="buscar" placeholder="Buscar por nombre o código...">
                    </div>

                    <!-- Botón filtros -->
                    <button type="button" x-on:click="expandir = !expandir"
                        class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-filter"></i>
                        Más filtros
                        <i class="fas fa-chevron-down text-[10px] transition-transform" x-bind:class="expandir ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Botón limpiar -->
                    <button type="button" wire:click="resetFilters"
                        class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                </div>

                <!-- Filtros expandidos -->
                <div x-show="expandir" x-collapse>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100">
                        <!-- Categoría -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Categoría</label>
                            <select wire:model.live="categoriaId"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="todas">Todas las categorías</option>
                                @foreach ($categorias as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Stock</label>
                            <select wire:model.live="filterStock"
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="todos">Todos</option>
                                <option value="bajo">Stock bajo</option>
                                <option value="sin">Sin stock</option>
                            </select>
                        </div>

                        <!-- Proveedor -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Proveedor</label>
                            <input type="text" wire:model.live="filterProveedor" placeholder="Buscar proveedor..."
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones superiores -->
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button wire:click="$dispatch('abrir-modal-producto')" type="button"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-plus text-xs"></i>
                    Nuevo producto
                </button>
            </div>
        </div>

        <!-- Tabla -->
        @if ($productos->count())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Producto
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Categoría
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Marca
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Disponible
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($productos as $p)
                                <tr class="hover:bg-gray-50 transition-colors" wire:key="producto-{{ $p->id }}">
                                    <td class="px-4 py-3 text-center border-r border-gray-100 font-medium">{{ $p->nombre }}</td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">{{ $p->categoria->nombre }}</td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">{{ $p->marca ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100 font-semibold">{{ $p->stock_disponible }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="$dispatch('abrir-modal-entrada', { productoId: {{ $p->id }} })" type="button"
                                                class="text-blue-600 hover:text-blue-800 text-xs font-semibold transition-colors">
                                                Entrada →
                                            </button>
                                            @if ($p->categoria->es_kit)
                                                <button wire:click="$dispatch('abrir-modal-componentes', { productoId: {{ $p->id }} })" type="button"
                                                    class="text-purple-600 hover:text-purple-800 text-xs font-semibold transition-colors">
                                                    Componentes →
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $productos->links() }}
                </div>
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
    </div>

    <livewire:almacen.productos.crear />
    <livewire:almacen.productos.registrar-entrada />
    <livewire:almacen.productos.definir-componentes />
</div>