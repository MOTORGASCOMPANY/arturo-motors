<div wire:loading.attr="disabled">

    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-tag text-indigo-600"></i> Roles
                </h2>
                <p class="text-sm text-gray-500 mt-1">Gestión de roles y permisos del sistema</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200">
                            <span class="pl-3 text-sm text-gray-500">Mostrar</span>
                            <select wire:model.live="cant"
                                class="bg-transparent border-none text-sm font-medium text-gray-700 outline-none px-2 py-2 cursor-pointer">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                            <span class="pr-3 text-sm text-gray-500">entradas</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 px-3 py-2 lg:w-72">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            <input wire:model.live="search"
                                type="text"
                                placeholder="Buscar por nombre del rol..."
                                class="bg-transparent border-none outline-none text-sm w-full ml-2 focus:ring-0 placeholder-gray-400">
                        </div>
                        @if ($search)
                            <button wire:click="$set('search', '')"
                                class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        @endif
                        @livewire('create-rol')
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @if ($roles->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100 w-16">
                                    #
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Nombre
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Permisos
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Fecha de Creación
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($roles as $item)
                                <tr class="hover:bg-gray-50 transition-colors" wire:key="role-{{ $item->id }}">
                                    <td class="px-4 py-3 text-center border-r border-gray-100 font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                                <i class="fas fa-user-tag text-indigo-600 text-xs"></i>
                                            </div>
                                            <span class="font-bold text-gray-900">{{ strtoupper($item->name) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        <div class="flex flex-wrap gap-1 justify-center items-center">
                                            @forelse ($item->permissions->take(3) as $permiso)
                                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-indigo-100 text-indigo-700">
                                                    {{ $permiso->name }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 italic text-xs">Sin permisos</span>
                                            @endforelse
                                            @if($item->permissions->count() > 3)
                                                <button wire:click="verPermisos({{ $item->id }})"
                                                    class="px-2 py-0.5 text-xs font-medium rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200 transition cursor-pointer">
                                                    Ver todo
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        <span class="font-medium text-gray-900">{{ optional($item->created_at)->format('d/m/Y') }}</span>
                                        <br>
                                        <span class="text-xs text-gray-500">{{ optional($item->created_at)->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="editaRol({{ $item->id }})"
                                            class="py-2 px-3 rounded-lg bg-lime-500 font-bold text-white hover:bg-lime-600 transition">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $roles->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tag text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm">
                        No se encontró ningún registro con "<span class="font-semibold text-gray-700">{{ $search }}</span>".
                    </p>
                </div>
            @endif
        </div>

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

    {{-- MODAL VER TODOS LOS PERMISOS --}}
    <x-dialog-modal wire:model="openPermisos" wire:loading.attr="disabled">
        <x-slot name="title">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-key text-white"></i> &nbsp;Permisos de {{ $rolNombre }}</h1>
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-wrap gap-2">
                @foreach ($rolPermisos as $permiso)
                    <span class="px-3 py-1.5 text-sm font-medium rounded bg-indigo-100 text-indigo-700">
                        {{ $permiso }}
                    </span>
                @endforeach
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('openPermisos', false)" class="mx-2">
                Cerrar
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>
