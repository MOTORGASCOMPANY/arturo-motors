<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tools text-amber-600"></i>
                        Rearmar Kit
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Devolver pieza de repuesto al kit abierto para completarlo</p>
                </div>
                <a href="{{ route('almacen.stock') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver al stock</a>
            </div>

            {{-- Info del kit --}}
            @if ($kit)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-box text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-800">{{ $kit->producto->nombre }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">ABIERTO</span>
                                @if ($kit->serie)
                                    <span class="text-sm text-gray-500">Serie: {{ $kit->serie }}</span>
                                @endif
                                <span class="text-sm text-gray-400">ID: {{ $kit->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Piezas faltantes --}}
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i>
                        Piezas extraídas (faltantes)
                    </h3>

                    @if (empty($piezasFaltantes))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mb-2"></i>
                            <p class="text-sm font-semibold text-green-700">¡El kit está completo! No hay piezas faltantes.</p>
                            <a href="{{ route('almacen.stock') }}" class="mt-3 inline-block px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition">
                                Volver al stock
                            </a>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($piezasFaltantes as $faltante)
                                <div class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-xl">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-times text-sm"></i>
                                    </div>
                                    <p class="flex-1 text-sm font-semibold text-gray-800">
                                        {{ $faltante['nombre'] }}
                                        <span class="text-red-500 text-xs ml-1">(extraído ×{{ $faltante['cantidad_extraida'] }})</span>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Buscar pieza de repuesto --}}
                @if (!empty($piezasFaltantes))
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fas fa-search text-blue-500"></i>
                            Buscar pieza de repuesto
                        </h3>

                        <div class="relative">
                            <input type="text" wire:model.live="buscarPieza" placeholder="Buscar por serie o nombre del producto..."
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-10">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>

                        {{-- Resultados de búsqueda --}}
                        @if (!empty($piezasEncontradas))
                            <div class="mt-2 bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                                @foreach($piezasEncontradas as $pieza)
                                    <button type="button" wire:click="seleccionarPieza({{ $pieza['id'] }})"
                                        class="w-full flex items-center gap-3 p-3 hover:bg-blue-50 transition text-left border-b border-gray-100 last:border-0">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-microchip text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $pieza['producto']['nombre'] ?? '—' }}</p>
                                            <p class="text-xs text-gray-500">Serie: {{ $pieza['serie'] ?? '—' }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full shrink-0">Disponible</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Pieza seleccionada --}}
                    @if ($piezaSeleccionadaId)
                        @php $seleccionada = \App\Models\ItemSerializado::with('producto')->find($piezaSeleccionadaId); @endphp
                        @if ($seleccionada)
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-check text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $seleccionada->producto->nombre }}</p>
                                            <p class="text-xs text-gray-500">Serie: {{ $seleccionada->serie ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="deseleccionarPieza"
                                        class="text-gray-400 hover:text-red-500 transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Botón rearmar --}}
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <a href="{{ route('almacen.stock') }}"
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancelar
                        </a>
                        @if ($piezaSeleccionadaId)
                            <button type="button" wire:click="confirmarRearme" wire:loading.attr="disabled" wire:confirm="¿Confirmar rearme del kit?"
                                class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition disabled:opacity-50">
                                <span wire:loading.remove wire:target="confirmarRearme">
                                    <i class="fas fa-tools mr-1"></i> Rearmar kit
                                </span>
                                <span wire:loading wire:target="confirmarRearme">Procesando...</span>
                            </button>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- SweetAlert2 listener --}}
    <script src="{{ asset('js/components/livewire-swal-listener.js') }}"></script>
</div>
