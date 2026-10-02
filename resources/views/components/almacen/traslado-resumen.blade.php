    @php
        $seleccionados = \App\Models\ItemSerializado::with(['producto.categoria'])
            ->whereIn('id', array_keys($this->itemsSeleccionados))
            ->get();

        $kits = $seleccionados
            ->filter(fn ($i) => is_null($i->kit_padre_id) && $i->producto?->categoria?->es_kit)
            ->values();
        $piezas = $seleccionados
            ->filter(fn ($i) => is_null($i->kit_padre_id) && ! $i->producto?->categoria?->es_kit)
            ->values();

        // Completitud de cada kit seleccionado (1 consulta por grupo, no por fila).
        $recetasPorProducto = $kits->isEmpty()
            ? collect()
            : \App\Models\KitComponente::whereIn('producto_kit_id', $kits->pluck('producto_id'))
                ->get()
                ->groupBy('producto_kit_id');

        $hijosPorKit = $kits->isEmpty()
            ? collect()
            : \App\Models\ItemSerializado::whereIn('kit_padre_id', $kits->pluck('id'))
                ->where('estado', 'en_stock')
                ->get(['id', 'kit_padre_id'])
                ->countBy('kit_padre_id');

        $completitud = $kits->mapWithKeys(function ($kit) use ($recetasPorProducto, $hijosPorKit) {
            $esperadas = (int) ($recetasPorProducto->get($kit->producto_id)?->sum('cantidad_esperada') ?? 0);
            $presentes = (int) ($hijosPorKit->get($kit->id) ?? 0);

            return [$kit->id => [
                'esperadas' => $esperadas,
                'presentes' => $presentes,
                'completo' => $esperadas > 0 && $presentes >= $esperadas,
            ]];
        });
    @endphp

    <section class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="w-7 h-7 {{ !$this->resumenVacio ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }} text-xs font-bold rounded-full flex items-center justify-center shrink-0 transition-colors">
                @if (!$this->resumenVacio)
                    <i class="fas fa-check text-[11px]"></i>
                @else
                    3
                @endif
            </span>
            <h2 class="font-semibold text-gray-800 text-sm sm:text-base min-w-0">Resumen del envío</h2>

            <span class="sm:ml-auto max-w-full text-xs font-bold px-2.5 py-1 rounded-full break-words
                {{ !$this->resumenVacio ? 'text-indigo-600 bg-indigo-50 border border-indigo-100' : 'text-gray-500 bg-gray-100 border border-gray-200' }}">
                {{ $this->seleccionCount }} {{ \Illuminate\Support\Str::plural('item', $this->seleccionCount) }}
            </span>
        </div>

        @if ($this->resumenVacio)
            <div class="text-center py-10 text-gray-400 border border-dashed border-gray-200 rounded-xl">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-list-check text-gray-300 text-xl"></i>
                </div>
                <p class="text-sm font-medium text-gray-500">Todavía no elegiste nada</p>
                <p class="text-xs mt-1">Elegí la sede destino y seleccioná kits o piezas: van a aparecer acá</p>
            </div>
        @else
            <ul class="space-y-1">
                @foreach ($kits as $kit)
                    @php $c = $completitud[$kit->id] ?? null; @endphp
                    <li class="flex items-center gap-2 text-sm py-2 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                        <i class="fas fa-box text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 font-medium truncate">{{ $kit->producto->nombre }}</span>

                        @if ($c && $c['esperadas'] > 0)
                            <span class="shrink-0 px-1.5 py-0.5 rounded text-[10px] font-bold
                                {{ $c['completo'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $c['completo'] ? 'Completo' : $c['presentes'] . '/' . $c['esperadas'] }}
                            </span>
                        @endif

                        <span class="text-xs text-gray-400 shrink-0 ml-auto">#{{ $kit->id }}</span>
                    </li>
                @endforeach

                @foreach ($piezas as $pieza)
                    <li class="flex items-center gap-2 text-sm py-2 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                        <i class="fas fa-puzzle-piece text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 truncate">{{ $pieza->producto->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">{{ $pieza->serie ?? 's/serie' }}</span>
                    </li>
                @endforeach

                @foreach ($this->piezasCantidadCarrito as $p)
                    <li class="flex items-center gap-2 text-sm py-2 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                        <i class="fas fa-cubes text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 truncate">{{ $p->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">×{{ $p->cantidad_solicitada }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4 pt-3 border-t border-gray-200 flex flex-wrap items-center justify-between gap-2 text-sm">
                <span class="flex items-center gap-3 text-[11px] text-gray-400">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400"></span>Kit</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-400"></span>Serializado</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Por cantidad</span>
                </span>
                <span class="text-gray-500">Total de items</span>
                <span class="font-bold text-gray-900 text-base tabular-nums">{{ $this->seleccionCount }}</span>
            </div>
        @endif
    </section>
