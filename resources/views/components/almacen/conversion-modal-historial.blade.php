                    <div class="flex flex-col shrink-0 bg-gray-50/50 w-full lg:w-72 max-h-[45vh] lg:max-h-none border-t lg:border-t-0 border-gray-100 overflow-hidden">

                        <div class="px-4 pt-3 sm:pt-4 pb-2 flex items-center gap-2 shrink-0">
                            <i class="fas fa-check-double text-green-500 text-sm"></i>
                            <h3 class="text-sm font-bold text-gray-900">Despachados</h3>
                            @if($this->historial->isNotEmpty())
                                <span class="ml-auto px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded">{{ $this->historial->count() }}</span>
                            @endif
                        </div>
                        <div class="overflow-y-auto px-3 pb-3 max-h-40 lg:max-h-none lg:flex-1">
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

                        <div class="border-t border-gray-200/60 shrink-0 flex flex-col min-h-0 overflow-hidden">
                            <div class="px-4 pt-3 pb-2 flex items-center gap-2 shrink-0">
                                <i class="fas fa-history text-purple-500 text-sm"></i>
                                <h3 class="text-sm font-bold text-gray-900">Línea de tiempo</h3>
                            </div>
                            <div class="overflow-y-auto px-3 pb-3 max-h-40">
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
