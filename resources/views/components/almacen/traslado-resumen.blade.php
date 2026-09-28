    @unless ($this->resumenVacio)
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 sm:p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-list-check"></i> Resumen del envío
            </h3>

            <div class="space-y-1">
                @php
                    $kitsSeleccionados = collect(array_keys($this->itemsSeleccionados))
                        ->map(fn ($id) => \App\Models\ItemSerializado::with('producto')->find($id))
                        ->filter(fn ($i) => $i && is_null($i->kit_padre_id) && $i->producto?->categoria?->es_kit);
                @endphp

                @foreach ($kitsSeleccionados as $kit)
                    <div class="flex items-center gap-2 text-sm py-1.5 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                        <i class="fas fa-box text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 font-medium truncate">{{ $kit->producto->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">#{{ $kit->id }}</span>
                    </div>
                @endforeach

                @php
                    $sueltosSeleccionados = collect(array_keys($this->itemsSeleccionados))
                        ->map(fn ($id) => \App\Models\ItemSerializado::with('producto')->find($id))
                        ->filter(fn ($i) => $i && is_null($i->kit_padre_id) && !$i->producto?->categoria?->es_kit);
                @endphp

                @foreach ($sueltosSeleccionados as $pieza)
                    <div class="flex items-center gap-2 text-sm py-1.5 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                        <i class="fas fa-puzzle-piece text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 truncate">{{ $pieza->producto->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">{{ $pieza->serie ?? 's/serie' }}</span>
                    </div>
                @endforeach

                @foreach ($this->piezasCantidadCarrito as $p)
                    <div class="flex items-center gap-2 text-sm py-1.5 border-b border-gray-100 last:border-0">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                        <i class="fas fa-cubes text-gray-300 text-xs shrink-0"></i>
                        <span class="text-gray-700 truncate">{{ $p->nombre }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-auto">×{{ $p->cantidad_solicitada }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center text-sm">
                <span class="text-gray-500">Total de items</span>
                <span class="font-bold text-gray-900 text-base">{{ $this->seleccionCount }}</span>
            </div>
        </div>
    @endunless
