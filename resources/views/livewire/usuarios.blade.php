<div wire:loading.attr="disabled">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users-cog text-indigo-600"></i> Usuarios
                </h2>
                <p class="text-sm text-gray-500 mt-1">Todos los usuarios registrados</p>
            </div>
            @if(auth()->user()->hasRole('Administrador del sistema'))
            <button wire:click="abrirModalCrear"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                <i class="fas fa-plus text-xs"></i> Crear usuario
            </button>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" wire:model.live="search"
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                            placeholder="Buscar por nombre o correo...">
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="expandir = !expandir"
                            class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                            <i class="fas fa-filter"></i> Más filtros
                            <i class="fas" :class="expandir ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        @if($search)
                        <button wire:click="$set('search', '')"
                            class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                        @endif
                    </div>
                </div>
                <div x-show="expandir" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Rol</label>
                            <select wire:model.live="filterRol"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                <option value="">Todos</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Entradas por página</label>
                            <select wire:model.live="cant"
                                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($usuarios->count())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100 w-16">#</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Nombre</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Correo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Roles</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($usuarios as $item)
                        <tr wire:key="user-{{ $item->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-500 font-medium">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-gray-100">
                                <div class="flex items-center justify-center gap-3">
                                    <img class="w-9 h-9 rounded-full object-cover border-2 border-gray-100"
                                        src="{{ $item->profile_photo_url }}" alt="{{ $item->name }}">
                                    <span class="font-semibold text-gray-800">{{ strtoupper($item->name) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-600">
                                {{ $item->email }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-gray-100">
                                @forelse ($item->roles as $role)
                                <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700 mr-1">
                                    {{ $role->name }}
                                </span>
                                @empty
                                <span class="text-gray-400 italic text-xs">Sin roles</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="editarUsuario({{ $item->id }})"
                                    class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="Editar">
                                    <i class="fa-solid fa-pencil text-sm"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $usuarios->links() }}
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 px-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 text-sm font-medium">No se encontró ningún registro</p>
                @if($search)
                <p class="text-gray-400 text-xs mt-1">Intenta con otros términos de búsqueda</p>
                @endif
            </div>
        </div>
        @endif
    </div>
    

    <!-- Modal para crear usuario -->
    @if(auth()->user()->hasRole('Administrador del sistema'))
    <x-dialog-modal wire:model.live="creando" wire:loading.attr="disabled">
        <x-slot name="title" class="font-bold">
            <h1 class="text-xl font-bold"><i class="fa-solid fa-user-plus text-white"></i> &nbsp;Crear Nuevo Usuario</h1>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-label value="Nombre Completo" />
                        <x-input type="text" class="w-full" wire:model="new_name" />
                        <x-input-error for="new_name" />
                    </div>
                    <div>
                        <x-label value="Correo Electrónico" />
                        <x-input type="email" class="w-full" wire:model="new_email" />
                        <x-input-error for="new_email" />
                    </div>
                    <div>
                        <x-label value="Contraseña" />
                        <x-input type="password" class="w-full" wire:model="new_password" placeholder="Mínimo 6 caracteres" />
                        <x-input-error for="new_password" />
                    </div>
                    <div>
                        <x-label value="DNI / Documento" />
                        <x-input type="text" class="w-full" wire:model="new_dni" placeholder="DNI, Carnet Extranjería o Carnet Refugio" />
                        <x-input-error for="new_dni" />
                    </div>
                    <div>
                        <x-label value="Celular" />
                        <x-input type="text" class="w-full" wire:model="new_celular" />
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('creando', false)" class="mx-2">
                Cancelar
            </x-secondary-button>
            <x-button wire:click="store" wire:loading.attr="disabled" wire:target="store">
                Crear Usuario
            </x-button>
        </x-slot>
    </x-dialog-modal>
    @endif

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
