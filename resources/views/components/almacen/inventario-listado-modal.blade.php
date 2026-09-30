            @php
                $colorMap = ['sellado' => 'amber', 'incompleto' => 'orange', 'completado' => 'purple', 'consumido' => 'red', 'sueltosSerializados' => 'green', 'sueltosCantidad' => 'indigo'];
                $tipoColor = $colorMap[$this->filtroTipoInventario] ?? 'gray';
            @endphp

            {{-- ═══ NIVEL 1 y NIVEL 2: sin cambios (modales) ═══ --}}
            @php
                $listaItems = ($this->modalListadoAbierto ? ($this->listadoInventario ?? collect()) : collect());
                $esKits = !in_array($this->filtroTipoInventario, ['sueltosSerializados', 'sueltosCantidad'], true);
            @endphp

            <x-dialog-modal wire:model.live="modalListadoAbierto" maxWidth="xl">
                <x-slot name="title">
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="volverDashboard"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="w-10 h-10 rounded-lg bg-{{ $tipoColor }}-100 flex items-center justify-center shrink-0">
                            <i class="fas {{ $this->listadoInventarioIcono }} text-{{ $tipoColor }}-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-white">{{ $this->listadoInventarioTitulo }}</h3>
                            <p class="text-sm text-slate-300 mt-0.5">{{ $listaItems->count() }} item{{ $listaItems->count() !== 1 ? 's' : '' }}</p>
                        </div>
                        <button type="button" wire:click="volverDashboard" aria-label="Cerrar"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                <div class="max-h-[70vh] overflow-y-auto">
                    @php
                        // Piezas con "produce" (lote de fabricación): se agrupan en bloques
                        // "Produce XXXX-AAAA". Solo se agrupa si al menos un item de la lista
                        // trae el lote; si no, la lista queda plana como hasta ahora.
                        $bloquesLista = [['label' => null, 'items' => $listaItems]];
                        $algunConProduce = $this->filtroTipoInventario !== 'sueltosCantidad'
                            && $listaItems->contains(fn ($i) => !empty($i->atributos['produce'] ?? null));

                        if ($algunConProduce) {
                            $conProduce = $listaItems
                                ->filter(fn ($i) => !empty($i->atributos['produce'] ?? null))
                                ->groupBy(fn ($i) => (string) $i->atributos['produce'])
                                ->sortKeys();

                            $sinProduce = $listaItems
                                ->filter(fn ($i) => empty($i->atributos['produce'] ?? null));

                            $bloquesLista = $conProduce
                                ->map(fn ($items, $produce) => [
                                    'label' => 'Produce ' . $produce,
                                    'items' => $items->values(),
                                ])
                                ->values();

                            if ($sinProduce->isNotEmpty()) {
                                $bloquesLista->push(['label' => 'Sin produce', 'items' => $sinProduce->values()]);
                            }
                        }
                    @endphp

                    @if ($listaItems->isEmpty())
                        <div class="px-5 py-12 text-center">
                            <i class="fas {{ $this->listadoInventarioIcono }} text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-400 text-sm">Sin items para mostrar</p>
                        </div>
                    @else
                        @foreach ($bloquesLista as $bloque)
                            @if ($bloque['label'])
                                <div class="flex items-center gap-2 px-4 pt-3 pb-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-black uppercase tracking-wide {{ $bloque['label'] === 'Sin produce' ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-green-100 text-green-700 border border-green-200' }}">{{ $bloque['label'] }}</span>
                                    <span class="text-[11px] font-semibold text-gray-500 tabular-nums">{{ $bloque['items']->count() }} pieza{{ $bloque['items']->count() !== 1 ? 's' : '' }}</span>
                                    <div class="flex-1 h-px bg-gray-100"></div>
                                </div>
                            @endif
                            @foreach ($bloque['items'] as $item)
                                @php
                                    $estadoMeta = match($item->estado ?? null) {
                                        'en_stock'   => ['label' => 'Sellado',    'chip' => 'bg-green-100 text-green-700'],
                                        'abierto'    => ['label' => 'Abierto',    'chip' => 'bg-orange-100 text-orange-700'],
                                        'completado' => ['label' => 'Completado', 'chip' => 'bg-purple-100 text-purple-700'],
                                        'asignado'   => ['label' => 'Asignado',   'chip' => 'bg-yellow-100 text-yellow-700'],
                                        'consumido'  => ['label' => 'Consumido',  'chip' => 'bg-red-100 text-red-700'],
                                        default      => ['label' => $item->estado ?? '—', 'chip' => 'bg-gray-100 text-gray-600'],
                                    };
                                @endphp
                                @if ($esKits)
                                    <button type="button" wire:click="verDetalleKit({{ $item->id }})"
                                        class="w-full flex items-center gap-3 px-5 py-3.5 text-left border-b border-gray-100 transition-colors hover:bg-{{ $tipoColor }}-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <div class="w-10 h-10 rounded-lg bg-{{ $tipoColor }}-50 flex items-center justify-center shrink-0">
                                            <i class="fas {{ $this->listadoInventarioIcono }} text-{{ $tipoColor }}-500 text-sm"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="text-sm font-bold text-gray-800">{{ $item->producto?->nombre ?? 'Kit' }}</span>
                                                <span class="px-1.5 py-0.5 bg-gray-900 text-white text-[10px] font-black rounded tabular-nums">#{{ $item->id }}</span>
                                                <span class="px-2 py-0.5 {{ $estadoMeta['chip'] }} text-[10px] font-bold rounded-full">{{ $estadoMeta['label'] }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $item->sede?->nombre ?? '—' }}
                                                @if ($item->serie)
                                                    <span class="text-gray-300 mx-1">|</span>
                                                    <span class="font-mono">{{ $item->serie }}</span>
                                                @endif
                                                @if ($item->serviceOrder)
                                                    <span class="text-gray-300 mx-1">|</span>
                                                    <i class="fas fa-file-alt text-gray-400 mr-0.5"></i>Orden #{{ $item->service_order_id }}
                                                    @if ($item->serviceOrder->cliente)
                                                        · {{ $item->serviceOrder->cliente->nombre . ' ' . $item->serviceOrder->cliente->apellido }}
                                                    @endif
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-[10px] font-bold text-{{ $tipoColor }}-600 uppercase tracking-wide shrink-0">Ver kit</span>
                                        <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0"></i>
                                    </button>
                                @else
                                    <button type="button" wire:click="verDetallePieza({{ $item->id }})"
                                        class="w-full flex items-center gap-3 px-5 py-3 text-left border-b border-gray-100 transition-colors hover:bg-green-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-green-500">
                                        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                                            <i class="fas fa-barcode text-green-500 text-sm"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <p class="text-sm font-bold text-gray-800">{{ $item->producto?->nombre ?? 'Producto' }}</p>
                                                @if ($item->serie)
                                                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $item->serie }}</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->sede?->nombre ?? '—' }}</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-green-600 uppercase tracking-wide shrink-0">Ver detalle</span>
                                        <i class="fas fa-chevron-right text-gray-300 text-xs shrink-0"></i>
                                    </button>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                </div>
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="volverDashboard">
                        Cerrar
                    </x-secondary-button>
                </x-slot>
            </x-dialog-modal>