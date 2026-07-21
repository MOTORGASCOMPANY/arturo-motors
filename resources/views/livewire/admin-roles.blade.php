<div>
    <div class="container mx-auto py-4">
        <x-custom-table>
            <x-slot name="titulo">
                <h2 class="text-blue-600 font-bold text-3xl flex items-center gap-2">
                    <i class="fa-solid fa-user-tag fa-xl text-blue-600"></i>
                    Roles
                </h2>
            </x-slot>

            <x-slot name="btnAgregar">
                @livewire('create-rol')
            </x-slot>

            <x-slot name="contenido">
                @if (count($roles))
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="w-full whitespace-nowrap rounded-lg overflow-hidden shadow">
                            <thead class="bg-slate-600 border-b font-bold text-white">
                                <tr>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left"
                                        wire:click="order('id')">
                                        Id
                                        @if ($sort == 'id')
                                            @if ($direction == 'asc')
                                                <i class="fas fa-sort-numeric-up-alt float-right mt-0.5"></i>
                                            @else
                                                <i class="fas fa-sort-numeric-down-alt float-right mt-0.5"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort float-right mt-0.5"></i>
                                        @endif
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left"
                                        wire:click="order('name')">
                                        Nombre
                                        @if ($sort == 'name')
                                            @if ($direction == 'asc')
                                                <i class="fas fa-sort-alpha-up-alt float-right mt-0.5"></i>
                                            @else
                                                <i class="fas fa-sort-alpha-down-alt float-right mt-0.5"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort float-right mt-0.5"></i>
                                        @endif
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left"
                                        wire:click="order('created_at')">
                                        Fecha de creación
                                        @if ($sort == 'created_at')
                                            @if ($direction == 'asc')
                                                <i class="fas fa-sort-numeric-up-alt float-right mt-0.5"></i>
                                            @else
                                                <i class="fas fa-sort-numeric-down-alt float-right mt-0.5"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort float-right mt-0.5"></i>
                                        @endif
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-6 py-4 text-left">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-300">
                                @foreach ($roles as $item)
                                    <tr tabindex="0"
                                        class="focus:outline-none h-16 border border-slate-300 hover:bg-gray-200">
                                        <td class="pl-5">
                                            <div class="flex items-center">
                                                <p class="text-slate-900 p-1 bg-slate-200 rounded-md">
                                                    {{ $item->id }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="pl-2">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium leading-none text-gray-600 mr-2">
                                                    {{ $item->name }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="pl-2">
                                            <div class="flex items-center">
                                                <p class="text-sm leading-none text-gray-600 ml-2">
                                                    {{ optional($item->created_at)->format('d-m-Y H:i:s') }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="pl-2">
                                            <div class="flex items-center justify-center">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    <button wire:click="editaRol({{ $item->id }})"
                                                        class="px-3 py-2 bg-slate-400 hover:bg-slate-600 text-white rounded-lg shadow transition">
                                                        <i class="fas fa-pen text-white"></i>
                                                    </button>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($roles->hasPages())
                        <div>
                            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-2 overflow-x-auto">
                                <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                    <div class="px-5 py-5 bg-white border-t">
                                        {{ $roles->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="px-6 py-4 text-center font-bold bg-slate-200 rounded-md">
                        No se encontro ningun registro.
                    </div>
                @endif
            </x-slot>

        </x-custom-table>
    </div>

    {{-- MODAL PARA EDITAR ROL --}}
    <x-dialog-modal wire:model="editando" wire:loading.attr="disabled">
        <x-slot name="title" class="font-bold">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-pen text-white"></i> &nbsp;Editar Rol</h1>
        </x-slot>

        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre:" />
                <x-input wire:model="name" type="text" class="w-full" />
                <x-input-error for="name" />
            </div>
            <div class="mb-4">
                <x-label value="Permisos:" />
                @if (isset($permisos))
                    @foreach ($permisos as $key => $permiso)
                        <div class="flex items-center pl-3">
                            <input wire:model="selectedPermisos" id="{{ $permiso->id . 'checkbox' }}" type="checkbox"
                                value="{{ $permiso->name }}"
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none  focus:ring-slate-600">
                            <label for="{{ $permiso->id . 'checkbox' }}"
                                class="py-2 ml-2 text-sm font-medium text-gray-900 ">
                                {{ $permiso->descripcion ? $permiso->descripcion : $permiso->name }}
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
            <x-input-error for="selectedPermisos" />
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
