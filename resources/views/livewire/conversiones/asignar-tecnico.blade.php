<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-gray-600 font-semibold text-2xl">
                <i class="fas fa-tools mr-2"></i>Asignar técnico
            </h2>
            <span class="text-xs text-gray-500">Conversiones pendientes de asignación</span>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Buscar</x-label>
                    <div class="relative">
                        <x-input icon="fas fa-search" wire:model.live="search" placeholder="Cliente, placa o ID..." class="w-full" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        @if ($ordenes->count())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Fecha
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Cliente
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Vehículo
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Servicio
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Técnico
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($ordenes as $orden)
                                <tr wire:key="orden-{{ $orden->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->service->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        <select wire:model="tecnicoSeleccionado.{{ $orden->id }}" class="text-sm rounded-lg border-gray-300">
                                            <option value="">-- Selecciona --</option>
                                            @foreach ($tecnicos as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="tecnico.{{ $orden->id }}" class="mt-1" />
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="asignar({{ $orden->id }})" 
                                                wire:loading.attr="disabled" 
                                                wire:target="asignar({{ $orden->id }})"
                                                type="button"
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                                            <span wire:loading.remove wire:target="asignar({{ $orden->id }})">Asignar</span>
                                            <span wire:loading wire:target="asignar({{ $orden->id }})">Guardando...</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200/60">
                    {{ $ordenes->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-clipboard-check text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 text-sm">No hay conversiones pendientes de asignar.</p>
                </div>
            </div>
        @endif
    </div>
</div>
