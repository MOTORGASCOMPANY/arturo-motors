            @if ($this->tabSeleccion === 'kits')
                @php $kits = $this->kitsDisponibles; @endphp

                @if ($kits->isEmpty())
                    <x-ui.empty-state icon="fa-box-open" message="No hay kits disponibles en esta sede" />
                @else
                    <p class="text-[11px] text-gray-400 mb-3 flex items-start gap-1.5">
                        <i class="fas fa-circle-info text-indigo-300 mt-0.5 shrink-0"></i>
                        <span>Los kits abiertos piden confirmación antes de agregarse, porque van a viajar con piezas faltantes.</span>
                    </p>

                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1 -mr-1">

                        @foreach ($kits as $kit)
                            @php
                                $isSelected = isset($this->itemsSeleccionados[$kit->id]);
                                $isInspecting = $this->kitInspeccionId === $kit->id;
                                $completo = $kit->es_sellado;
                            @endphp

                            <div class="border-2 rounded-xl transition-all duration-150
                                {{ $isSelected ? 'border-indigo-500 bg-indigo-50/60 ring-1 ring-indigo-100' : ($completo ? 'border-gray-200 hover:border-gray-300' : 'border-amber-200 bg-amber-50/40 hover:border-amber-300') }}">

                                <label class="flex items-center gap-3 p-3 cursor-pointer">
                                    <input type="checkbox"
                                        wire:click="toggleKit({{ $kit->id }})"
                                        @checked($isSelected)
                                        class="w-4.5 h-4.5 sm:w-4 sm:h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0 cursor-pointer">

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <p class="text-sm font-bold text-gray-800 truncate">
                                                {{ $kit->producto->nombre }}
                                            </p>
                                            @if ($completo)
                                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full shrink-0">
                                                    <i class="fas fa-lock mr-0.5"></i> Sellado
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full shrink-0">
                                                    <i class="fas fa-unlock mr-0.5"></i> Abierto · {{ $kit->hijosCount }}/{{ $kit->totalEsperado }}
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-[11px] text-gray-400 mt-0.5 flex flex-wrap items-center gap-x-1.5">
                                            <span>#{{ $kit->id }}</span>
                                            <span class="text-gray-300">·</span>
                                            <span>{{ $kit->totalEsperado }} piezas esperadas</span>
                                            @unless ($completo)
                                                <span class="text-gray-300">·</span>
                                                <span class="font-semibold text-amber-700">{{ $kit->totalEsperado - $kit->hijosCount }} faltantes</span>
                                            @endunless
                                            <span class="text-gray-300">·</span>
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-location-dot text-[9px]"></i>{{ $kit->sede?->nombre ?? '—' }}</span>
                                        </p>
                                    </div>

                                    <button
                                        wire:click.stop="toggleInspeccion({{ $kit->id }})"
                                        type="button"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg {{ $isSelected ? 'text-indigo-500 hover:bg-indigo-100' : ($completo ? 'text-gray-400 hover:text-gray-700 hover:bg-gray-100' : 'text-amber-500 hover:bg-amber-100') }} transition shrink-0"
                                        title="Ver componentes">
                                        <i class="fas {{ $isInspecting ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs transition-transform"></i>
                                    </button>
                                </label>

                                @if ($isInspecting)
                                    @php $insp = $this->kitInspeccion; @endphp

                                    @if ($insp && $insp['kit']->id === $kit->id)
                                        <div class="px-3 pb-3 pt-2 border-t border-gray-100 bg-white/60 rounded-b-xl">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                                    Componentes
                                                </p>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $insp['hijosSeleccionadosCount'] >= $insp['totalEsperado'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ $insp['hijosSeleccionadosCount'] }}/{{ $insp['totalEsperado'] }} para envío
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                @foreach ($insp['receta'] as $r)
                                                    @php
                                                        $presente = $insp['hijos'][$r->producto_componente_id] ?? 0;
                                                        $completoComp = $presente >= $r->cantidad_esperada;
                                                        $hijosComponente = $insp['hijosItems']->filter(fn ($h) => $h->producto_id === $r->producto_componente_id);
                                                    @endphp
                                                    <div class="p-2 rounded-lg border
                                                        {{ $completoComp ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                                        <div class="flex items-center gap-2 text-[11px]">
                                                            <i class="fas {{ $completoComp ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500' }} text-[10px] shrink-0"></i>
                                                            <span class="font-semibold text-gray-700 truncate">{{ $r->componente?->nombre ?? '—' }}</span>
                                                            <span class="text-gray-500 ml-auto shrink-0 font-medium">
                                                                {{ $presente }}/{{ $r->cantidad_esperada }}
                                                            </span>
                                                        </div>
                                                        @if ($hijosComponente->count() > 0)
                                                            <div class="mt-1.5 ml-5 space-y-1 border-l-2 border-gray-200 pl-2">
                                                                @foreach ($hijosComponente as $hijo)
                                                                    @php $isChildSelected = isset($this->itemsSeleccionados[$hijo->id]); @endphp
                                                                    <label class="flex items-center gap-1.5 cursor-pointer text-[10px] py-0.5 {{ $isChildSelected ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                                                                        <input type="checkbox"
                                                                            wire:click="toggleHijoKit({{ $hijo->id }})"
                                                                            @checked($isChildSelected)
                                                                            class="w-3.5 h-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                                                        <span class="font-mono truncate">{{ $hijo->serie ?? '(sin serie)' }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                            </div>
                        @endforeach

                    </div>
                @endif
            @endif
