<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-3xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <!-- Encabezado con título a la izquierda y botón a la derecha -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6">
                <!-- Título y subtítulo -->
                <div class="px-2">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-layer-group mr-2"></i>Categorías de almacén
                    </h2>
                    <span class="text-xs">Tipos de equipos y repuestos que maneja el taller</span>
                </div>
                <!-- Botón alineado a la derecha en la misma fila -->
                <div>
                    <a wire:click="$dispatch('abrir-modal-categoria')" type="button" class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold tracking-wide cursor-pointer hover:bg-indigo-600 transition inline-block">
                        Nueva categoría &nbsp;<i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>

            <!-- Tabla -->
            @if ($categorias->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Nombre
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Tipo
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Atributos
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Productos
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categorias as $c)
                                <tr wire:key="categoria-{{ $c->id }}">
                                    <td class="px-4 py-3 font-medium border-b border-gray-200 bg-white text-sm">{{ $c->nombre }}</td>
                                    <td class="px-4 py-3 text-center border-b border-gray-200 bg-white text-sm">
                                        @if($c->es_kit)
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Kit</span>
                                        @elseif($c->es_serializado)
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Serializado</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Por cantidad</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 border-b border-gray-200 bg-white text-sm">
                                        @php
                                            $attrs = is_array($c->esquema_atributos) ? $c->esquema_atributos : (json_decode($c->esquema_atributos, true) ?? []);
                                            $parts = [];
                                            foreach ($attrs as $key => $val) {
                                                $oined = is_array($val) ? implode('/', $val) : $val;
                                                $parts[] = "{$key}: {$oined}";
                                            }
                                        @endphp
                                        {{ count($parts) ? implode(', ', $parts) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm">{{ $c->productos_count }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <!-- Botón 1: Editar -->
                                            <div class="relative inline-block group">
                                                <a wire:click="$dispatch('abrir-modal-editar-categoria', { categoriaId: {{ $c->id }} })"
                                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-900 rounded-lg transition-colors duration-150">
                                                    <i class="fa-solid fa-edit text-xs"></i>
                                                </a>                                                
                                                <!-- Tooltip 1 -->
                                                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center pointer-events-none z-10">
                                                    <span class="relative z-10 p-1.5 text-[10px] font-semibold leading-none text-white whitespace-nowrap bg-gray-800 rounded shadow-md">
                                                        Editar
                                                    </span>
                                                    <div class="w-2 h-2 -mt-1 rotate-45 bg-gray-800"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No hay categorias registrados.
                </div>
            @endif
        </div>
    </div>

    <livewire:almacen.categorias.crear />
    <livewire:almacen.categorias.editar />
</div>