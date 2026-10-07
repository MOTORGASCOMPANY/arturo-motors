            @if ($this->tabSeleccion === 'cantidad')
                @php $productos = $this->productosCantidad; @endphp

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 mb-3">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <select wire:model="productoCantidadId"
                                class="w-full rounded-lg border-gray-300 text-sm py-2.5 sm:py-2 pr-9 appearance-none focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Selecciona pieza --</option>
                                @foreach ($productos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }} (disponible: {{ $p->disponible }})</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down text-gray-400 text-xs absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" min="1" wire:model="cantidadPieza"
                                class="w-20 rounded-lg border-gray-300 text-sm py-2.5 sm:py-2 text-center focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Cant.">
                            <button wire:click="agregarPiezaCantidad" type="button"
                                class="flex-1 sm:flex-none px-4 py-2.5 sm:py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 active:scale-[0.98] transition whitespace-nowrap">
                                <i class="fas fa-plus mr-1"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <x-input-error for="cantidadPieza" class="mt-2" />
                </div>

                @if ($this->piezasCantidadCarrito->count())
                    <ul class="space-y-1.5">
                        @foreach ($this->piezasCantidadCarrito as $p)
                            <li class="flex justify-between items-center gap-2 text-sm bg-white border-2 border-gray-200 rounded-xl px-3 py-2.5 sm:py-2 transition">
                                <span class="flex items-center gap-2 text-gray-700 truncate">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                                    <i class="fas fa-cubes text-gray-300 text-xs shrink-0"></i>
                                    {{ $p->nombre }}
                                    <span class="font-bold text-gray-900 shrink-0 tabular-nums">×{{ $p->cantidad_solicitada }}</span>
                                </span>
                                <button wire:click="quitarPiezaCantidad({{ $p->id }})" type="button"
                                    class="text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg text-xs w-8 h-8 flex items-center justify-center shrink-0 transition"
                                    title="Quitar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <x-ui.empty-state icon="fa-cubes" message="Aún no agregaste piezas por cantidad" />
                @endif
            @endif
