            @if ($this->tabSeleccion === 'piezas')
                @php $piezas = $this->piezasSueltas; @endphp

                @if ($piezas->isEmpty())
                    <div class="text-center py-14 text-gray-400">
                        <i class="fas fa-puzzle-piece text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">No hay piezas sueltas disponibles</p>
                        <p class="text-xs mt-1">Prueba con otro término de búsqueda</p>
                    </div>
                @else
                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1 -mr-1">
                        @foreach ($piezas as $pieza)
                            @php $isSelected = isset($this->itemsSeleccionados[$pieza->id]); @endphp

                            <label class="flex items-center gap-3 border-2 rounded-xl px-3 sm:px-4 py-3 cursor-pointer transition-all duration-150
                                {{ $isSelected ? 'border-indigo-500 bg-indigo-50/60 ring-1 ring-indigo-100' : 'border-gray-100 hover:border-gray-300 bg-white' }}">
                                <input type="checkbox"
                                    wire:click="togglePieza({{ $pieza->id }})"
                                    @checked($isSelected)
                                    class="w-4.5 h-4.5 sm:w-4 sm:h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $pieza->producto->nombre }}</p>
                                    <p class="text-[11px] text-gray-400 truncate mt-0.5">
                                        <i class="fas fa-barcode mr-1"></i>{{ $pieza->serie ?? 'Sin serie' }}
                                        <span class="text-gray-300 mx-1">·</span>
                                        <i class="fas fa-location-dot mr-1"></i>{{ $pieza->sede?->nombre ?? '—' }}
                                    </p>
                                </div>

                                @if ($isSelected)
                                    <i class="fas fa-circle-check text-indigo-500 text-sm shrink-0"></i>
                                @endif
                            </label>
                        @endforeach
                    </div>
                @endif
            @endif
