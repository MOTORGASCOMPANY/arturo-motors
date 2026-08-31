<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-5xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-6 md:block sm:block">
                <!-- Titulo y subtitulo -->
                <div class="px-2 w-full mb-4">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-tools mr-2"></i>Productos de almacén
                    </h2>
                    <span class="text-xs">Todos los productos de almacén</span>
                </div>
                <!-- Filtros -->
                <div class="w-full flex flex-wrap items-center justify-between gap-4">
                    <!-- Buscar -->
                    <div class="flex bg-gray-50 items-center w-full lg:w-2/6 p-2 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                        <input class="bg-gray-50 outline-none block rounded-md border-indigo-500 w-full border-none focus:ring-0"
                            type="text" wire:model.live.debounce.400ms="buscar" placeholder="Buscar...">
                    </div> 
                    <!-- Filtro Estado -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <x-label class="mr-2" value="Categorias" />
                        <select wire:model.live="categoriaId"
                            class="border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none cursor-pointer">
                            <option value="todas">Todas las categorías</option>
                            @foreach ($categorias as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Boton crear -->
                    <div>
                        <button wire:click="$dispatch('abrir-modal-producto')" type="button"
                            class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold tracking-wide cursor-pointer hover:bg-indigo-600 transition">
                            Nuevo producto &nbsp;<i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            @if ($productos->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Producto
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Categoría
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Marca
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Disponible
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $p)
                                <tr wire:key="producto-{{ $p->id }}">
                                    <td class="px-4 py-3 font-medium border-b border-gray-200 bg-white text-sm">{{ $p->nombre }}</td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">{{ $p->categoria->nombre }}</td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">{{ $p->marca ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold border-b border-gray-200 bg-white text-sm">{{ $p->stock_disponible }}</td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm">
                                        {{--<a href="{{ route('almacen.productos.entrada', $p->id) }}" class="text-blue-600 text-xs font-semibold">Registrar entrada →</a>--}}
                                        <button wire:click="$dispatch('abrir-modal-entrada', { productoId: {{ $p->id }} })" type="button"
                                                class="text-blue-600 text-xs font-semibold">
                                            Registrar entrada →
                                        </button>
                                        @if ($p->categoria->es_kit)                                        
                                            <button wire:click="$dispatch('abrir-modal-componentes', { productoId: {{ $p->id }} })" type="button"
                                                    class="text-purple-600 text-xs font-semibold mr-2">
                                                Definir componentes →
                                            </button>
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200/60">
                    {{ $productos->links() }}
                </div>
            @else
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No hay productos registrados.
                </div>
            @endif
        </div>
    </div>

    <livewire:almacen.productos.crear />
    <livewire:almacen.productos.registrar-entrada />
    <livewire:almacen.productos.definir-componentes />
</div>