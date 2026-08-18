<div wire:init="" wire:loading.attr="disabled">

    <div class="container mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-6 md:block sm:block">
                <div class="px-2 w-64 mb-4 md:w-full">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-unlock-keyhole mr-2"></i>Permisos
                    </h2>
                    <span class="text-xs">Gestión de permisos del sistema</span>
                </div>

                <div class="w-full items-center md:flex md:justify-between">
                    <div class="flex bg-gray-50 items-center p-2 rounded-md mb-4">
                        <span>Mostrar</span>
                        <select wire:model.live="cant"
                            class="bg-gray-50 mx-2 border-indigo-500 rounded-md outline-none ml-1 block">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                        <span>Entradas</span>
                    </div>

                    <div class="flex bg-gray-50 items-center lg:w-3/6 p-2 rounded-md mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                        <input class="bg-gray-50 outline-none block rounded-md border-indigo-500 w-full border-none focus:ring-0"
                            type="text" wire:model.live="search" placeholder="Buscar por nombre o descripción...">
                    </div>

                    <div class="mb-4">
                        @livewire('create-permiso')
                    </div>
                </div>
            </div>

            @if ($permisos->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase w-16">
                                    #</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Nombre</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Descripción</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Fecha de Creación</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase w-20">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permisos as $item)
                                <tr wire:key="permiso-{{ $item->id }}">
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm text-center font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                <div class="w-full h-full rounded-full bg-purple-100 flex items-center justify-center">
                                                    <i class="fas fa-key text-purple-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-gray-900 font-bold">{{ $item->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm">
                                        {{ $item->descripcion ?? 'Sin descripción' }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900">{{ optional($item->created_at)->format('d/m/Y') }}</span>
                                            <span class="text-xs text-gray-500">{{ optional($item->created_at)->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm text-center">
                                        <button wire:click="editarPermiso({{ $item->id }})"
                                            class="py-2 px-3 rounded-md bg-lime-500 font-bold text-white hover:bg-lime-600 transition">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $permisos->links() }}
                </div>
            @else
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No se encontró ningún registro con "{{ $search }}".
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL PARA EDITAR PERMISO --}}
    <x-dialog-modal wire:model="editando" wire:loading.attr="disabled">
        <x-slot name="title" class="font-bold">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-pen text-white"></i> &nbsp;Editar Permiso</h1>
        </x-slot>

        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre:" />
                <x-input wire:model="name" type="text" class="w-full" />
                <x-input-error for="name" />
            </div>

            <div class="mb-4">
                <x-label value="Descripcion:" />
                <x-input wire:model="descripcion" type="text" class="w-full" />
                <x-input-error for="descripcion" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('editando',false)" class="mx-2">
                Cancelar
            </x-secondary-button>
            <x-button wire:click="actualizar" wire:loading.attr="disabled" wire:target="actualizar">
                Actualizar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
