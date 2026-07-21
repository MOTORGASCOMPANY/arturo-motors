<!-- resources>views>livewire>lista-clientes.blade.php -->
<div class="flex box-border">
    <div class="container mx-auto py-4">
        <x-custom-table>
            <x-slot name="titulo">
                <h2 class="text-gray-600 font-semibold text-2xl">Clientes</h2>
                <span class="text-xs text-gray-500">Todos los registros de clientes</span>
            </x-slot>
            <x-slot name="btnAgregar">
                <button wire:click=""
                    class="bg-slate-600 px-6 py-4 rounded-md text-white font-semibold tracking-wide cursor-pointer">Agregar</button>
            </x-slot>
            <x-slot name="contenido">
                <!-- Contenido de la tabla aquí -->
                @if ($clientes->count())
                    <table class="w-full whitespace-nowrap table-auto">
                        <thead class="bg-slate-600 font-bold text-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left">#</th>
                                <th scope="col" class="px-6 py-4 text-left">Nombre</th>
                                <th scope="col" class="px-6 py-4 text-left">Apellido</th>
                                <th scope="col" class="px-6 py-4 text-left">Documento</th>
                                <th scope="col" class="px-6 py-4 text-left">Telefono</th>
                                <th scope="col" class="px-6 py-4 text-left">Correo</th>
                                <th scope="col" class="px-6 py-4 text-left">Direccion</th>
                                <th scope="col" class="px-6 py-4 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($clientes as $cli)
                                <tr tabindex="0" class="focus:outline-none bg-white h-16 hover:bg-gray-100">
                                    <td class="px-6 py-4 text-left">
                                        <div class="flex items-center">
                                            <div
                                                class="bg-indigo-200 rounded-md w-7 h-7 flex flex-shrink-0 justify-center items-center text-indigo-900">
                                                {{ $cli->id }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <p class="text-sm font-medium leading-none text-gray-600">
                                            {{ $cli->nombre }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <p class="text-sm leading-none text-gray-600">
                                            {{ $cli->apellido }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <p
                                            class="text-sm leading-none text-gray-600 p-2 bg-blue-200 rounded-full inline-block">
                                            {{ $cli->documento }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <p class="text-sm font-medium leading-none text-gray-600">
                                            {{ $cli->telefono }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <p class="text-sm font-medium leading-none text-gray-600">
                                            {{ $cli->email }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        <p class="text-sm font-medium leading-none text-gray-600">
                                            {{ $cli->direccion }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <div class="relative group">
                                                <a wire:click="edit({{ $cli->id }})"
                                                    class="py-1 px-2 text-center rounded-md bg-amber-300 font-bold text-black cursor-pointer hover:bg-amber-400">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <span
                                                    class="absolute bottom-full  mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                                    Editar
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- Sección de paginación mejorada --}}
                    @if ($clientes->hasPages())
                        <div class="py-4 px-2 bg-white">
                            {{ $clientes->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="px-6 py-4 text-center font-bold bg-blue-100 rounded-md">
                        No se encontró ningún registro.
                    </div>
                @endif
            </x-slot>
        </x-custom-table>
    </div>

    <!-- Dialog Modal para actualizar -->
    <x-dialog-modal wire:model="open" wire:loading.attr="disabled" wire:target="open">
        <x-slot name="title">
            Editar Conversión
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nombre -->
                <div class="">
                    <x-label for="nombre" value="Nombre" />
                    <x-input id="nombre" type="text" class="mt-1 block w-full rounded-lg" wire:model="nombre" />
                    <x-input-error for="nombre" class="mt-2" />
                </div>
                <!-- Apellido -->
                <div class="">
                    <x-label for="apellido" value="Apellido" />
                    <x-input id="apellido" type="text" class="mt-1 block w-full rounded-lg" wire:model="apellido" />
                    <x-input-error for="apellido" class="mt-2" />
                </div>
                <!-- Documento -->
                <div class="">
                    <x-label for="documento" value="Documento" />
                    <x-input id="documento" type="text" class="mt-1 block w-full rounded-lg"
                        wire:model="documento" />
                    <x-input-error for="documento" class="mt-2" />
                </div>
                <!-- Teléfono -->
                <div class="">
                    <x-label for="telefono" value="Teléfono" />
                    <x-input id="telefono" type="tel" class="mt-1 block w-full rounded-lg" wire:model="telefono" />
                    <x-input-error for="telefono" class="mt-2" />
                </div>
                <!-- Email -->
                <div class="md:col-span-2">
                    <x-label for="email" value="Email" />
                    <x-input id="email" type="email" class="mt-1 block w-full rounded-lg"
                        wire:model="email" />
                    <x-input-error for="email" class="mt-2" />
                </div>
                <!-- Dirección -->
                <div class="md:col-span-2">
                    <x-label for="direccion" value="Dirección" />
                    <x-input id="direccion" type="text" class="mt-1 block w-full rounded-lg"
                        wire:model="direccion" />
                    <x-input-error for="direccion" class="mt-2" />
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
</div>
