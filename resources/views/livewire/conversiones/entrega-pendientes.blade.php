<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-tools text-emerald-600"></i>
                Entregas pendientes
            </h2>
            <p class="text-sm text-gray-500 mt-1">Todas las conversiones listas para entrega y cobro</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
            <div class="p-4">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1">
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Buscar</x-label>
                        <div class="relative">
                            <x-input icon="fas fa-search" wire:model.live="search" placeholder="Cliente, placa o ID..." class="w-full" />
                        </div>
                    </div>
                    <button type="button" x-on:click="expandir = !expandir" class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-sliders-h"></i> Más filtros
                        <i class="fas" :class="expandir ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>
                <div x-show="expandir" x-transition class="mt-3 pt-3 border-t border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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

        @if ($ordenes->count())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                    Fin de conversión
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
                                    Monto
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Acción
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($ordenes as $orden)
                                <tr wire:key="entrega-orden-{{ $orden->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center border-r border-gray-100 whitespace-nowrap">
                                        {{ $orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100 whitespace-nowrap">
                                        <span class="font-bold text-gray-800">{{ $orden->vehiculo->placa }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        {{ $orden->service->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold border-r border-gray-100 whitespace-nowrap">
                                        S/ {{ number_format($orden->precio_final, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <a href="{{ route('conversiones.entregar', $orden->id) }}"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block shadow-sm">
                                            Entregar y cobrar
                                        </a>
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
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-check-circle text-emerald-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No hay conversiones listas para entrega.</p>
                    <p class="text-sm text-gray-400 mt-1">Todas las entregas fueron procesadas.</p>
                </div>
            </div>
        @endif
    </div>
</div>