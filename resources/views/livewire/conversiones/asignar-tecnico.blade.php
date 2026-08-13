<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-5xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-6 md:block sm:block">
                <!-- Titulo y subtitulo -->
                <div class="px-2 w-full mb-4">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-tools mr-2"></i>Asignar técnico
                    </h2>
                    <span class="text-xs">Conversiones pendientes de asignación</span>
                </div>
            </div>

            <!-- Tabla -->
            @if ($ordenes->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Fecha
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Cliente
                                </th>                                    
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Vehículo
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Servicio
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Técnico
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ordenes as $orden)
                                <tr wire:key="orden-{{ $orden->id }}">
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        {{ $orden->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        {{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        {{ $orden->service->nombre }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <select wire:model="tecnicoSeleccionado.{{ $orden->id }}" class="text-sm rounded-lg border-gray-300">
                                            <option value="">-- Selecciona --</option>
                                            @foreach ($tecnicos as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="tecnico.{{ $orden->id }}" class="mt-1" />
                                    </td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm">
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
            @else
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md text-indigo-900">
                    No hay conversiones pendientes de asignar.
                </div>
            @endif
        </div>
    </div>
</div>