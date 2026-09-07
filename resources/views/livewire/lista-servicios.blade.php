<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-cogs text-orange-600"></i> Servicios
            </h2>
            <p class="text-xs text-gray-500 mt-1">Gestión de servicios del taller</p>
        </div>
        <button wire:click="openCreateModal()"
            class="px-4 py-2 text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> Nuevo Servicio
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
        <div class="p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <x-label for="search" value="Buscar" class="text-gray-600 font-semibold mb-1 text-xs" />
                    <x-input id="search" type="text" wire:model.live="search" placeholder="Nombre o tipo..." class="w-full" />
                </div>
                <div>
                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Tipo</x-label>
                    <select wire:model.live="filterTipo" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                        <option value="">Todos</option>
                        <option value="simple">Simple</option>
                        <option value="conversion">Conversión</option>
                    </select>
                </div>
                <div>
                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Estado</x-label>
                    <select wire:model.live="filterActivo" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                        <option value="todos">Todos</option>
                        <option value="si">Activos</option>
                        <option value="no">Inactivos</option>
                    </select>
                </div>
                <button wire:click="limpiarFiltros"
                    class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($servicios->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">#</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Nombre</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Tipo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Precio Base</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($servicios as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <div class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-orange-100 text-orange-700 text-xs font-bold">
                                        {{ $loop->iteration }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    {{ $item->nombre }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    @if($item->tipo == 'conversion')
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                                            Conversión GNV
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                            Simple
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    S/. {{ number_format($item->precio_base, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    @if($item->activo)
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="edit({{ $item->id }})"
                                        class="px-2.5 py-1 text-[11px] font-semibold bg-orange-100 text-orange-700 hover:bg-orange-200 rounded-lg transition-colors"
                                        title="Editar servicio">
                                        <i class="fas fa-pen-to-square mr-0.5"></i> Editar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $servicios->withQueryString()->links() }}
            </div>
        @else
            <div class="px-6 py-10 text-center">
                <div class="w-16 h-16 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-cogs text-2xl"></i>
                </div>
                <p class="text-gray-600 font-semibold">No hay servicios registrados</p>
                <p class="text-xs text-gray-400 mt-1">Aún no se han registrado servicios en el sistema.</p>
            </div>
        @endif
    </div>

    <!-- Modal para editar servicio -->
    <x-dialog-modal wire:model="open" wire:loading.attr="disabled">
        <x-slot name="title">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-pen text-white"></i> &nbsp;Editar Servicio</h1>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-label for="nombre" value="Nombre del Servicio" />
                    <x-input id="nombre" type="text" class="mt-1 block w-full" wire:model="nombre" placeholder="Ej: Conversión GNV" />
                    @error('nombre')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="tipo" value="Tipo" />
                    <select id="tipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model="tipo">
                        <option value="simple">Simple</option>
                        <option value="conversion">Conversión GNV</option>
                    </select>
                    @error('tipo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="precio_base" value="Precio Base (S/.)" />
                    <x-input id="precio_base" type="number" step="0.01" min="0" class="mt-1 block w-full" wire:model="precio_base" placeholder="0.00" />
                    @error('precio_base')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="activo" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm font-semibold text-gray-700">Activo</span>
                    </label>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)" class="mx-2">
                Cancelar
            </x-secondary-button>
            <x-button wire:click="updateService" wire:loading.attr="disabled" wire:target="updateService">
                Actualizar
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Modal para crear servicio -->
    <x-dialog-modal wire:model="openCreate" wire:loading.attr="disabled">
        <x-slot name="title">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-plus text-white"></i> &nbsp;Nuevo Servicio</h1>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-label for="createNombre" value="Nombre del Servicio" />
                    <x-input id="createNombre" type="text" class="mt-1 block w-full" wire:model="createNombre" placeholder="Ej: Conversión GNV" />
                    @error('createNombre')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createTipo" value="Tipo" />
                    <select id="createTipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model="createTipo">
                        <option value="simple">Simple</option>
                        <option value="conversion">Conversión GNV</option>
                    </select>
                    @error('createTipo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createPrecio_base" value="Precio Base (S/.)" />
                    <x-input id="createPrecio_base" type="number" step="0.01" min="0" class="mt-1 block w-full" wire:model="createPrecio_base" placeholder="0.00" />
                    @error('createPrecio_base')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="createActivo" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm font-semibold text-gray-700">Activo</span>
                    </label>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('openCreate', false)" class="mx-2">
                Cancelar
            </x-secondary-button>
            <x-button wire:click="storeService" wire:loading.attr="disabled" wire:target="storeService">
                Guardar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
