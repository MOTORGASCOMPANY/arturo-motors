<div wire:loading.attr="disabled">

    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-unlock-keyhole text-indigo-600"></i> Permisos
            </h2>
            <p class="text-sm text-gray-500 mt-1">Gestión de permisos del sistema</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200">
                        <span class="pl-3 text-sm text-gray-500">Mostrar</span>
                        <select wire:model.live="cant"
                            class="bg-transparent border-none text-sm font-medium text-gray-700 px-2 py-2 outline-none cursor-pointer">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                        <span class="pr-3 text-sm text-gray-500">entradas</span>
                    </div>

                    <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 flex-1 sm:w-72">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 ml-3" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                        <input class="bg-transparent border-none text-sm text-gray-700 w-full px-3 py-2 outline-none focus:ring-0"
                            type="text" wire:model.live="search" placeholder="Buscar por nombre o descripción...">
                    </div>

                    @if($search)
                        <button wire:click="$set('search', '')"
                            class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    @endif
                </div>

                <div>
                    @livewire('create-permiso')
                </div>
            </div>
        </div>

        @if ($permisos->count())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100 w-16">
                                    #</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Nombre</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Descripción</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Fecha de Creación</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($permisos as $item)
                                <tr wire:key="permiso-{{ $item->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center border-r border-gray-100 font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        <div class="flex items-center justify-center gap-3">
                                            <div class="flex-shrink-0 w-8 h-8">
                                                <div class="w-full h-full rounded-full bg-purple-100 flex items-center justify-center">
                                                    <i class="fas fa-key text-purple-600 text-xs"></i>
                                                </div>
                                            </div>
                                            <span class="font-semibold text-gray-900">{{ $item->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-600">
                                        {{ $item->descripcion ?? 'Sin descripción' }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        <div class="flex flex-col items-center">
                                            <span class="font-medium text-gray-900">{{ optional($item->created_at)->format('d/m/Y') }}</span>
                                            <span class="text-xs text-gray-500">{{ optional($item->created_at)->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="editarPermiso({{ $item->id }})"
                                            class="py-2 px-3 rounded-lg bg-lime-500 font-bold text-white hover:bg-lime-600 transition">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $permisos->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-unlock-keyhole text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm">
                        No se encontró ningún registro con "<span class="font-semibold text-gray-700">{{ $search }}</span>".
                    </p>
                </div>
            </div>
        @endif

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
