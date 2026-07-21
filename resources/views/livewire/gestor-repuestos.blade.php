<div class="flex box-border">
    <div class="container mx-auto py-4">
        {{--
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Repuestos</h1>
            <x-button wire:click="$toggle('open_form')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nuevo Repuesto
            </x-button>
        </div>
        --}}
        {{-- Tabla de repuestos --}}
        <x-custom-table>
            <x-slot name="titulo">
                <h2 class="text-gray-600 font-semibold text-2xl">Almacen</h2>
                <span class="text-xs text-gray-500">Todos los repuestos y accesorios</span>
            </x-slot>
            <x-slot name="btnAgregar">
                <x-button wire:click="$toggle('open_form')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Nuevo Repuesto
                </x-button>
            </x-slot>
            <x-slot name="contenido">
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descripción
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stock
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($repuestos as $repuesto)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $repuesto->nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ Str::limit($repuesto->descripcion, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ number_format($repuesto->precio, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $repuesto->stock }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <x-button wire:click="edit({{ $repuesto->id }})" class="mr-2">
                                            Editar
                                        </x-button>
                                        <x-danger-button wire:click="confirmDelete({{ $repuesto->id }})">
                                            Eliminar
                                        </x-danger-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        No se encontraron repuestos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-slot>
        </x-custom-table>

        {{-- Formulario de creación/edición --}}
        <x-dialog-modal wire:model="open_form">
            <x-slot name="title">
                {{ $repuesto_id ? 'Editar Repuesto' : 'Crear Nuevo Repuesto' }}
            </x-slot>
            <x-slot name="content">
                <div class="space-y-4">
                    {{-- Nombre --}}
                    <div>
                        <x-label for="nombre" value="Nombre" />
                        <x-input id="nombre" type="text" wire:model.blur="nombre" class="mt-1 block w-full" />
                        <x-input-error for="nombre" class="mt-2" />
                    </div>
                    {{-- Descripción --}}
                    <div>
                        <x-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" wire:model.blur="descripcion"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <x-input-error for="descripcion" class="mt-2" />
                    </div>
                    {{-- Precio --}}
                    <div>
                        <x-label for="precio" value="Precio" />
                        <x-input id="precio" type="number" step="0.01" wire:model.blur="precio"
                            class="mt-1 block w-full" />
                        <x-input-error for="precio" class="mt-2" />
                    </div>
                    {{-- Stock --}}
                    <div>
                        <x-label for="stock" value="Stock" />
                        <x-input id="stock" type="number" wire:model.blur="stock" class="mt-1 block w-full" />
                        <x-input-error for="stock" class="mt-2" />
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="resetForm" class="mr-2">
                    Cancelar
                </x-secondary-button>
                <x-button wire:click="save" wire:loading.attr="disabled" class="">
                    Guardar
                </x-button>
            </x-slot>
        </x-dialog-modal>

        {{-- Modal de confirmación de eliminación --}}
        <x-confirmation-modal wire:model="confirming_delete">
            <x-slot name="title">
                Confirmar Eliminación
            </x-slot>

            <x-slot name="content">
                ¿Estás seguro de que deseas eliminar este repuesto? Esta acción no se puede deshacer.
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirming_delete')">
                    Cancelar
                </x-secondary-button>
                <x-danger-button class="ml-2" wire:click="delete">
                    Eliminar
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    </div>
</div>
