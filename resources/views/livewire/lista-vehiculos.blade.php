<!-- resources>views>livewire>lista-vehiculos.blade.php -->
<div class="flex box-border">
    <div class="container mx-auto py-4">
        <x-table-vehiculos>
            @if ($vehiculos->count())
                <table class="w-full whitespace-nowrap table-auto">
                    <thead class="bg-slate-600 font-bold text-white">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left">#</th>
                            <th scope="col" class="px-6 py-4 text-left">Cliente</th>
                            <th scope="col" class="px-6 py-4 text-left">Taller</th>
                            <th scope="col" class="px-6 py-4 text-left">Placa</th>
                            <th scope="col" class="px-6 py-4 text-left">Marca</th>
                            <th scope="col" class="px-6 py-4 text-left">Modelo</th>
                            <th scope="col" class="px-6 py-4 text-left">Documentos</th>
                            <th scope="col" class="px-6 py-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($vehiculos as $veh)
                            <tr tabindex="0" class="focus:outline-none bg-white h-16 hover:bg-gray-100">
                                <td class="px-6 py-4 text-left">
                                    <div class="flex items-center">
                                        <div
                                            class="bg-indigo-200 rounded-md w-7 h-7 flex flex-shrink-0 justify-center items-center text-indigo-900">
                                            {{ $veh->id }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm font-medium leading-none text-gray-600">
                                        {{ $veh->cliente->nombre . ' ' . $veh->cliente->apellido }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm leading-none text-gray-600">
                                        Lifegas Company
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p
                                        class="text-sm leading-none text-gray-600 p-2 bg-blue-200 rounded-full inline-block">
                                        {{ $veh->placa }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm font-medium leading-none text-gray-600">
                                        {{ $veh->marca }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm font-medium leading-none text-gray-600">
                                        {{ $veh->modelo }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <div class="relative text-center" x-data="{ menu: false }">
                                        <!-- Boton -->
                                        <button type="button" x-on:click="menu = ! menu" id="menu-button"
                                            aria-expanded="true" aria-haspopup="true" data-te-ripple-init
                                            data-te-ripple-color="light"
                                            class="hover:animate-pulse inline-block rounded-full bg-teal-500 p-2 uppercase leading-normal text-white shadow-md transition duration-150 ease-in-out hover:bg-teal-700 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-teal-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-teal-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="h-4 w-4">
                                                <path fill-rule="evenodd"
                                                    d="M19.5 21a3 3 0 003-3V9a3 3 0 00-3-3h-5.379a.75.75 0 01-.53-.22L11.47 3.66A2.25 2.25 0 009.879 3H4.5a3 3 0 00-3 3v12a3 3 0 003 3h15zm-6.75-10.5a.75.75 0 00-1.5 0v4.19l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3a.75.75 0 001.06 0l3-3a.75.75 0 10-1.06-1.06l-1.72 1.72V10.5z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <!-- Opciones de Boton -->
                                        <div x-show="menu" x-on:click.away="menu = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-gray-300 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-40"
                                            role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                                            tabindex="-1">
                                            <div class="" role="none">
                                                <a href="{{ route('manual.pdf', $veh->id) }}" target="__blank"
                                                    rel="noopener noreferrer"
                                                    class="flex px-4 py-2 text-sm text-blue-600 hover:bg-slate-600 hover:text-white justify-between items-center rounded-t-md hover:cursor-pointer">
                                                    <i class="fa-solid fa-clipboard-list"></i>
                                                    <span>Manual Conversion</span>
                                                </a>
                                                <a href="{{ route('vehiculo.pdf', $veh->id) }}" target="__blank"
                                                    rel="noopener noreferrer"
                                                    class="flex px-4 py-2 text-sm text-blue-600 hover:bg-slate-600 hover:text-white justify-between items-center rounded-t-md hover:cursor-pointer">
                                                    <i class="fa-solid fa-clipboard-list"></i>
                                                    <span>Carta Garantia</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <div class="relative group">
                                            <a wire:click="edit({{ $veh->id }})"
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
                @if ($vehiculos->hasPages())
                    <div class="py-4 px-2 bg-white">
                        {{ $vehiculos->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-4 text-center font-bold bg-blue-100 rounded-md">
                        No se encontró ningún registro.
                </div>
            @endif
        </x-table-vehiculos>
    </div>

    <!-- Dialog Modal para actualizar -->
    <x-dialog-modal wire:model="open" wire:loading.attr="disabled" wire:target="open">
        <x-slot name="title">
            Editar Vehiculo
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label for="placa" value="Placa" />
                    <x-input id="placa" type="text" class="mt-1 block w-full" wire:model.live="placa" />
                    @error('placa')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="marca" value="Marca" />
                    <x-input id="marca" type="text" class="mt-1 block w-full" wire:model.live="marca" />
                    @error('marca')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="modelo" value="Modelo" />
                    <x-input id="modelo" type="text" class="mt-1 block w-full" wire:model.live="modelo" />
                    @error('modelo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="anio" value="Año" />
                    <x-input id="anio" type="number" class="mt-1 block w-full" wire:model.live="anio" />
                    @error('anio')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="combustible" value="Combustible" />
                    <x-input id="combustible" type="text" class="mt-1 block w-full"
                        wire:model.live="combustible" />
                    @error('combustible')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="serie" value="Serie" />
                    <x-input id="serie" type="text" class="mt-1 block w-full" wire:model.live="serie" />
                    @error('serie')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="color" value="Color" />
                    <x-input id="color" type="text" class="mt-1 block w-full" wire:model.live="color" />
                    @error('color')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)" class="mx-2">
                Cerrar
            </x-secondary-button>
            <x-button wire:click="updateVehiculo" wire:loading.attr="disabled" wire:target="updateVehiculo">
                Actualizar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
