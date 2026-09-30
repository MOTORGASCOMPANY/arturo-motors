            {{-- ═══ Detalle de pieza serializada suelta (clon del modal de detalle de kit) ═══ --}}
            @php
                $pd = $piezaDetalle ?? null;
                $estadoPieza = $pd ? (match($pd->estado) {
                    'en_stock'   => ['label' => 'En stock',  'chip' => 'bg-green-100 text-green-700',  'icon' => 'fa-box'],
                    'asignado'   => ['label' => 'Asignado',  'chip' => 'bg-yellow-100 text-yellow-700','icon' => 'fa-user-check'],
                    'instalado'  => ['label' => 'Instalado', 'chip' => 'bg-blue-100 text-blue-700',    'icon' => 'fa-car'],
                    'devuelto'   => ['label' => 'Devuelto',  'chip' => 'bg-gray-100 text-gray-700',    'icon' => 'fa-undo'],
                    'defectuoso' => ['label' => 'Defectuoso','chip' => 'bg-red-100 text-red-700',      'icon' => 'fa-triangle-exclamation'],
                    default      => ['label' => $pd->estado, 'chip' => 'bg-gray-100 text-gray-600',     'icon' => 'fa-circle'],
                }) : null;
            @endphp

            @if ($pd)
            <x-dialog-modal wire:model.live="modalDetallePiezaAbierto" maxWidth="xl">
                <x-slot name="title">
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="cerrarDetallePieza"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-barcode text-green-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-white truncate">{{ $pd->producto?->nombre ?? 'Pieza' }}</h3>
                                @if ($pd->serie)
                                    <span class="font-mono text-xs text-slate-200 bg-white/10 px-2 py-0.5 rounded">{{ $pd->serie }}</span>
                                @endif
                                <span class="px-2 py-0.5 {{ $estadoPieza['chip'] }} text-[10px] font-bold rounded-full">{{ $estadoPieza['label'] }}</span>
                            </div>
                            <p class="text-sm text-slate-300 mt-0.5">{{ $pd->sede?->nombre ?? '—' }}</p>
                        </div>
                        <button type="button" wire:click="cerrarDetallePieza" aria-label="Cerrar"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="max-h-[70vh] overflow-y-auto space-y-5">

                        {{-- Ingreso / registro (mapeo BD: atributos + ledger movimientos_stock) --}}
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Ingreso y registro</p>
                            <div class="bg-gray-50 rounded-lg border border-gray-100 px-3 py-2.5">
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <span class="text-gray-400">Cómo ingresó</span>
                                        <p class="font-medium text-gray-700 mt-0.5">
                                            <i class="fas fa-sign-in-alt text-green-500 mr-1"></i>{{ $pd->_origen }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Fecha</span>
                                        <p class="font-medium text-gray-700 mt-0.5">
                                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>{{ $pd->_fechaRegistro }}
                                        </p>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-400">Registrado por</span>
                                        <p class="font-medium text-gray-700 mt-0.5">
                                            <i class="fas fa-user text-gray-400 mr-1"></i>{{ $pd->_registradoPor }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Orden de servicio --}}
                        @if ($pd->serviceOrder)
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Orden de servicio</p>
                                <div class="bg-gray-50 rounded-lg border border-gray-100 px-3 py-2.5 space-y-1">
                                    <p class="text-sm font-bold text-gray-800">
                                        <i class="fas fa-file-alt text-gray-400 mr-1"></i>Orden #{{ $pd->service_order_id }}
                                    </p>
                                    @if ($pd->serviceOrder->cliente)
                                        <p class="text-xs text-gray-500"><i class="fas fa-user text-gray-400 mr-1"></i>{{ $pd->serviceOrder->cliente->nombre . ' ' . $pd->serviceOrder->cliente->apellido }}</p>
                                    @endif
                                    @if ($pd->serviceOrder->vehiculo)
                                        <p class="text-xs text-gray-500"><i class="fas fa-car text-gray-400 mr-1"></i>{{ $pd->serviceOrder->vehiculo->marca }} {{ $pd->serviceOrder->vehiculo->modelo }} — {{ $pd->serviceOrder->vehiculo->placa }}</p>
                                    @endif
                                    @if ($pd->serviceOrder->tecnico)
                                        <p class="text-xs text-gray-500"><i class="fas fa-wrench text-gray-400 mr-1"></i>{{ $pd->serviceOrder->tecnico->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Kit padre (si fue componente devuelto al almacén) --}}
                        @if ($pd->kitPadre)
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Kit de origen</p>
                                <div class="bg-indigo-50 rounded-lg border border-indigo-100 px-3 py-2.5">
                                    <p class="text-sm font-bold text-indigo-800">
                                        <i class="fas fa-box text-indigo-500 mr-1"></i>{{ $pd->kitPadre->producto?->nombre ?? 'Kit' }}
                                        <span class="px-1.5 py-0.5 bg-gray-900 text-white text-[10px] font-black rounded tabular-nums ml-1">#{{ $pd->kit_padre_id }}</span>
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="cerrarDetallePieza">
                        Cerrar
                    </x-secondary-button>
                </x-slot>
            </x-dialog-modal>
            @endif