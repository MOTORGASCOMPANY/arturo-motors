<div wire:loading.attr="disabled">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-tools text-indigo-600"></i> Órdenes de Servicio
                </h2>
                <p class="text-xs text-gray-500 mt-1">Todas las órdenes de servicio</p>
            </div>
            <a href="{{ route('ordenes.simple.crear') }}"
                class="px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Nueva orden
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
            <div class="p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <x-label for="buscar" value="Buscar" class="text-gray-600 font-semibold mb-1 text-xs" />
                        <x-input id="buscar" type="text" wire:model.live.debounce.400ms="buscar" placeholder="Nombre, documento o placa..." class="w-full" />
                    </div>
                    <div class="w-36">
                        <x-label for="tipo" value="Tipo" class="text-gray-600 font-semibold mb-1 text-xs" />
                        <select id="tipo" wire:model.live="tipo"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="todos">Todos</option>
                            <option value="simple">Simple</option>
                            <option value="conversion">Conversión</option>
                        </select>
                    </div>
                    <div class="w-44">
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Estado</x-label>
                        <select wire:model.live="estado" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="todos">Todos</option>
                            <option value="creada">Creada</option>
                            <option value="en_evaluacion">En evaluación</option>
                            <option value="aprobado_conversion">Aprobado conversión</option>
                            <option value="en_conversion">En conversión</option>
                            <option value="conversion_completada">Conversión completada</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <button @click="expandir = !expandir"
                        class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-sliders-h"></i>
                        <span x-text="expandir ? 'Menos' : 'Más filtros'"></span>
                        <i class="fas" :class="expandir ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <button wire:click="limpiarFiltros"
                        class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-eraser"></i> Limpiar
                    </button>
                </div>
                <!-- Filtros avanzados -->
                <div x-show="expandir" x-transition class="mt-4 pt-4 border-t border-gray-100">
                    <div class="grid grid-cols-2 sm:grid-cols-2 gap-3">
                        <div>
                            <x-label for="desde" value="Desde" class="text-gray-500 text-xs mb-1" />
                            <input id="desde" type="date" wire:model.live="desde"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm" />
                        </div>
                        <div>
                            <x-label for="hasta" value="Hasta" class="text-gray-500 text-xs mb-1" />
                            <input id="hasta" type="date" wire:model.live="hasta"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @if ($ordenes->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Fecha</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Folio</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Cliente</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Vehículo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Servicio</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Monto</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Estado</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($ordenes as $orden)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                        {{ $orden->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-800 border-r border-gray-100">
                                        {{ $orden->comprobante->folio ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-800 border-r border-gray-100">
                                        {{ $orden->vehiculo->placa }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                        {{ $orden->service->nombre }}
                                        @if ($orden->service->tipo === 'conversion')
                                            <span class="ml-1 text-[10px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded-full font-semibold">CONV</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-800 border-r border-gray-100">
                                        S/ {{ number_format($orden->precio_final, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center border-r border-gray-100">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ match(true) {
                                                str_contains($orden->estado, 'cancel') || str_contains($orden->estado, 'rechaz') => 'bg-red-100 text-red-700',
                                                in_array($orden->estado, ['entregada', 'entregado']) => 'bg-emerald-100 text-emerald-700',
                                                default => 'bg-amber-100 text-amber-700',
                                            } }}">
                                            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('ordenes.detalle', $orden->id) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Ver detalle">
                                                <i class="fa-solid fa-eye text-xs"></i>
                                            </a>
                                            @if ($orden->comprobante)
                                                <a href="{{ route('comprobantes.pdf', $orden->id) }}" target="_blank"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" title="Ver PDF">
                                                    <i class="fa-solid fa-file-pdf text-xs"></i>
                                                </a>
                                            @endif
                                            @if ($orden->service->tipo === 'simple' && $orden->estado !== 'cancelada')
                                                <button x-data
                                                    @click="
                                                        Swal.fire({
                                                            icon: 'warning', title: '¿Cancelar orden?',
                                                            text: 'La orden #{{ $orden->id }} será cancelada y se revertirá el cobro.',
                                                            showCancelButton: true, confirmButtonText: 'Sí, cancelar',
                                                            cancelButtonText: 'No, volver', confirmButtonColor: '#dc2626'
                                                        }).then(r => { if (r.isConfirmed) { $wire.call('cancelar', {{ $orden->id }}); } })
                                                    "
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors" title="Cancelar">
                                                    <i class="fa-solid fa-ban text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $ordenes->links() }}
                </div>
            @else
                <div class="px-6 py-10 text-center">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-tools text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-semibold">No hay órdenes registradas</p>
                </div>
            @endif
        </div>
    </div>
</div>
