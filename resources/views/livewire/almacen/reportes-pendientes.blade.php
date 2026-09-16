<div wire:poll.10s class="max-w-[1600px] mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">
                <i class="fas fa-desktop mr-2 text-blue-600"></i>Conversiones Activas
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Monitoreo en tiempo real</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" wire:model.live="busqueda" placeholder="Buscar..."
                       class="w-56 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-gray-900 outline-none">
                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
            <span class="flex items-center gap-1.5 text-xs text-gray-400">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span> Live
            </span>
        </div>
    </div>

    {{-- TABLA --}}
    @if($this->conversiones->isEmpty())
        <div class="bg-white rounded-lg border border-gray-200 p-10 text-center">
            <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
            <p class="text-gray-500 font-medium">No hay conversiones activas</p>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gray-50 border-b border-gray-200 text-left">
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Orden</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Cliente</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Placa</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Kit</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">T&eacute;cnico</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Inicio</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Fin</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($this->conversiones as $orden)
                            @php $r = $this->resumenConversion($orden); @endphp
                            <tr wire:click="abrirModal({{ $orden->id }})"
                                class="transition cursor-pointer {{ $orden->fecha_inicio_conversion ? 'hover:bg-gray-50' : 'bg-gray-50 cursor-not-allowed opacity-60' }}">
                                <td class="px-3 py-2.5 font-bold text-gray-900">#{{ $orden->id }}</td>
                                <td class="px-3 py-2.5 text-gray-700 whitespace-nowrap">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td>
                                <td class="px-3 py-2.5"><span class="font-mono font-semibold text-gray-900">{{ $orden->vehiculo->placa }}</span></td>
                                <td class="px-3 py-2.5 text-gray-700 text-xs">{{ $this->nombreKit($orden) }}</td>
                                <td class="px-3 py-2.5 text-gray-700 text-xs whitespace-nowrap">{{ $orden->tecnico->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-500 text-xs whitespace-nowrap">{{ $orden->fecha_inicio_conversion ? $orden->fecha_inicio_conversion->format('d/m H:i') : '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-500 text-xs whitespace-nowrap">{{ $orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m H:i') : '—' }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($orden->fecha_inicio_conversion)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">En conversi&oacute;n</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Sin iniciar</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($this->conversiones->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">{{ $this->conversiones->links('pagination::tailwind') }}</div>
            @endif
        </div>
    @endif

    {{-- ═══ MODAL ═══ --}}
    @if($modalAbierto && $this->conversionSeleccionada)
        @php $orden = $this->conversionSeleccionada; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px]" wire:click="cerrarModal"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col ring-1 ring-black/5">

                {{-- Header --}}
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-car text-white"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-white text-base">Orden #{{ $orden->id }}</span>
                                <span class="px-2 py-0.5 bg-white/20 text-white text-xs font-mono font-semibold rounded">{{ $orden->vehiculo->placa }}</span>
                                @if($this->generacionKit)
                                    <span class="px-2 py-0.5 bg-purple-500 text-white text-[10px] font-bold rounded">{{ $this->generacionKit }}</span>
                                @endif
                            </div>
                            <span class="text-sm text-blue-100">
                                {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                                &middot; T&eacute;c: {{ $orden->tecnico->name ?? '—' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-white text-blue-700 text-xs font-bold rounded-full">En conversi&oacute;n</span>
                        <button wire:click="cerrarModal" class="w-8 h-8 flex items-center justify-center text-white/80 hover:text-white hover:bg-white/15 rounded-lg transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Contenido: 2 columnas --}}
                <div class="flex-1 min-h-0 flex overflow-hidden">

                    {{-- Columna izquierda: piezas --}}
                    <div class="flex-1 flex flex-col min-h-0 border-r border-gray-100">
                        <div class="px-5 pt-4 pb-2 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-box text-gray-400 text-sm"></i>
                                <h3 class="text-sm font-bold text-gray-900">Piezas del kit</h3>
                            </div>
                            <button wire:click="abrirPartesGenerales" class="text-xs text-gray-500 hover:text-gray-900 transition">
                                <i class="fas fa-eye mr-1"></i> Ver todos
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto px-5 pb-4">
                            @php
                                $todosItems = collect();
                                foreach($this->kitItems as $item) {
                                    $todosItems->push((object)[
                                        'id' => $item->id,
                                        'nombre' => $item->producto->nombre,
                                        'detalle' => 'Serie: ' . $item->serie,
                                        'icono' => 'fa-microchip',
                                        'tipo' => 'serial',
                                        'reemplazado' => in_array($item->id, $this->itemsReemplazados),
                                    ]);
                                }
                                foreach($this->itemsCantidad as $item) {
                                    $todosItems->push((object)[
                                        'id' => $item->id,
                                        'nombre' => $item->producto->nombre,
                                        'detalle' => 'Por cantidad',
                                        'icono' => 'fa-cubes',
                                        'tipo' => 'cantidad',
                                        'reemplazado' => in_array($item->id, $this->itemsReemplazados),
                                    ]);
                                }
                            @endphp

                            @if($todosItems->isEmpty())
                                <p class="text-sm text-gray-400 text-center py-6">No se encontr&oacute; kit asignado</p>
                            @else
                                <div class="flex flex-col gap-2">
                                    @foreach($todosItems as $item)
                                        @php
                                            $seleccionada = $piezaReemplazarId === $item->id;
                                        @endphp
                                        <div class="border rounded-lg transition-all {{ $seleccionada ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-200' : ($item->reemplazado ? 'border-green-300 bg-green-50' : 'border-gray-200') }}">

                                            {{-- Fila --}}
                                            <div wire:click="seleccionarPieza({{ $item->id }})"
                                                 class="w-full flex items-center gap-3 p-2.5 cursor-pointer transition hover:opacity-80">
                                                <div class="w-8 h-8 {{ $item->reemplazado ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center shrink-0">
                                                    @if($item->reemplazado)
                                                        <i class="fas fa-check text-green-500 text-xs"></i>
                                                    @else
                                                        <i class="fas {{ $item->icono }} text-gray-400 text-xs"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0 text-left">
                                                    <span class="text-xs font-semibold text-gray-900 block">{{ $item->nombre }}</span>
                                                    <span class="text-[11px] text-gray-400">{{ $item->detalle }}</span>
                                                </div>
                                                @if($item->reemplazado)
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded">OK</span>
                                                @else
                                                    <span class="px-2 py-0.5 {{ $item->tipo === 'cantidad' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500' }} text-[10px] font-bold rounded">{{ $item->tipo === 'cantidad' ? 'Cantidad' : 'Serial' }}</span>
                                                @endif
                                            </div>

                                            {{-- BUSCANDO KIT --}}
                                            @if($seleccionada && $metodoReemplazo === 'buscando_kit')
                                                <div class="px-4 pb-3 pt-0 border-t border-blue-200">
                                                    <div class="ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-amber-700 font-semibold"><i class="fas fa-info-circle mr-1"></i>Sin pieza suelta. Seleccione un kit:</p>
                                                        @foreach($kitsDisponibles as $kit)
                                                            <div wire:click="seleccionarKit({{ $kit['id'] }})"
                                                                 class="border border-gray-200 rounded-lg p-2.5 cursor-pointer hover:border-purple-400 transition flex items-center justify-between bg-white">
                                                                <div class="min-w-0">
                                                                    <span class="text-xs font-medium text-gray-900">{{ $kit['producto'] }}</span>
                                                                    <span class="text-[11px] text-gray-400 ml-1 font-mono">{{ $kit['serie'] ?? '' }}</span>
                                                                </div>
                                                                <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                                                            </div>
                                                        @endforeach
                                                        <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700"><i class="fas fa-times mr-1"></i>Cancelar</button>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- KIT SELECCIONADO → PEDIR SERIE --}}
                                            @if($seleccionada && $metodoReemplazo === 'kit_seleccionado')
                                                <div class="px-4 pb-3 pt-0 border-t border-purple-200">
                                                    <div class="ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-purple-700 font-semibold"><i class="fas fa-box-open mr-1"></i>Ingrese el serial:</p>
                                                        <input type="text" wire:model.live="nuevaSerie" placeholder="Ej: 31312313"
                                                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-mono focus:ring-1 focus:ring-purple-500 outline-none">
                                                        <div>
                                                            <label class="text-[11px] text-gray-500 font-medium block mb-1">Observaci&oacute;n (opcional)</label>
                                                            <input type="text" wire:model.live="observacion" placeholder="Motivo del reemplazo..."
                                                                   class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-purple-500 outline-none">
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button wire:click="confirmarReemplazoKit" wire:loading.attr="disabled"
                                                                    class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">Confirmar</button>
                                                            <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700 px-1"><i class="fas fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- CANTIDAD --}}
                                            @if($seleccionada && $metodoReemplazo === 'cantidad')
                                                <div class="px-4 pb-3 pt-0 border-t border-orange-200">
                                                    <div class="ml-11 mt-2 space-y-2">
                                                        <p class="text-xs text-orange-700 font-semibold"><i class="fas fa-cubes mr-1"></i>&iquest;Cu&aacute;ntas unidades?</p>
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white">
                                                                <button wire:click="$set('cantidadAdicional', Math.max(1, parseInt($wire.cantidadAdicional) - 1))"
                                                                        class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" wire:model.live="cantidadAdicional" min="1" max="100"
                                                                       class="w-12 px-1 py-1.5 text-center border-0 text-xs font-bold focus:ring-0 outline-none">
                                                                <button wire:click="$set('cantidadAdicional', Math.min(100, parseInt($wire.cantidadAdicional) + 1))"
                                                                        class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="text-[11px] text-gray-500 font-medium block mb-1">Observaci&oacute;n (opcional)</label>
                                                            <input type="text" wire:model.live="observacion" placeholder="Motivo del despacho..."
                                                                   class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-1 focus:ring-orange-500 outline-none">
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button wire:click="confirmarCantidadAdicional" wire:loading.attr="disabled"
                                                                    class="px-4 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded-lg transition">
                                                                <i class="fas fa-check mr-1"></i> Despachar
                                                            </button>
                                                            <button wire:click="cancelarReemplazo" class="text-xs text-gray-400 hover:text-gray-700"><i class="fas fa-times mr-1"></i></button>
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

                    {{-- Columna derecha: historial --}}
                    <div class="w-72 flex flex-col min-h-0 bg-gray-50/50">
                        {{-- Despachados (ReportePiezaNoEncajada) --}}
                        <div class="px-4 pt-4 pb-2 flex items-center gap-2 shrink-0">
                            <i class="fas fa-check-double text-green-500 text-sm"></i>
                            <h3 class="text-sm font-bold text-gray-900">Despachados</h3>
                            @if($this->historial->isNotEmpty())
                                <span class="ml-auto px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded">{{ $this->historial->count() }}</span>
                            @endif
                        </div>
                        <div class="flex-1 overflow-y-auto px-3 pb-3">
                            @if($this->historial->isEmpty())
                                <p class="text-xs text-gray-400 text-center py-6">Sin despachos todav&iacute;a</p>
                            @else
                                <div class="space-y-1.5">
                                    @foreach($this->historial as $h)
                                        <div class="flex items-start gap-2 p-2 bg-white border border-green-200 rounded-lg">
                                            <div class="w-6 h-6 shrink-0 bg-green-100 rounded flex items-center justify-center mt-0.5">
                                                <i class="fas fa-check text-green-600 text-[9px]"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <span class="text-xs font-medium text-gray-900 block truncate">{{ $h->pieza }}</span>
                                                <span class="text-[11px] text-gray-500 block">
                                                    @if($h->serie_vieja !== '—') {{ $h->serie_vieja }} &mdash; @endif {{ $h->motivo }}
                                                </span>
                                                <span class="text-[10px] text-gray-400">{{ $h->fecha }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Timeline de la conversión (ServiceOrderStatusHistory) --}}
                        <div class="border-t border-gray-200/60 shrink-0">
                            <div class="px-4 pt-3 pb-2 flex items-center gap-2">
                                <i class="fas fa-history text-purple-500 text-sm"></i>
                                <h3 class="text-sm font-bold text-gray-900">Línea de tiempo</h3>
                            </div>
                            <div class="flex-1 min-h-0 overflow-y-auto px-3 pb-3">
                                @if($this->timelineConversion->isEmpty())
                                    <p class="text-xs text-gray-400 text-center py-4">Sin movimientos</p>
                                @else
                                    <div class="relative ml-2 border-l border-gray-200 space-y-3">
                                        @foreach($this->timelineConversion as $t)
                                            <div class="relative pl-3">
                                                <div class="absolute -left-[6px] top-0.5 w-3 h-3 rounded-full {{ $t->esConversion ? 'bg-purple-400' : 'bg-gray-300' }} border-2 border-white"></div>
                                                <div class="text-[10px]">
                                                    <p class="font-semibold text-gray-800">
                                                        {{ ucfirst(str_replace('_', ' ', $t->estado_nuevo)) }}
                                                    </p>
                                                    @if($t->estado_anterior)
                                                        <p class="text-gray-500">Ant: {{ ucfirst(str_replace('_', ' ', $t->estado_anterior)) }}</p>
                                                    @endif
                                                    <p class="text-gray-400 mt-0.5">
                                                        {{ $t->fecha }} &mdash; {{ $t->usuario }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex justify-end shrink-0">
                    <button wire:click="cerrarModal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-xs">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL COMPONENTES --}}
    @if($modalPartesAbierto)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarPartesGenerales"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-cogs text-gray-400 text-sm"></i>
                        <h3 class="font-bold text-gray-900 text-sm">Componentes del kit</h3>
                        @if($this->generacionKit)
                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded">{{ $this->generacionKit }}</span>
                        @endif
                    </div>
                    <button wire:click="cerrarPartesGenerales" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-5 py-4 max-h-[28rem] overflow-y-auto">
                    @if($this->todasPiezasKit->isNotEmpty())
                        <p class="text-xs text-gray-500 mb-3">{{ $this->todasPiezasKit->count() }} piezas</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                            @foreach($this->todasPiezasKit as $comp)
                                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-50">
                                    <span class="text-sm font-medium text-gray-900 truncate">{{ $comp->nombre }}</span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-xs font-bold text-gray-700">x{{ $comp->cantidad_esperada }}</span>
                                        <span class="px-1.5 py-0.5 {{ $comp->es_serializado ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-600' }} text-[10px] font-semibold rounded">
                                            {{ $comp->es_serializado ? 'Serial' : 'Cant.' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button wire:click="cerrarPartesGenerales" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-xs">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>