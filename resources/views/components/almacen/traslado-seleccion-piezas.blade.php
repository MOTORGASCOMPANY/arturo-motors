            @if ($this->tabSeleccion === 'piezas')
                @php $piezas = $this->piezasSueltas; @endphp

                @if ($piezas->isEmpty())
                    <x-almacen.empty-state icon="fa-puzzle-piece" message="No hay piezas sueltas disponibles" />
                @else
                    <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1 -mr-1">
                        @foreach ($piezas as $pieza)
                            @php $isSelected = isset($this->itemsSeleccionados[$pieza->id]); @endphp

                            <label class="flex items-center gap-3 border-2 rounded-xl px-3 sm:px-4 py-3 cursor-pointer transition-all duration-150
                                {{ $isSelected ? 'border-indigo-500 bg-indigo-50/60 ring-1 ring-indigo-100' : 'border-gray-200 hover:border-gray-300 bg-white' }}">
                                <input type="checkbox"
                                    wire:click="togglePieza({{ $pieza->id }})"
                                    @checked($isSelected)
                                    class="w-4.5 h-4.5 sm:w-4 sm:h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">

                                <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>

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
