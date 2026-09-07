<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-amber-600"></i> Clientes
            </h2>
            <p class="text-xs text-gray-500 mt-1">Todos los registros de clientes</p>
        </div>
        <button wire:click="openCreateModal()"
            class="px-4 py-2 text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> Agregar
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
        <div class="p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <x-label for="search" value="Buscar" class="text-gray-600 font-semibold mb-1 text-xs" />
                    <x-input id="search" type="text" wire:model.live="search" placeholder="Nombre o documento..." class="w-full" />
                </div>
                <div>
                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Vehículos</x-label>
                    <select wire:model.live="tieneVehiculo" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                        <option value="todos">Todos</option>
                        <option value="si">Con vehículos</option>
                        <option value="no">Sin vehículos</option>
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
        @if ($clientes->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">#</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Nombre</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Apellido</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Documento</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Teléfono</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Correo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Dirección</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($clientes as $cli)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <div class="inline-flex items-center justify-center w-7 h-7 rounded-md bg-indigo-100 text-indigo-700 text-xs font-bold">
                                        {{ $loop->iteration }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    {{ $cli->nombre ?? $cli->razon_social }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    {{ $cli->apellido ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                        {{ $cli->documento }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    {{ $cli->telefono }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    {{ $cli->email }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    {{ $cli->direccion }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="edit({{ $cli->id }})"
                                        class="px-2.5 py-1 text-[11px] font-semibold bg-amber-100 text-amber-700 hover:bg-amber-200 rounded-lg transition-colors"
                                        title="Editar cliente">
                                        <i class="fas fa-pen-to-square mr-0.5"></i> Editar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $clientes->withQueryString()->links() }}
            </div>
        @else
            <div class="px-6 py-10 text-center">
                <div class="w-16 h-16 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <p class="text-gray-600 font-semibold">No hay clientes registrados</p>
                <p class="text-xs text-gray-400 mt-1">Aún no se han registrado clientes en el sistema.</p>
            </div>
        @endif
    </div>

    <!-- Dialog Modal para actualizar -->
    <x-dialog-modal wire:model="open" wire:loading.attr="disabled" wire:target="open">
        <x-slot name="title">
            Editar Cliente
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nombre -->
                <div>
                    <x-label for="nombre" value="Nombre" />
                    <x-input id="nombre" type="text" class="mt-1 block w-full" wire:model="nombre" placeholder="Ej: Juan" />
                </div>
                <!-- Apellido -->
                <div>
                    <x-label for="apellido" value="Apellido" />
                    <x-input id="apellido" type="text" class="mt-1 block w-full" wire:model="apellido" placeholder="Ej: Pérez" />
                </div>
                <!-- Documento -->
                <div>
                    <x-label for="documento" value="Documento (DNI)" />
                    <x-input id="documento" type="text" class="mt-1 block w-full" wire:model="documento" placeholder="Ej: 12345678" maxlength="8" />
                </div>
                <!-- Teléfono -->
                <div>
                    <x-label for="telefono" value="Teléfono (máx. 9 dígitos)" />
                    <x-input id="telefono" type="tel" class="mt-1 block w-full" wire:model="telefono" placeholder="Ej: 912345678" maxlength="9" />
                </div>
                <!-- Email -->
                <div class="md:col-span-2">
                    <x-label for="email" value="Correo Electrónico" />
                    <x-input id="email" type="email" class="mt-1 block w-full" wire:model="email" placeholder="Ej: juan@email.com" />
                </div>
                <!-- Dirección -->
                <div class="md:col-span-2">
                    <x-label for="direccion" value="Dirección" />
                    <x-input id="direccion" type="text" class="mt-1 block w-full" wire:model="direccion" placeholder="Ej: Av. Principal 123" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)" class="mx-2">
                Cerrar
            </x-secondary-button>
            <x-button wire:click="updateCliente" wire:loading.attr="disabled" wire:target="updateCliente">
                Actualizar
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Dialog Modal para crear nuevo cliente -->
    <x-dialog-modal wire:model="openCreate" wire:loading.attr="disabled" wire:target="openCreate">
        <x-slot name="title">
            Agregar Nuevo Cliente
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nombre -->
                <div>
                    <x-label for="createNombre" value="Nombre" />
                    <x-input id="createNombre" type="text" class="mt-1 block w-full" wire:model.live="createNombre" placeholder="Ej: Juan" />
                </div>
                <!-- Apellido -->
                <div>
                    <x-label for="createApellido" value="Apellido" />
                    <x-input id="createApellido" type="text" class="mt-1 block w-full" wire:model.live="createApellido" placeholder="Ej: Pérez" />
                </div>
                <!-- Documento -->
                <div>
                    <x-label for="createDocumento" value="Documento (DNI)" />
                    <x-input id="createDocumento" type="text" class="mt-1 block w-full" wire:model.live="createDocumento" placeholder="Ej: 12345678" maxlength="8" />
                </div>
                <!-- Teléfono -->
                <div>
                    <x-label for="createTelefono" value="Teléfono (máx. 9 dígitos)" />
                    <x-input id="createTelefono" type="tel" class="mt-1 block w-full" wire:model.live="createTelefono" placeholder="Ej: 912345678" maxlength="9" />
                </div>
                <!-- Email -->
                <div class="md:col-span-2">
                    <x-label for="createEmail" value="Correo Electrónico" />
                    <x-input id="createEmail" type="email" class="mt-1 block w-full" wire:model.live="createEmail" placeholder="Ej: juan@email.com" />
                </div>
                <!-- Dirección -->
                <div class="md:col-span-2">
                    <x-label for="createDireccion" value="Dirección" />
                    <x-input id="createDireccion" type="text" class="mt-1 block w-full" wire:model.live="createDireccion" placeholder="Ej: Av. Principal 123" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('openCreate', false)" class="mx-2">
                Cerrar
            </x-secondary-button>
            <x-button wire:click="storeCliente" wire:loading.attr="disabled" wire:target="storeCliente">
                Guardar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
