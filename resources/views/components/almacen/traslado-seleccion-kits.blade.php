            @if ($this->tabSeleccion === 'kits')
                @php $kits = $this->kitsDisponibles; @endphp

                @if ($kits->isEmpty())
                    <div class="text-center py-14 text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">No hay kits disponibles en esta sede</p>
                        <p class="text-xs mt-1">Prueba con otro filtro de búsqueda o revisa la pestaña de piezas sueltas</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1 -mr-1">

                        @foreach ($kits as $kit)
                            @php
                                $isSelected = isset($this->itemsSeleccionados[$kit->id]);
                                $isInspecting = $this->kitInspeccionId === $kit->id;
                            @endphp

                            <div class="border-2 rounded-xl transition-all duration-150
                                {{ $isSelected ? 'border-indigo-500 bg-indigo-50/60 ring-1 ring-indigo-100' : 'border-gray-200 hover:border-gray-300' }}">

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
                                            @if ($kit->es_sellado)
                                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full shrink-0">
                                                    Sellado
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full shrink-0">
                                                    Abierto
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-[11px] text-gray-400 mt-0.5 flex flex-wrap items-center gap-x-1.5">
                                            <span>#{{ $kit->id }}</span>
                                            <span class="text-gray-300">·</span>
                                            <span>{{ $kit->totalEsperado }} piezas esperadas</span>
                                            @if (! $kit->es_sellado)
                                                <span class="text-gray-300">·</span>
                                                <span>{{ $kit->hijosCount }} presentes</span>
                                            @endif
                                            <span class="text-gray-300">·</span>
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-location-dot text-[9px]"></i>{{ $kit->sede?->nombre ?? '—' }}</span>
                                        </p>
                                    </div>

                                    <button
                                        wire:click.stop="toggleInspeccion({{ $kit->id }})"
                                        type="button"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0"
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
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $insp['hijosSeleccionadosCount'] >= $insp['totalEsperado'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                    {{ $insp['hijosSeleccionadosCount'] }}/{{ $insp['totalEsperado'] }} para envío
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                                @foreach ($insp['receta'] as $r)
                                                    @php
                                                        $presente = $insp['hijos'][$r->producto_componente_id] ?? 0;
                                                        $completo = $presente >= $r->cantidad_esperada;
                                                        $hijosComponente = $insp['hijosItems']->filter(fn($h) => $h->producto_id === $r->producto_componente_id);
                                                    @endphp
                                                    <div class="p-2 rounded-lg border
                                                        {{ $completo ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                                                        <div class="flex items-center gap-2 text-[11px]">
                                                            <i class="fas {{ $completo ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500' }} text-[10px] shrink-0"></i>
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
