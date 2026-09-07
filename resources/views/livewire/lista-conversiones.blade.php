<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Conversiones
        </h2>
        <p class="mt-1 text-sm text-gray-500">Todos los registros de conversión</p>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Búsqueda --}}
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Buscar por cliente o placa..."
                        class="w-full pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                    />
                </div>

                {{-- Estado --}}
                <select
                    wire:model.live="es"
                    class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white"
                >
                    <option value="">Todos los estados</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="completado">Completado</option>
                    <option value="certificado">Certificado</option>
                </select>

                {{-- Botón Más filtros --}}
                <button
                    x-on:click="expandir = !expandir"
                    class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span x-text="expandir ? 'Menos filtros' : 'Más filtros'">Más filtros</span>
                </button>

                {{-- Botón Limpiar --}}
                <button
                    wire:click="limpiarFiltros"
                    class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar
                </button>
            </div>

            {{-- Filtros avanzados --}}
            <div x-show="expandir" x-transition class="mt-4 pt-4 border-t border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Técnico --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Técnico</label>
                        <select
                            wire:model.live="filterTecnico"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white"
                        >
                            <option value="">Todos los técnicos</option>
                            @foreach ($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Fecha desde --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha desde</label>
                        <input
                            type="date"
                            wire:model.live="filterFechaDesde"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                        >
                    </div>
                    {{-- Fecha hasta --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha hasta</label>
                        <input
                            type="date"
                            wire:model.live="filterFechaHasta"
                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    @if (count($conversiones))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">#</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Cliente</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Vehículo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Técnico</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Fecha Inicio</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Fecha Fin</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Documentos</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Creación</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($conversiones as $conve)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-500">{{ $conve->id ?? null }}</td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 font-medium text-gray-900">
                                    {{ $conve->expediente->cliente->nombre . ' ' . $conve->expediente->cliente->apellido }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-500">
                                    {{ $conve->expediente->vehiculo->placa ?? null }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-500">
                                    {{ $conve->expediente->tecnico->name ?? 'No Asignado' }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    @php
                                        $colors = [
                                            'en_proceso' => 'bg-yellow-100 text-yellow-800',
                                            'completado' => 'bg-blue-200 text-blue-700',
                                            'certificado' => 'bg-green-200 text-green-700',
                                        ];
                                        $labels = [
                                            'en_proceso' => 'En Proceso',
                                            'completado' => 'Completado',
                                            'certificado' => 'Certificado',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$conve->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $labels[$conve->estado] ?? 'Desconocido' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-500">
                                    {{ $conve->fecha_inicio }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-500">
                                    {{ $conve->fecha_fin }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <div class="relative inline-block" x-data="{ menu: false }">
                                        <button
                                            type="button"
                                            x-on:click="menu = !menu"
                                            class="inline-flex items-center justify-center rounded-full bg-teal-500 p-2 text-white shadow-md hover:bg-teal-600 transition-colors focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                                <path fill-rule="evenodd" d="M19.5 21a3 3 0 003-3V9a3 3 0 00-3-3h-5.379a.75.75 0 01-.53-.22L11.47 3.66A2.25 2.25 0 009.879 3H4.5a3 3 0 00-3 3v12a3 3 0 003 3h15zm-6.75-10.5a.75.75 0 00-1.5 0v4.19l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3a.75.75 0 001.06 0l3-3a.75.75 0 10-1.06-1.06l-1.72 1.72V10.5z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div
                                            x-show="menu"
                                            x-on:click.away="menu = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black/5 divide-y divide-gray-100 focus:outline-none z-40"
                                            role="menu"
                                        >
                                            <div class="py-1" role="none">
                                                <a href="{{ route('vehiculo.pdf', $conve->expediente->vehiculo->id) }}" target="_blank" rel="noopener noreferrer"
                                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors" role="menuitem">
                                                    <i class="fa-solid fa-clipboard-list text-teal-500"></i>
                                                    Carta de Garantía
                                                </a>
                                                <a href="{{ route('manual.pdf', $conve->expediente->vehiculo->id) }}" target="_blank" rel="noopener noreferrer"
                                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors" role="menuitem">
                                                    <i class="fa-solid fa-clipboard-list text-teal-500"></i>
                                                    Manual y Mantenimiento
                                                </a>
                                                <a href="{{ route('ordenRepuestos.pdf', ['id' => $conve->id]) }}" target="_blank" rel="noopener noreferrer"
                                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors" role="menuitem">
                                                    <i class="fa-solid fa-clipboard-list text-teal-500"></i>
                                                    Orden Repuestos y Acces
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 text-gray-500">
                                    {{ $conve->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <div class="relative group">
                                            <a wire:click="editConversion({{ $conve->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors cursor-pointer">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </a>
                                            <span class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-lg py-1 px-2 whitespace-nowrap z-10">
                                                Editar
                                            </span>
                                        </div>
                                        @hasanyrole('Administrador del sistema|Tecnico')
                                            <div class="relative group">
                                                <a wire:click="redirectToSolicitudRepuestos({{ $conve->id }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition-colors cursor-pointer">
                                                    <i class="fa-solid fa-cart-plus text-xs"></i>
                                                </a>
                                                <span class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-lg py-1 px-2 whitespace-nowrap z-10">
                                                    Repuestos
                                                </span>
                                            </div>
                                        @endhasanyrole
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($conversiones->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $conversiones->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">No se encontraron conversiones</p>
                <p class="mt-1 text-xs text-gray-400">Intenta ajustar los filtros de búsqueda</p>
            </div>
        </div>
    @endif
</div>

    <!-- Dialog Modal para actualizar -->
    <x-dialog-modal wire:model="open" wire:loading.attr="disabled" wire:target="">
        <x-slot name="title">
            Editar Conversión
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Campo Técnico -->
                <div>
                    <x-label for="tecnico_id" value="Técnico Asignado" />
                    <select id="tecnico_id" wire:model="tecnico_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">-- Seleccionar Técnico --</option>
                        @foreach ($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="tecnico_id" class="mt-2" />
                </div>

                <!-- Campo Fecha de Inicio -->
                <div>
                    <x-label for="fecha_inicio" value="Fecha de Inicio" />
                    <x-input id="fecha_inicio" type="date" class="mt-1 block w-full"
                        wire:model="fecha_inicio" />
                    <x-input-error for="fecha_inicio" class="mt-2" />
                </div>

                <!-- Campo Fecha de Fin -->
                <div>
                    <x-label for="fecha_fin" value="Fecha de Fin" />
                    <x-input id="fecha_fin" type="date" class="mt-1 block w-full"
                        wire:model="fecha_fin" />
                    <x-input-error for="fecha_fin" class="mt-2" />
                </div>

                <!-- Campo Estado -->
                <div>
                    <x-label for="estado" value="Estado" />
                    <select id="estado" wire:model="estado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="en_proceso">En Proceso</option>
                        <option value="completado">Completado</option>
                        <option value="certificado">Certificado</option>
                    </select>
                    <x-input-error for="estado" class="mt-2" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)" class="mx-2">
                Cerrar
            </x-secondary-button>

            <x-button wire:click="updateConversion" wire:loading.attr="disabled" wire:target="updateConversion">
                Actualizar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
