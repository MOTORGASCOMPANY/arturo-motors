                    <div class="flex-1 min-h-0 flex flex-col lg:border-r border-gray-100">
                        <div class="px-4 sm:px-5 pt-4 pb-2 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-box text-gray-400 text-sm"></i>
                                <h3 class="text-sm font-bold text-gray-900">Piezas del kit</h3>
                            </div>
                            <button wire:click="abrirPartesGenerales" class="text-xs text-gray-500 hover:text-gray-900 transition py-1 px-1">
                                <i class="fas fa-eye mr-1"></i> Ver todos
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto px-4 sm:px-5 pb-4">
                            @php
                                $todosItems = collect();
                                foreach($this->kitItems as $item) {
                                    $todosItems->push((object)[
                                        'id' => $item->id,
                                        'nombre' => $item->producto->nombre,
                                        'detalle' => 'Serie: ' . $item->serie,
                                        'icono' => 'fa-microchip',
                                        'tipo' => 'serial',
                                        'cantidad' => 1,
                                        'reemplazado' => in_array($item->id, $this->itemsReemplazados),
                                    ]);
                                }
                                // Por cantidad: agrupar por producto (receta x2 no son "duplicados")
                                foreach($this->itemsCantidad->groupBy('producto_id') as $grupo) {
                                    $first = $grupo->first();
                                    $qty = $grupo->count();
                                    $reemplazado = $grupo->contains(fn ($i) => in_array($i->id, $this->itemsReemplazados));
                                    $todosItems->push((object)[
                                        'id' => $first->id,
                                        'nombre' => $first->producto->nombre,
                                        'detalle' => $qty > 1 ? "Por cantidad ×{$qty}" : 'Por cantidad',
                                        'icono' => 'fa-cubes',
                                        'tipo' => 'cantidad',
                                        'cantidad' => $qty,
                                        'reemplazado' => $reemplazado,
                                    ]);
                                }
                            @endphp

                            @if($todosItems->isEmpty())
                                <p class="text-sm text-gray-400 text-center py-6">No se encontr&oacute; kit asignado</p>
                            @else
                                <div class="flex flex-col gap-2">
                                    @foreach($todosItems as $item)
                                        @php
                                            $seleccionada = $this->piezaReemplazarId === $item->id;
                                        @endphp
                                        <div class="border rounded-lg transition-all {{ $seleccionada ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-200' : ($item->reemplazado ? 'border-green-300 bg-green-50' : 'border-gray-200') }}">

                                            <div wire:click="seleccionarPieza({{ $item->id }})"
                                                 class="w-full flex items-center gap-3 p-3 sm:p-2.5 cursor-pointer transition hover:opacity-80">
                                                <div class="w-8 h-8 {{ $item->reemplazado ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center shrink-0">
                                                    @if($item->reemplazado)
                                                        <i class="fas fa-check text-green-500 text-xs"></i>
                                                    @else
                                                        <i class="fas {{ $item->icono }} text-gray-400 text-xs"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0 text-left">
                                                    <span class="text-xs font-semibold text-gray-900 block truncate">{{ $item->nombre }}</span>
                                                    <span class="text-[11px] text-gray-400">{{ $item->detalle }}</span>
                                                </div>
                                                @if($item->reemplazado)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded shrink-0">OK</span>
                                                @elseif($item->tipo === 'cantidad' && ($item->cantidad ?? 1) > 1)
                                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded shrink-0">×{{ $item->cantidad }}</span>
                                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded shrink-0">Cantidad</span>
                                                @else
                                                    <span class="px-2 py-0.5 {{ $item->tipo === 'cantidad' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500' }} text-[10px] font-bold rounded shrink-0">{{ $item->tipo === 'cantidad' ? 'Cantidad' : 'Serial' }}</span>
                                                @endif
                                            </div>

                                            @if($seleccionada && $this->metodoReemplazo === 'buscando_suelta')
                                                <div class="px-3 sm:px-4 pb-3 pt-0 border-t border-blue-200">
                                                    <div class="sm:ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-blue-700 font-semibold"><i class="fas fa-list mr-1"></i>Piezas sueltas disponibles — elegí una:</p>
                                                        @foreach($this->piezasSueltas as $suelta)
                                                            @php
                                                                $esElegida = $this->piezaSueltaSeleccionadaId === $suelta['id'];
                                                                $attrs = $suelta['atributos'];
                                                            @endphp
                                                            <div wire:click="seleccionarPiezaSuelta({{ $suelta['id'] }})"
                                                                 class="border rounded-lg p-2.5 cursor-pointer transition flex items-start gap-2 bg-white {{ $esElegida ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-200' : 'border-gray-200 hover:border-blue-300' }}">
                                                                <div class="w-7 h-7 rounded-lg {{ $esElegida ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shrink-0 mt-0.5">
                                                                    <i class="fas fa-microchip text-xs"></i>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <span class="text-xs font-bold text-gray-900 block">{{ $suelta['serie'] }}</span>
                                                                    <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-1">
                                                                        @if(!empty($attrs['marca']))
                                                                            <span class="text-[10px] text-gray-500"><span class="font-semibold">Marca:</span> {{ $attrs['marca'] }}</span>
                                                                        @endif
                                                                        @if(!empty($attrs['generacion']))
                                                                            <span class="text-[10px] text-gray-500"><span class="font-semibold">Gen:</span> {{ $attrs['generacion'] }}</span>
                                                                        @endif
                                                                        @if(!empty($attrs['capacidad']))
                                                                            <span class="text-[10px] text-gray-500"><span class="font-semibold">Capac.:</span> {{ $attrs['capacidad'] }}</span>
                                                                        @endif
                                                                        @if(!empty($attrs['produce']))
                                                                            <span class="text-[10px] text-gray-500"><span class="font-semibold">Produce:</span> {{ $attrs['produce'] }}</span>
                                                                        @endif
                                                                        @if(!empty($attrs['proveedor']))
                                                                            <span class="text-[10px] text-gray-400"><span class="font-semibold">Prov:</span> {{ $attrs['proveedor'] }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                @if($esElegida)
                                                                    <i class="fas fa-check-circle text-blue-500 text-sm mt-1"></i>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                                            @if($this->piezaSueltaSeleccionadaId)
                                                                <button wire:click="confirmarPiezaSuelta" wire:loading.attr="disabled"
                                                                        class="px-4 py-2 sm:py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
                                                                    <i class="fas fa-check mr-1"></i> Asignar
                                                                </button>
                                                            @endif
                                                            <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700 py-2 sm:py-0"><i class="fas fa-times mr-1"></i>Cancelar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($seleccionada && $this->metodoReemplazo === 'buscando_kit')
                                                <div class="px-3 sm:px-4 pb-3 pt-0 border-t border-blue-200">
                                                    <div class="sm:ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-amber-700 font-semibold"><i class="fas fa-info-circle mr-1"></i>Sin pieza suelta. Seleccione un kit para extraer:</p>
                                                        @foreach($this->kitsDisponibles as $kit)
                                                            @php
                                                                $compNecesaria = collect($kit['componentes'])->firstWhere('es_necesaria', true);
                                                            @endphp
                                                            <div wire:click="seleccionarKit({{ $kit['id'] }})"
                                                                 class="border border-gray-200 rounded-lg p-2.5 cursor-pointer hover:border-purple-400 transition bg-white">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <div class="min-w-0">
                                                                        <span class="text-xs font-medium text-gray-900">{{ $kit['producto'] }}</span>
                                                                        <span class="text-[11px] text-gray-400 ml-1 font-mono">{{ $kit['serie'] ?? '' }}</span>
                                                                    </div>
                                                                    <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0"></i>
                                                                </div>
                                                                <div class="flex flex-wrap gap-1 mt-1.5">
                                                                    @foreach($kit['componentes'] as $comp)
                                                                        <span class="text-[10px] px-1.5 py-0.5 rounded {{ $comp['es_necesaria'] ? 'bg-purple-100 text-purple-700 font-semibold' : 'bg-gray-100 text-gray-500' }}">
                                                                            {{ $comp['nombre'] }}
                                                                        </span>
                                                                    @endforeach
                                                                    @if(!empty($kit['serie_disponible']))
                                                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-600 text-white font-mono font-bold">
                                                                            <i class="fas fa-barcode mr-1"></i>{{ $kit['serie_disponible'] }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700 py-2 sm:py-0"><i class="fas fa-times mr-1"></i>Cancelar</button>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($seleccionada && $this->metodoReemplazo === 'kit_con_serie')
                                                <div class="px-3 sm:px-4 pb-3 pt-0 border-t border-purple-200">
                                                    <div class="sm:ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-purple-700 font-semibold"><i class="fas fa-barcode mr-1"></i>El kit ya tiene serie — se usa esa pieza directamente:</p>
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-mono font-bold rounded-lg">{{ $this->piezaKitConSerie['serie'] ?? '' }}</span>
                                                            <span class="text-[11px] text-gray-500">(la pieza se mueve al kit de la orden)</span>
                                                        </div>
                                                        <div>
                                                            <label class="text-[11px] text-gray-500 font-medium block mb-1">Observaci&oacute;n (opcional)</label>
                                                            <input type="text" wire:model.live="observacion" placeholder="Motivo del reemplazo..."
                                                                   class="w-full px-3 py-2 sm:py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-purple-500 outline-none">
                                                        </div>
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <button wire:click="confirmarReemplazoKit" wire:loading.attr="disabled"
                                                                    class="px-4 py-2 sm:py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition"><i class="fas fa-check mr-1"></i> Reemplazar</button>
                                                            <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700 px-1 py-2 sm:py-0"><i class="fas fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($seleccionada && $this->metodoReemplazo === 'kit_seleccionado')
                                                <div class="px-3 sm:px-4 pb-3 pt-0 border-t border-purple-200">
                                                    <div class="sm:ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-purple-700 font-semibold"><i class="fas fa-box-open mr-1"></i>Ingrese el serial:</p>
                                                        <input type="text" wire:model.live="nuevaSerie" placeholder="Ej: 31312313"
                                                               class="w-full px-3 py-2 sm:py-1.5 border border-gray-300 rounded-lg text-xs font-mono focus:ring-1 focus:ring-purple-500 outline-none">
                                                        <div>
                                                            <label class="text-[11px] text-gray-500 font-medium block mb-1">Observaci&oacute;n (opcional)</label>
                                                            <input type="text" wire:model.live="observacion" placeholder="Motivo del reemplazo..."
                                                                   class="w-full px-3 py-2 sm:py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-purple-500 outline-none">
                                                        </div>
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <button wire:click="confirmarReemplazoKit" wire:loading.attr="disabled"
                                                                    class="px-4 py-2 sm:py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">Confirmar</button>
                                                            <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700 px-1 py-2 sm:py-0"><i class="fas fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($seleccionada && $this->metodoReemplazo === 'cantidad')
                                                <div class="px-3 sm:px-4 pb-3 pt-0 border-t border-orange-200">
                                                    <div class="sm:ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-orange-700 font-semibold"><i class="fas fa-cubes mr-1"></i>&iquest;Cu&aacute;ntas unidades? <span class="font-normal text-orange-500">(Disponible: {{ $this->stockDisponible }})</span></p>
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white">
                                                                <button wire:click="$set('cantidadAdicional', Math.max(1, parseInt($wire.cantidadAdicional) - 1))"
                                                                        class="px-3 py-2 sm:px-2.5 sm:py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" wire:model.live="cantidadAdicional" min="1" max="{{ $this->stockDisponible }}"
                                                                       class="w-12 px-1 py-2 sm:py-1.5 text-center border-0 text-xs font-bold focus:ring-0 outline-none">
                                                                <button wire:click="$set('cantidadAdicional', Math.min({{ $this->stockDisponible }}, parseInt($wire.cantidadAdicional) + 1))"
                                                                        class="px-3 py-2 sm:px-2.5 sm:py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="text-[11px] text-gray-500 font-medium block mb-1">Observaci&oacute;n (opcional)</label>
                                                            <input type="text" wire:model.live="observacion" placeholder="Motivo del despacho..."
                                                                   class="w-full px-3 py-2 sm:py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-orange-500 outline-none">
                                                        </div>
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <button wire:click="confirmarCantidadAdicional" wire:loading.attr="disabled"
                                                                    @disabled((int) $this->cantidadAdicional < 1 || (int) $this->cantidadAdicional > $this->stockDisponible)
                                                                    class="px-4 py-2 sm:py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition">
                                                                <i class="fas fa-check mr-1"></i> Despachar
                                                            </button>
                                                            <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700 py-2 sm:py-0"><i class="fas fa-times mr-1"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
