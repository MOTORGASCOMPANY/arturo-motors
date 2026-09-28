            @php
                $colorMap = ['sellado' => 'amber', 'incompleto' => 'orange', 'completado' => 'purple', 'consumido' => 'red', 'sueltosSerializados' => 'green', 'sueltosCantidad' => 'indigo'];
                $tipoColor = $colorMap[$this->filtroTipoInventario] ?? 'gray';
            @endphp
            @php
                $k = $this->mostrarDetalleKit ? $this->kitDetalle : null;
                $estadoKit = $k ? (match($k->estado) {
                    'en_stock'   => ['label' => 'Sellado',    'chip' => 'bg-green-100 text-green-700',   'icon' => 'fa-box'],
                    'abierto'    => ['label' => 'Abierto',    'chip' => 'bg-orange-100 text-orange-700', 'icon' => 'fa-folder-open'],
                    'completado' => ['label' => 'Completado', 'chip' => 'bg-purple-100 text-purple-700', 'icon' => 'fa-check-circle'],
                    'asignado'   => ['label' => 'Asignado',   'chip' => 'bg-yellow-100 text-yellow-700', 'icon' => 'fa-user-check'],
                    'consumido'  => ['label' => 'Consumido',  'chip' => 'bg-red-100 text-red-700',       'icon' => 'fa-fire'],
                    default      => ['label' => $k->estado,   'chip' => 'bg-gray-100 text-gray-600',     'icon' => 'fa-circle'],
                }) : null;
                $pctCompletado = ($k && $k->totalEsperado > 0) ? round($k->totalPresente / $k->totalEsperado * 100) : 0;
            @endphp

            <x-modal wire:model.live="mostrarDetalleKit" maxWidth="xl">
                @if ($k)
                    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
                        <button type="button" wire:click="volverListado"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="w-10 h-10 rounded-lg bg-{{ $tipoColor }}-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-box text-{{ $tipoColor }}-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-gray-800 truncate">{{ $k->producto?->nombre ?? 'Kit' }}</h3>
                                <span class="px-1.5 py-0.5 bg-gray-900 text-white text-[10px] font-black rounded tabular-nums">#{{ $k->id }}</span>
                                <span class="px-2 py-0.5 {{ $estadoKit['chip'] }} text-[10px] font-bold rounded-full">{{ $estadoKit['label'] }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $k->sede?->nombre ?? '—' }}
                                @if ($k->serie)
                                    <span class="text-gray-300 mx-1">|</span>
                                    <span class="font-mono">{{ $k->serie }}</span>
                                @endif
                            </p>
                        </div>
                        <button type="button" wire:click="volverListado" aria-label="Cerrar"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="max-h-[70vh] overflow-y-auto px-5 py-4 space-y-5">

                            {{-- Progreso --}}
                            @if ($k->totalEsperado > 0)
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1.5">
                                        <span class="font-semibold text-gray-600">Componentes</span>
                                        <span class="font-bold tabular-nums {{ $pctCompletado >= 100 ? 'text-green-600' : 'text-gray-700' }}">{{ $k->totalPresente }}/{{ $k->totalEsperado }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full transition-all {{ $pctCompletado >= 100 ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $pctCompletado }}%"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- Receta --}}
                            @if ($k->recetaDetalles && $k->recetaDetalles->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Receta del kit</p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->recetaDetalles as $r)
                                            <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ $r['completo'] ? 'bg-green-50 border border-green-200' : 'bg-orange-50 border border-orange-200' }}">
                                                <i class="fas {{ $r['es_serializado'] ? 'fa-microchip text-indigo-500' : 'fa-cubes text-amber-500' }} text-xs"></i>
                                                <span class="flex-1 text-sm font-medium text-gray-700">{{ $r['nombre'] }}</span>
                                                <span class="text-xs font-bold tabular-nums {{ $r['completo'] ? 'text-green-700' : 'text-orange-700' }}">{{ $r['presente'] }}/{{ $r['cantidad_esperada'] }}</span>
                                                @if ($r['completo'])
                                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                                @else
                                                    <i class="fas fa-exclamation text-orange-500 text-xs"></i>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Piezas asignadas (activas, resumidas por producto) --}}
                            @if ($k->piezasResumidas && $k->piezasResumidas->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Equipos del kit ({{ $k->totalPresente }})</p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->piezasResumidas as $pr)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                                                <i class="fas fa-microchip text-indigo-500 text-xs shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $pr['nombre'] }}</p>
                                                    @if ($pr['series']->isNotEmpty())
                                                        <p class="text-xs text-gray-400 mt-0.5 font-mono truncate">
                                                            {{ $pr['series']->take(3)->implode(', ') }}{{ $pr['series']->count() > 3 ? ' +' . ($pr['series']->count() - 3) : '' }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($pr['estados']) && $pr['estados']->isNotEmpty())
                                                        <p class="text-[10px] text-gray-400 mt-0.5 capitalize">{{ $pr['estados']->implode(' · ') }}</p>
                                                    @endif
                                                </div>
                                                @if ($pr['cantidad'] > 1)
                                                    <span class="shrink-0 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-black rounded-full tabular-nums">×{{ $pr['cantidad'] }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Reemplazados / defectuosos (histórico de la conversión) --}}
                            @if ($k->reemplazados && $k->reemplazados->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-2">
                                        <i class="fas fa-exchange-alt mr-1"></i>Reemplazados / fuera de servicio ({{ $k->reemplazados->count() }})
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->reemplazados as $rep)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-amber-50 rounded-lg border border-amber-200">
                                                <i class="fas fa-triangle-exclamation text-amber-500 text-xs shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-amber-900 truncate">{{ $rep->producto?->nombre ?? '—' }}</p>
                                                    @if ($rep->serie)
                                                        <p class="text-xs text-amber-600 mt-0.5 font-mono">{{ $rep->serie }}</p>
                                                    @endif
                                                </div>
                                                <span class="shrink-0 px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">{{ $rep->estado }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Cantidad extra / repuestos de la orden (azul) --}}
                            @if ($k->itemsExtraOrden && $k->itemsExtraOrden->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-2">
                                        <i class="fas fa-plus-circle mr-1"></i>Cantidad extra asignada ({{ $k->itemsExtraOrden->count() }})
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->itemsExtraOrden as $extra)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-blue-50 rounded-lg border border-blue-200">
                                                <i class="fas fa-cubes text-blue-500 text-xs shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-blue-800 truncate">{{ $extra->producto?->nombre ?? '—' }}</p>
                                                    @if ($extra->serie)
                                                        <p class="text-xs text-blue-500 mt-0.5 font-mono">#{{ $extra->serie }}</p>
                                                    @elseif (($extra->atributos['cantidad_solicitada'] ?? null))
                                                        <p class="text-xs text-blue-500 mt-0.5">Cantidad: {{ $extra->atributos['cantidad_solicitada'] }}</p>
                                                    @endif
                                                </div>
                                                <span class="shrink-0 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full">{{ $extra->estado }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Movimientos de stock de la conversión (kits consumidos) --}}
                            @if ($k->movimientosConversion && $k->movimientosConversion->isNotEmpty())
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        <i class="fas fa-exchange-alt mr-1"></i>Movimientos de la conversión ({{ $k->movimientosConversion->count() }})
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($k->movimientosConversion as $mov)
                                            <div class="flex items-center gap-2.5 px-3 py-2 bg-white rounded-lg border border-gray-200">
                                                <span class="shrink-0 w-14 text-center text-[10px] font-black uppercase rounded {{ $mov->tipo === 'entrada' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $mov->tipo }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-700 truncate">{{ $mov->producto?->nombre ?? '—' }}</p>
                                                    @if ($mov->motivo)
                                                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $mov->motivo }}</p>
                                                    @endif
                                                </div>
                                                <span class="shrink-0 text-xs font-bold tabular-nums text-gray-700">×{{ $mov->cantidad }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Orden de servicio --}}
                            @if ($k->serviceOrder)
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Orden de servicio</p>
                                    <div class="bg-gray-50 rounded-lg border border-gray-100 px-3 py-2.5 space-y-1">
                                        <p class="text-sm font-bold text-gray-800">
                                            <i class="fas fa-file-alt text-gray-400 mr-1"></i>Orden #{{ $k->service_order_id }}
                                        </p>
                                        @if ($k->serviceOrder->cliente)
                                            <p class="text-xs text-gray-500"><i class="fas fa-user text-gray-400 mr-1"></i>{{ $k->serviceOrder->cliente->nombre . ' ' . $k->serviceOrder->cliente->apellido }}</p>
                                        @endif
                                        @if ($k->serviceOrder->vehiculo)
                                            <p class="text-xs text-gray-500"><i class="fas fa-car text-gray-400 mr-1"></i>{{ $k->serviceOrder->vehiculo->marca }} {{ $k->serviceOrder->vehiculo->modelo }} — {{ $k->serviceOrder->vehiculo->placa }}</p>
                                        @endif
                                        @if ($k->serviceOrder->tecnico)
                                            <p class="text-xs text-gray-500"><i class="fas fa-wrench text-gray-400 mr-1"></i>{{ $k->serviceOrder->tecnico->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Datos del item --}}
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Datos del item</p>
                                <div class="bg-gray-50 rounded-lg border border-gray-100 px-3 py-2.5">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-gray-400">Creado</span>
                                            <p class="font-medium text-gray-700">{{ $k->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Sede</span>
                                            <p class="font-medium text-gray-700">{{ $k->sede?->nombre ?? '—' }}</p>
                                        </div>
                                        @if ($k->atributos)
                                            @foreach ($k->atributos as $key => $val)
                                                @if ($val)
                                                    @php
                                                        $displayKey = ucfirst(str_replace('_', ' ', $key));
                                                        $displayVal = $val;
                                                        // Formatear campos especiales
                                                        if ($key === 'abierto_por' && is_numeric($val)) {
                                                            $user = \App\Models\User::find($val);
                                                            $displayVal = $user?->name ?? "Usuario #{$val}";
                                                        } elseif (in_array($key, ['abierto_en', 'recepcion_fecha', 'fecha_recepcion']) && $val) {
                                                            try {
                                                                $displayVal = \Carbon\Carbon::parse($val)->format('d/m/Y H:i');
                                                            } catch (\Throwable) {}
                                                        } elseif ($key === 'motivo_apertura') {
                                                            $displayKey = 'Motivo apertura';
                                                        }
                                                    @endphp
                                                    <div>
                                                        <span class="text-gray-400">{{ $displayKey }}</span>
                                                        <p class="font-medium text-gray-700">{{ $displayVal }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center gap-3 px-5 py-3 border-t border-gray-200 bg-gray-50">
                            @if ($k->estado === 'abierto')
                                <button type="button" wire:click="abrirEditarItem({{ $k->id }})"
                                    class="px-4 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </button>
                            @endif
                            @if ($k->estado === 'abierto' && $k->producto->categoria->es_kit)
                                <div class="ml-auto">
                                    @if ($k->tieneFaltantes && !$k->todosConStock)
                                        <button type="button" disabled
                                            class="px-5 py-2 text-sm font-bold text-white bg-gray-400 rounded-lg cursor-not-allowed"
                                            title="Faltan componentes y no hay stock disponible">
                                            <i class="fas fa-clock mr-1"></i> Espera stock para completar
                                        </button>
                                    @else
                                        <button type="button" wire:click="abrirCompletarKit({{ $k->id }})"
                                            class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                            <i class="fas fa-plus mr-1"></i> Completar kit
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </x-modal>