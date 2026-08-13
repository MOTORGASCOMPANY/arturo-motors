<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="container mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-6 md:block sm:block">
                <!-- Titulo y subtitulo -->
                <div class="px-2 w-full mb-4">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-tools mr-2"></i>Mis conversiones
                    </h2>
                    <span class="text-xs text-gray-500">Vehículos asignados para evaluación e instalación</span>
                </div>
                <!-- Filtros -->
                <div class="w-full flex flex-wrap items-center justify-between gap-4">
                    <!-- Filtro Estado -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <x-label class="mr-2" value="Estado" />
                        <select wire:model.live="estado"
                            class="border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none cursor-pointer">
                            <option value="pendientes">Solo pendientes</option>
                            <option value="todas">Todas (incluye finalizadas)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            @if ($ordenes->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Fecha
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Cliente
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Vehículo
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Servicio
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Estado
                                </th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ordenes as $orden)
                                <tr wire:key="mis-conversion-{{ $orden->id }}">
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        {{ $orden->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <span class="font-bold text-gray-800">{{ $orden->vehiculo->placa }}</span> — {{ $orden->vehiculo->marca }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        {{ $orden->service->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-semibold
                                            {{ match ($orden->estado) {
                                                'en_evaluacion' => 'bg-amber-100 text-amber-700',
                                                'aprobado_conversion' => 'bg-blue-100 text-blue-700',
                                                'en_conversion' => 'bg-purple-100 text-purple-700',
                                                'conversion_completada' => 'bg-emerald-100 text-emerald-700',
                                                'evaluacion_rechazada', 'cancelado' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            } }}">
                                            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        @switch($orden->estado)
                                            @case('en_evaluacion')
                                                <a href="{{ route('conversiones.evaluar', $orden->id) }}"
                                                    class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block shadow-sm">
                                                    <i class="fas fa-clipboard-check mr-1"></i> Evaluar
                                                </a>
                                            @break

                                            @case('aprobado_conversion')
                                                <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2.5 py-1 rounded border border-gray-200">
                                                    <i class="fas fa-clock mr-1 text-gray-400"></i> Esperando equipos
                                                </span>
                                            @break

                                            @case('en_conversion')
                                                <a href="{{ route('conversiones.realizar', $orden->id) }}"
                                                    class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block shadow-sm">
                                                    <i class="fas fa-wrench mr-1"></i> Continuar
                                                </a>
                                            @break

                                            @case('conversion_completada')
                                                <span class="text-xs text-emerald-600 font-medium">
                                                    <i class="fas fa-check-circle mr-1"></i> Completada
                                                </span>
                                            @break

                                            @default
                                                <span class="text-xs text-gray-400">—</span>
                                        @endswitch
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
                {{-- 
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No tienes conversiones asignadas.
                </div>
                --}}
                <div class="px-6 py-8 text-center font-semibold bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100 shadow-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block text-indigo-400"></i>
                    No tienes conversiones asignadas actualmente.
                </div>
            @endif
        </div>
    </div>
</div>
