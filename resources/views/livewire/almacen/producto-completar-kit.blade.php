    {{-- ═══ A partir de aquí: modales — sin cambios de UI, tal como estaban ═══ --}}

    @php
        $faltantes = collect($completarKitComponentes)->filter(fn($c) => $c['faltan'] > 0);
        $seleccion = $completarKitSeleccion;
        // Stock disponible para componentes por cantidad (['stock' => N] | collect()).
        $stockDe = function ($c): int {
            $d = $c['disponibles'] ?? null;
            return is_array($d) ? (int) ($d['stock'] ?? 0) : 0;
        };
        // Serializados: requieren selección exacta de items.
        // Cantidad: no hay selección — alcanza con stock suelto >= faltan.
        $puedeCompletar = $faltantes->isEmpty() || $faltantes->every(function ($c) use ($seleccion, $stockDe) {
            if (!($c['es_serializado'] ?? false)) {
                return $stockDe($c) >= $c['faltan'];
            }
            $elegidos = collect($seleccion[$c['producto_id']] ?? [])->filter()->count();
            return $elegidos >= $c['faltan'];
        });
        $completos = collect($completarKitComponentes)->filter(fn($c) => $c['faltan'] <= 0);
        $totalFaltan = $faltantes->sum('faltan');
        $totalElegidos = $faltantes->sum(function ($c) use ($seleccion, $stockDe) {
            if (!($c['es_serializado'] ?? false)) {
                return min($stockDe($c), $c['faltan']);
            }
            return min(collect($seleccion[$c['producto_id']] ?? [])->filter()->count(), $c['faltan']);
        });
        $pctElegidos = $totalFaltan > 0 ? round($totalElegidos / $totalFaltan * 100) : 100;
    @endphp

    <x-dialog-modal wire:model.live="modalCompletarKitAbierto" maxWidth="xl">
                <x-slot name="title">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-puzzle-piece text-indigo-600"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-white">Completar kit</h3>
                            <p class="text-sm text-slate-300 truncate">{{ $completarKitNombre }}</p>
                        </div>
                        <button type="button" wire:click="cerrarCompletarKit" aria-label="Cerrar"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    @if ($totalFaltan > 0)
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-600">Piezas elegidas</span>
                                <span class="font-semibold tabular-nums {{ $puedeCompletar ? 'text-green-600' : 'text-gray-700' }}">
                                    {{ $totalElegidos }} de {{ $totalFaltan }}
                                </span>
                            </div>
                            <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $puedeCompletar ? 'bg-green-500' : 'bg-indigo-500' }}"
                                     style="width: {{ $pctElegidos }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="max-h-[65vh] overflow-y-auto space-y-4">
                    @error('general')
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $message }}
                        </div>
                    @enderror

                    @foreach ($faltantes as $comp)
                        @php
                            $idsElegidos = is_array($completarKitSeleccion[$comp['producto_id']] ?? null) ? $completarKitSeleccion[$comp['producto_id']] : [];
                            $esSerialComp = $comp['es_serializado'] ?? false;
                            $stockComp = is_array($comp['disponibles']) ? (int) ($comp['disponibles']['stock'] ?? 0) : 0;
                            if ($esSerialComp) {
                                $elegidosComp = collect($idsElegidos)->filter()->count();
                                $listo = $elegidosComp >= $comp['faltan'];
                            } else {
                                // Cantidad: "listo" según stock suelto, no hay selección de items.
                                $elegidosComp = min($stockComp, $comp['faltan']);
                                $listo = $stockComp >= $comp['faltan'];
                            }
                            $disponibles = $comp['disponibles'] instanceof \Illuminate\Support\Collection ? $comp['disponibles'] : collect($comp['disponibles']);
                        @endphp
                        <section class="rounded-xl border {{ $listo ? 'border-green-200' : 'border-amber-200' }} overflow-hidden">
                            <div class="flex items-center gap-2 px-4 py-2.5 {{ $listo ? 'bg-green-50' : 'bg-amber-50' }}">
                                <i class="fas {{ $listo ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-amber-600' }} text-sm"></i>
                                <span class="flex-1 min-w-0 truncate text-sm font-bold text-gray-800">{{ $comp['nombre'] }}</span>
                                <span class="shrink-0 px-2 py-0.5 rounded-full text-xs font-bold tabular-nums {{ $listo ? 'bg-green-200 text-green-800' : 'bg-amber-200 text-amber-800' }}">
                                    {{ $elegidosComp }}/{{ $comp['faltan'] }}
                                </span>
                            </div>
                            @if ($disponibles->isNotEmpty())
                                <div class="p-2.5 space-y-1.5 bg-white">
                                    @if ($esSerialComp)
                                        @foreach ($comp['disponibles'] as $item)
                                            @php
                                                $elegido = in_array($item['id'], $idsElegidos);
                                                $produce = $item['atributos']['produce'] ?? $item->atributos['produce'] ?? null;
                                                $fechaItem = $item['atributos']['fecha_recepcion'] ?? $item['atributos']['recepcion_fecha'] ?? $item['atributos']['fecha'] ?? '';
                                            @endphp
                                            <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors focus-within:ring-2 focus-within:ring-indigo-500 {{ $elegido ? 'bg-indigo-50 border-indigo-300' : 'bg-white border-gray-200 hover:border-gray-300' }}">
                                                <input type="checkbox"
                                                    wire:model.live="completarKitSeleccion.{{ $comp['producto_id'] }}"
                                                    value="{{ $item['id'] }}"
                                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="flex-1 min-w-0 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                                    <span class="font-mono text-sm font-bold text-gray-800">{{ $item['serie'] }}</span>
                                                    @if ($produce)
                                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded">{{ $produce }}</span>
                                                    @endif
                                                    @if ($fechaItem)
                                                        <span class="text-xs text-gray-400">{{ $fechaItem }}</span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    @else
                                        {{-- Cantidad: sin checkboxes — solo se muestra el stock suelto disponible. --}}
                                        <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-indigo-100 bg-indigo-50/50 text-sm">
                                            <i class="fas fa-cubes text-indigo-500"></i>
                                            <span class="text-slate-700">
                                                Disponible en almacén:
                                                <strong class="tabular-nums">{{ $stockComp }}</strong>
                                                — se tomarán <strong class="tabular-nums">{{ $comp['faltan'] }}</strong> al confirmar.
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="px-4 py-4 text-center text-sm text-red-600 font-semibold bg-white">
                                    <i class="fas fa-times-circle mr-1"></i>{{ $esSerialComp ? 'Sin items disponibles en almacén' : 'Sin stock suelto disponible' }}
                                </div>
                            @endif
                        </section>
                    @endforeach

                    @if ($completos->isNotEmpty())
                        <div class="pt-1">
                            <p class="text-xs text-gray-500 mb-1.5">Ya completos</p>
                            <ul class="flex flex-wrap gap-1.5">
                                @foreach ($completos as $comp)
                                    <li class="inline-flex items-center gap-1.5 rounded-md bg-green-50 border border-green-200 px-2 py-1 text-xs">
                                        <i class="fas fa-check text-green-500"></i>
                                        <span class="font-medium text-gray-700">{{ $comp['nombre'] }}</span>
                                        <span class="text-green-700 font-semibold tabular-nums">{{ $comp['cantidad_esperada'] }}/{{ $comp['cantidad_esperada'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                </x-slot>

                <x-slot name="footer">
                    <div class="flex w-full items-center gap-3">
                    <button type="button" wire:click="cerrarCompletarKit"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        Cancelar
                    </button>
                    <div class="ml-auto flex items-center gap-3">
                        @if ($puedeCompletar)
                            <button type="button" wire:click="completarKit" wire:loading.attr="disabled"
                                class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                <span wire:loading.remove wire:target="completarKit"><i class="fas fa-check mr-1"></i> Completar kit</span>
                                <span wire:loading wire:target="completarKit">Guardando...</span>
                            </button>
                        @else
                            <span class="text-xs text-amber-700 font-medium">Elegí {{ $totalFaltan - $totalElegidos }} más</span>
                            <button type="button" disabled
                                class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg opacity-40 cursor-not-allowed">
                                <i class="fas fa-check mr-1"></i> Completar kit
                            </button>
                        @endif
                    </div>
                </div>
                </x-slot>
        </x-dialog-modal>