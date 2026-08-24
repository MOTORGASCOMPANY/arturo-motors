<div wire:loading.attr="disabled">

    <div class="container mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-6 md:block sm:block">
                <div class="px-2 w-64 mb-4 md:w-full">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-users-cog mr-2"></i>Usuarios
                    </h2>
                    <span class="text-xs">Todos los usuarios registrados</span>
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
                            type="text" wire:model.live="search" placeholder="Buscar por nombre o correo...">
                    </div>

                    <div class="mb-4">
                        <button class="bg-indigo-500 px-6 py-4 rounded-md text-white font-semibold tracking-wide cursor-pointer hover:bg-indigo-600 transition">
                            Nuevo Usuario &nbsp;<i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if ($usuarios->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase w-16">
                                    #</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Nombre</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Correo</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Roles</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase w-20">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $item)
                                <tr wire:key="user-{{ $item->id }}">
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm text-center font-medium text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                <img class="w-full h-full rounded-full object-cover"
                                                    src="{{ $item->profile_photo_url }}" alt="{{ $item->name }}">
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-gray-900 font-bold">{{ strtoupper($item->name) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm">
                                        {{ $item->email }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm">
                                        @forelse ($item->roles as $role)
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 italic text-xs">Sin roles</span>
                                        @endforelse
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 bg-white text-sm text-center">
                                        <button wire:click="editarUsuario({{ $item->id }})"
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
                    {{ $usuarios->links() }}
                </div>
            @else
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No se encontró ningún registro con "{{ $search }}".
                </div>
            @endif
        </div>
    </div>
    

    <!-- Modal para editar usuario -->
    <x-dialog-modal wire:model.live="editando" wire:loading.attr="disabled">
        <x-slot name="title" class="font-bold">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-user-pen text-white"></i> &nbsp;Editar Usuario y Asignar Roles</h1>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                {{-- Sección 1: Información Básica --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-label value="Nombre Completo" />
                        <x-input type="text" class="w-full" wire:model="name" />
                        <x-input-error for="name" />
                    </div>
                    <div>
                        <x-label value="Correo Electrónico" />
                        <x-input type="email" class="w-full" wire:model="email" />
                        <x-input-error for="email" />
                    </div>
                    <div>
                        <x-label value="DNI / Documento" />
                        <x-input type="text" class="w-full" wire:model="dni" />
                    </div>
                    <div>
                        <x-label value="Celular" />
                        <x-input type="text" class="w-full" wire:model="celular" />
                    </div>
                </div>

                <hr class="border-gray-300">

                {{-- Sección 2: Información Personal y Domicilio --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-label value="Dirección de Domicilio" />
                        <x-input type="text" class="w-full" wire:model="direccion" />
                    </div>
                    <div>
                        <x-label value="Fecha de Nacimiento" />
                        <x-input type="date" class="w-full" wire:model="fecha_nacimiento" />
                    </div>
                    <div class="flex items-center mt-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="asignacion_familiar"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-semibold text-gray-700">¿Tiene Asignación Familiar?</span>
                        </label>
                    </div>
                </div>

                <hr class="border-gray-300">

                {{-- Sección 3: Datos de Pago y Pensiones --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <x-label value="Número de Cuenta Bancaria" />
                        <x-input type="text" class="w-full" wire:model="numero_cuenta"
                            placeholder="BCP, BBVA, etc." />
                    </div>
                    <div>
                        <x-label value="Sistema Pensionario" />
                        <select wire:model="sistema_pensionario"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Seleccione...</option>
                            <option value="AFP Integra">AFP Integra</option>
                            <option value="AFP Prima">AFP Prima</option>
                            <option value="AFP Habitat">AFP Habitat</option>
                            <option value="AFP Profuturo">AFP Profuturo</option>
                            <option value="ONP">ONP</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <x-label value="Otros Beneficios o Notas" />
                        <textarea wire:model="beneficios" placeholder="Ej: ASIGNACION/ONP/ESSALUD"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2"></textarea>
                    </div>
                </div>

                {{-- Sección 4: Roles del Sistema --}}
                <div>
                    <x-label value="Roles de Acceso al Sistema" class="mb-2 font-bold text-indigo-600" />
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @if (isset($roles))
                            @foreach ($roles as $role)
                                <label class="inline-flex items-center bg-white p-2 rounded-md border border-gray-200 cursor-pointer hover:bg-gray-100 transition">
                                    <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" id="{{ $role->id . 'checkbox' }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-xs text-gray-600 uppercase">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>
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
