<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <!-- Titulo y subtitulo -->
        <div>
            <h2 class="text-gray-600 font-semibold text-2xl">
                <i class="fas fa-tools mr-2"></i>Mis conversiones
            </h2>
            <span class="text-xs text-gray-500">Vehículos asignados para evaluación e instalación</span>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
            <div class="p-4">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1">
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Buscar</x-label>
                        <div class="relative">
                            <x-input icon="fas fa-search" wire:model.live="search" placeholder="Cliente, placa o ID..." class="w-full" />
                        </div>
                    </div>
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Estado</x-label>
                        <select wire:model.live="estado" class="border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="pendientes">Solo pendientes</option>
                            <option value="todas">Todas</option>
                        </select>
                    </div>
                    <button type="button" x-on:click="expandir = !expandir" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-sliders-h"></i> Más filtros
                        <i class="fas" :class="expandir ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>
                <div x-show="expandir" x-transition class="mt-3 pt-3 border-t border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <x-label class="text-gray-600 font-semibold mb-1 text-xs">Estado específico</x-label>
                            <select wire:model.live="filterGranularEstado" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                                <option value="">Todos</option>
                                <option value="en_evaluacion">En evaluación</option>
                                <option value="aprobado_conversion">Aprobado conversión</option>
                                <option value="en_conversion">En conversión</option>
                                <option value="conversion_completada">Conversión completada</option>
                            </select>
                        </div>
                        <div>
                            <x-label class="text-gray-600 font-semibold mb-1 text-xs">Fecha desde</x-label>
                            <input type="date" wire:model.live="filterFechaDesde" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                        </div>
                        <div>
                            <x-label class="text-gray-600 font-semibold mb-1 text-xs">Fecha hasta</x-label>
                            <input type="date" wire:model.live="filterFechaHasta" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                        </div>
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
                                    Estado
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($ordenes as $orden)
                                <tr wire:key="mis-conversion-{{ $orden->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center border-r border-gray-100 whitespace-nowrap">
                                        {{ $orden->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100 whitespace-nowrap">
                                        <span class="font-bold text-gray-800">{{ $orden->vehiculo->placa }}</span> — {{ $orden->vehiculo->marca }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->service->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100 whitespace-nowrap">
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
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
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
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden px-6 py-12">
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mb-4">
                        <i class="fas fa-inbox text-3xl text-indigo-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No tienes conversiones asignadas actualmente.</p>
                </div>
            </div>
        @endif
    </div>
</div>
