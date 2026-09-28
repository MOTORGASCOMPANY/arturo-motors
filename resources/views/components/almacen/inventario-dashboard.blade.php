            {{-- ═══ NIVEL 0: DASHBOARD ═══ --}}
            @if ($this->nivelInventario === 'dashboard')
                @php
                    $inv  = $this->resumenInventario ?? [];
                    $c    = $inv['conteos'] ?? [];
                    $sellados     = $inv['kitsSellados']       ?? collect();
                    $incompletos  = $inv['kitsIncompletos']    ?? collect();
                    $completados  = $inv['kitsCompletados']    ?? collect();
                    $consumidos   = $inv['kitsConsumidos']     ?? collect();
                    $sueltosS     = $inv['sueltosSerializados']?? collect();
                    $sueltosC     = $inv['sueltosCantidad']    ?? collect();
                    $sinStock     = $inv['sinStock']           ?? collect();

                    $stats = [
                        ['tipo' => 'sellado',              'label' => 'Sellados',             'hint' => 'En stock',         'valor' => $c['sellados'] ?? 0,             'icono' => 'fa-box',          'color' => 'text-amber-600',  'icono_color' => 'text-amber-500',  'bg' => 'bg-amber-50',  'hover' => 'hover:bg-amber-50'],
                        ['tipo' => 'incompleto',           'label' => 'Incompletos',          'hint' => 'Abiertos',         'valor' => $c['incompletos'] ?? 0,          'icono' => 'fa-box-open',     'color' => 'text-orange-600', 'icono_color' => 'text-orange-500', 'bg' => 'bg-orange-50', 'hover' => 'hover:bg-orange-50'],
                        ['tipo' => 'completado',           'label' => 'Completados',          'hint' => 'Kits completados', 'valor' => $c['completados'] ?? 0,          'icono' => 'fa-check-circle', 'color' => 'text-purple-600', 'icono_color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'hover' => 'hover:bg-purple-50'],
                        ['tipo' => 'consumido',            'label' => 'Consumidos',           'hint' => 'En órdenes',       'valor' => $c['consumidos'] ?? 0,           'icono' => 'fa-fire',         'color' => 'text-red-600',    'icono_color' => 'text-red-500',    'bg' => 'bg-red-50',    'hover' => 'hover:bg-red-50'],
                        ['tipo' => 'sueltosSerializados',  'label' => 'Sueltos con serie',    'hint' => 'Tipos de producto','valor' => $c['sueltosSerializados'] ?? 0,  'icono' => 'fa-barcode',      'color' => 'text-green-600',  'icono_color' => 'text-green-500',  'bg' => 'bg-green-50',  'hover' => 'hover:bg-green-50'],
                        ['tipo' => 'sueltosCantidad',      'label' => 'Sueltos por cantidad', 'hint' => 'Tipos de producto','valor' => $c['sueltosCantidadTipos'] ?? 0, 'icono' => 'fa-cubes',        'color' => 'text-indigo-600', 'icono_color' => 'text-indigo-500', 'bg' => 'bg-indigo-50', 'hover' => 'hover:bg-indigo-50'],
                    ];
                @endphp

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-px bg-gray-200 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                        @foreach ($stats as $st)
                            @if ($st['tipo'] === 'sueltosCantidad')
                                {{-- Modal de items por cantidad eliminado: tarjeta solo informativa --}}
                                <div class="bg-white p-3 text-left">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-lg {{ $st['bg'] }} flex items-center justify-center shrink-0">
                                            <i class="fas {{ $st['icono'] }} {{ $st['icono_color'] }} text-xs"></i>
                                        </span>
                                        <span class="text-xs font-medium text-gray-600">{{ $st['label'] }}</span>
                                    </div>
                                    <p class="text-2xl font-black {{ $st['color'] }} mt-2 leading-none tabular-nums">{{ $st['valor'] }}</p>
                                    <span class="text-[11px] text-gray-400 mt-1 block">{{ $st['hint'] }}</span>
                                </div>
                            @else
                                <button type="button" wire:click="verListadoInventario('{{ $st['tipo'] }}')"
                                    class="bg-white p-3 text-left transition-colors duration-150 {{ $st['hover'] }} focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-lg {{ $st['bg'] }} flex items-center justify-center shrink-0">
                                            <i class="fas {{ $st['icono'] }} {{ $st['icono_color'] }} text-xs"></i>
                                        </span>
                                        <span class="text-xs font-medium text-gray-600">{{ $st['label'] }}</span>
                                    </div>
                                    <p class="text-2xl font-black {{ $st['color'] }} mt-2 leading-none tabular-nums">{{ $st['valor'] }}</p>
                                    <span class="text-[11px] text-gray-400 mt-1 block">{{ $st['hint'] }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>

                @php
                    // Helper: series vigentes agrupadas por componente (para tarjetas compactas).
                    $buildSeriesTxt = function ($kitItem): string {
                        $seriesList = $kitItem->piezasEnKit
                            ->filter(fn ($p) => (bool) ($p->producto?->categoria?->es_serializado))
                            ->sortBy('id')
                            ->values();
                        $vigentes = $seriesList->reject(
                            fn ($p) => in_array($p->estado, ['defectuoso', 'devuelta_por_no_calzar'], true)
                        );
                        return $vigentes
                            ->groupBy(fn ($p) => $p->producto?->nombre ?? '—')
                            ->sortKeys()
                            ->map(function ($grupo) {
                                $series = $grupo->pluck('serie')->filter()->values();
                                if ($series->isEmpty()) {
                                    return $grupo->first()->producto?->nombre . ' sin serie';
                                }
                                $nombre = $grupo->first()->producto?->nombre ?? '—';
                                $txt = $series->take(1)->implode(', ');
                                if ($series->count() > 1) {
                                    $txt .= ' +' . ($series->count() - 1);
                                }
                                return $nombre . ' ' . $txt;
                            })
                            ->implode(' · ');
                    };

                    // Helper: generación (3RA / 5TA) desde atributos o nombre del producto.
                    $buildGeneracion = function ($kitItem): string {
                        $gen = $kitItem->producto->atributos['generacion'] ?? '';
                        if (!$gen && preg_match('/(3RA|5TA)/i', $kitItem->producto->nombre ?? '', $m)) {
                            $gen = strtoupper($m[1]);
                        }
                        return $gen;
                    };

                    // Incompletos: filas con acción Completar (sección aparte, ancho completo).
                    $incompletosItems = collect();
                    foreach ($incompletos as $productoId => $items) {
                        foreach ($items as $kitItem) {
                            $incompletosItems->push($kitItem);
                        }
                    }

                    // 3 grupos de estado para layout en fila.
                    $kitGroups = [
                        'sellado' => [
                            'label' => 'Sellados', 'tipo' => 'sellado', 'color' => 'amber',
                            'icon' => 'fa-box', 'iconColor' => 'text-amber-500',
                            'chip' => 'Sellado', 'chipClass' => 'bg-amber-100 text-amber-700',
                            'items' => $sellados->flatten(),
                        ],
                        'completado' => [
                            'label' => 'Completados', 'tipo' => 'completado', 'color' => 'purple',
                            'icon' => 'fa-check-circle', 'iconColor' => 'text-purple-500',
                            'chip' => 'Completado', 'chipClass' => 'bg-purple-100 text-purple-700',
                            'items' => $completados->flatten(),
                        ],
                        'consumido' => [
                            'label' => 'Consumidos', 'tipo' => 'consumido', 'color' => 'red',
                            'icon' => 'fa-fire', 'iconColor' => 'text-red-500',
                            'chip' => 'Consumido', 'chipClass' => 'bg-red-100 text-red-700',
                            'items' => $consumidos->flatten(),
                        ],
                    ];

                    // Piezas sueltas (solo con serie; cantidad ya no abre modal).
                    $sueltosCards = collect();
                    foreach ($sueltosS as $productoId => $items) {
                        $prod = $items->first()?->producto;
                        foreach ($items as $item) {
                            $sueltosCards->push([
                                'nombre'     => $prod?->nombre ?? 'Producto',
                                'sede'       => $item->sede?->nombre ?? '—',
                                'badge'      => $item->serie ?? '—',
                                'badgeClass' => 'bg-gray-100 text-gray-700 font-mono',
                                'tipo'       => 'sueltosSerializados',
                                'icono'      => 'fa-barcode',
                                'produce'    => $item->atributos['produce'] ?? null,
                            ]);
                        }
                    }
                    foreach ($sueltosC as $stock) {
                        $sueltosCards->push([
                            'nombre'     => $stock->producto?->nombre ?? 'Producto',
                            'sede'       => $stock->sede?->nombre ?? '—',
                            'badge'      => '×' . ($stock->cantidad_suelta_real ?? $stock->cantidad),
                            'badgeClass' => 'bg-indigo-100 text-indigo-700',
                            'tipo'       => 'sueltosCantidad',
                            'icono'      => 'fa-cubes',
                            'produce'    => null,
                        ]);
                    }
                @endphp

                {{-- Incompletos: sección propia con acción Completar --}}
                @if ($incompletosItems->isNotEmpty())
                    <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <x-almacen.section-header icon="fa-box-open" color="orange" title="Incompletos" :count="$incompletosItems->count()" />
                        <ul class="divide-y divide-gray-100">
                            @foreach ($incompletosItems as $kitItem)
                                <li class="flex items-center gap-3 px-4 py-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-box-open text-orange-500 text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="text-sm font-bold text-gray-800 truncate">{{ $kitItem->producto?->nombre ?? 'Kit' }}</span>
                                            <span class="px-1.5 py-0.5 bg-gray-900 text-white text-[10px] font-black rounded tabular-nums">#{{ $kitItem->id }}</span>
                                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-full">Incompleto</span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">
                                            {{ $kitItem->sede?->nombre ?? '—' }}
                                            @if ($kitItem->serviceOrder)
                                                <span class="text-gray-300 mx-1">|</span>
                                                Ord #{{ $kitItem->service_order_id }}
                                            @endif
                                        </p>
                                    </div>
                                    <button type="button" wire:click="abrirCompletarKit({{ $kitItem->id }})"
                                        class="shrink-0 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-1">
                                        Completar
                                    </button>
                                    <button type="button" wire:click="verDetalleKit({{ $kitItem->id }})"
                                        class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded">
                                        Ver detalle
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Kits: 3 grupos en fila (Sellados | Completados | Consumidos) --}}
                @if ($kitGroups['sellado']['items']->isNotEmpty() || $kitGroups['completado']['items']->isNotEmpty() || $kitGroups['consumido']['items']->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach ($kitGroups as $gKey => $g)
                            @if ($g['items']->isNotEmpty())
                                <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                                    <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100">
                                        <i class="fas {{ $g['icon'] }} {{ $g['iconColor'] }}"></i>
                                        <h3 class="text-sm font-bold text-gray-800">{{ $g['label'] }}</h3>
                                        <span class="ml-auto px-2 py-0.5 bg-{{ $g['color'] }}-100 text-{{ $g['color'] }}-700 text-xs font-bold rounded-full tabular-nums">{{ $g['items']->count() }}</span>
                                    </div>
                                    <div class="p-3 grid grid-cols-1 gap-2 flex-1">
                                        @foreach ($g['items']->take(6) as $kitItem)
                                            @php
                                                $gen = $buildGeneracion($kitItem);
                                                $seriesTxt = $buildSeriesTxt($kitItem);
                                            @endphp
                                            <button type="button" wire:click="verDetalleKit({{ $kitItem->id }})"
                                                class="w-full text-left bg-gray-50/80 border border-gray-100 rounded-lg px-3 py-2 hover:border-indigo-300 hover:bg-indigo-50/40 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                                <div class="flex items-center justify-between gap-2 mb-1">
                                                    <div class="flex items-center gap-1.5 min-w-0">
                                                        <span class="text-xs font-black text-gray-800 tabular-nums">#{{ $kitItem->id }}</span>
                                                        @if ($gen)
                                                            <span class="px-1.5 py-0.5 bg-white border border-gray-200 text-gray-600 text-[10px] font-bold rounded uppercase">{{ $gen }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="px-1.5 py-0.5 {{ $g['chipClass'] }} text-[10px] font-bold rounded-full shrink-0">{{ $g['chip'] }}</span>
                                                </div>
                                                <p class="text-[11px] text-gray-500 truncate">
                                                    {{ $kitItem->sede?->nombre ?? '—' }}
                                                    @if ($kitItem->serviceOrder)
                                                        <span class="text-gray-300 mx-0.5">·</span>
                                                        Ord #{{ $kitItem->service_order_id }}
                                                    @endif
                                                </p>
                                                @if ($seriesTxt)
                                                    <p class="text-[10px] font-mono text-gray-400 truncate mt-0.5" title="{{ $seriesTxt }}">
                                                        {{ $seriesTxt }}
                                                    </p>
                                                @endif
                                            </button>
                                        @endforeach
                                        @if ($g['items']->count() > 6)
                                            <button type="button" wire:click="verListadoInventario('{{ $g['tipo'] }}')"
                                                class="mt-1 w-full text-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline py-1.5 rounded-lg hover:bg-indigo-50/50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                                Ver todos (+{{ $g['items']->count() - 6 }})
                                            </button>
                                        @endif
                                    </div>
                                </section>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if ($sueltosCards->isNotEmpty())
                    <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <x-almacen.section-header icon="fa-barcode" color="green" title="Piezas sueltas" :count="$sueltosCards->count()" />
                        @php $labelsSueltos = ['sueltosSerializados' => 'Con serie', 'sueltosCantidad' => 'Por cantidad']; @endphp
                        @foreach ($sueltosCards->groupBy('tipo') as $tipo => $grupo)
                            <div class="px-3 {{ $loop->first ? 'pt-2' : 'pt-3' }} pb-3 border-t border-gray-100 {{ $loop->first ? 'border-t-0' : '' }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">{{ $labelsSueltos[$tipo] ?? $tipo }}</span>
                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full tabular-nums">{{ $grupo->count() }}</span>
                                    <div class="flex-1 h-px bg-gray-100"></div>
                                </div>
                                @php
                                    $porProducto = $grupo->groupBy('nombre')->sortKeys();

                                    // Bloques por "produce" (lote de fabricación) dentro de
                                    // cada tarjeta de producto. Solo se agrupa si la tarjeta
                                    // tiene algún item con produce; si no, los chips quedan
                                    // sueltos como hasta ahora.
                                    $bloquesPorProducto = $porProducto->map(function ($piezasProd) {
                                        $conProduce = $piezasProd
                                            ->filter(fn ($sc) => !empty($sc['produce']))
                                            ->groupBy(fn ($sc) => (string) $sc['produce'])
                                            ->sortKeys();

                                        if ($conProduce->isEmpty()) {
                                            return collect([['label' => null, 'items' => $piezasProd]]);
                                        }

                                        $bloques = $conProduce->map(fn ($items, $produce) => [
                                            'label' => 'Produce ' . $produce,
                                            'items' => $items,
                                        ])->values();

                                        $sinProduce = $piezasProd->filter(fn ($sc) => empty($sc['produce']));
                                        if ($sinProduce->isNotEmpty()) {
                                            $bloques->push(['label' => 'Sin produce', 'items' => $sinProduce]);
                                        }

                                        return $bloques;
                                    });
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2">
                                    @foreach ($porProducto as $nombreProd => $piezasProd)
                                        @if ($tipo === 'sueltosSerializados')
                                            {{-- Clic en la tarjeta → listado completo de piezas con serie --}}
                                            <div wire:click="verListadoInventario('sueltosSerializados')" role="button" tabindex="0"
                                                class="bg-gray-50/80 border border-gray-100 rounded-lg px-3 py-2 cursor-pointer hover:border-green-300 hover:bg-green-50/60 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-green-500">
                                        @else
                                            <div class="bg-gray-50/80 border border-gray-100 rounded-lg px-3 py-2">
                                        @endif
                                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="w-6 h-6 rounded-md bg-white border border-gray-200 flex items-center justify-center shrink-0">
                                                        <i class="fas {{ $piezasProd->first()['icono'] ?? 'fa-cubes' }} text-green-500 text-[10px]"></i>
                                                    </span>
                                                    <span class="text-xs font-bold text-gray-800 truncate">{{ $nombreProd }}</span>
                                                </div>
                                                <span class="px-1.5 py-0.5 bg-white border border-gray-200 text-gray-500 text-[10px] font-bold rounded-full tabular-nums shrink-0">
                                                    {{ $piezasProd->count() }}
                                                </span>
                                            </div>
                                            @php $bloques = $bloquesPorProducto[$nombreProd] ?? collect(); @endphp
                                            @foreach ($bloques as $bloque)
                                                @if ($bloque['label'])
                                                    <div class="flex items-center gap-1.5 w-full mt-1">
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wide {{ $bloque['label'] === 'Sin produce' ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-green-100 text-green-700 border border-green-200' }}">{{ $bloque['label'] }}</span>
                                                        <span class="text-[10px] font-semibold text-gray-400 tabular-nums">{{ $bloque['items']->count() }}</span>
                                                        <div class="flex-1 h-px bg-gray-100"></div>
                                                    </div>
                                                @endif
                                                <div class="flex flex-wrap gap-1 w-full">
                                                    @foreach ($bloque['items'] as $sc)
                                                        @if ($sc['tipo'] === 'sueltosCantidad')
                                                            {{-- Modal de cantidad eliminado: solo informativo --}}
                                                            <span class="inline-flex items-center gap-1 text-[11px] bg-white border border-gray-200 rounded px-1.5 py-0.5"
                                                                title="{{ $sc['sede'] }}">
                                                                <span class="text-gray-400 truncate max-w-[4.5rem]">{{ $sc['sede'] }}</span>
                                                                <span class="font-semibold text-indigo-700">{{ $sc['badge'] }}</span>
                                                            </span>
                                                        @else
                                                            {{-- Chips informativos: el clic lo maneja la tarjeta --}}
                                                            <span class="inline-flex items-center gap-1 text-[11px] bg-white border border-gray-200 rounded px-1.5 py-0.5"
                                                                title="{{ $sc['sede'] }}">
                                                                <span class="text-gray-400 truncate max-w-[4.5rem]">{{ $sc['sede'] }}</span>
                                                                <span class="font-semibold font-mono text-gray-700">{{ $sc['badge'] }}</span>
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif

                {{-- Productos sin stock en la sede filtrada (alerta roja) --}}
                @if ($sinStock->isNotEmpty())
                    <section class="bg-white rounded-xl border border-red-200 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-3 border-b border-red-100 bg-red-50">
                            <i class="fas fa-triangle-exclamation text-red-500"></i>
                            <h3 class="text-sm font-bold text-red-700">Sin stock</h3>
                            <span class="ml-auto px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full tabular-nums">{{ $sinStock->count() }}</span>
                        </div>
                        <ul class="divide-y divide-red-50">
                            @foreach ($sinStock as $sp)
                                <li class="flex items-center gap-3 px-4 py-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-box-open text-red-500 text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="text-sm font-bold text-red-600 truncate">{{ $sp->nombre }}</span>
                                            @if ($sp->categoria?->es_kit)
                                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full">Kit</span>
                                            @endif
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">Sin stock</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $sp->categoria?->nombre ?? '—' }}@if ($sp->marca) <span class="text-gray-300">|</span> {{ $sp->marca }}@endif</p>
                                    </div>
                                    <span class="text-2xl font-bold text-red-500 tabular-nums shrink-0">0</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($sellados->isEmpty() && $incompletos->isEmpty() && $completados->isEmpty() && $consumidos->isEmpty() && $sueltosS->isEmpty() && $sueltosC->isEmpty() && $sinStock->isEmpty())
                    <x-almacen.empty-state icon="fa-boxes-stacked" message="No hay inventario para mostrar" />
                @endif
            @endif