@props(['titulo', 'icono', 'iconoColor', 'count', 'items', 'color', 'badgeClass', 'hoverClass', 'cajaBg', 'dispatchEvent' => null, 'actionLabel' => null, 'actionMethod' => null, 'showSede' => true, 'showCantidad' => true])

<section class="bg-white rounded-xl border border-gray-200 overflow-hidden">

    <x-almacen.section-header
        :icono="$icono"
        :iconoColor="$iconoColor"
        :titulo="$titulo"
        :count="$count"
        :badgeClass="$badgeClass"
    />

    <div class="bg-gray-50 p-2 space-y-2 max-h-[65vh] overflow-y-auto" x-data>

        @forelse ($items as $key => $item)
            @php
                // Soporta colecciones agrupadas por producto_id => items
                // o items individuales
                $esColeccion = $item instanceof \Illuminate\Support\Collection;
                $producto = $esColeccion ? ($item->first()?->producto ?? null) : ($item->producto ?? null);
                $sede = $esColeccion ? ($item->first()?->sede?->nombre ?? '—') : ($item->sede?->nombre ?? '—');
                $conteo = $esColeccion ? $item->count() : null;
                $productoId = $esColeccion ? $key : null;
            @endphp

            <button type="button"
                @if ($dispatchEvent && $esColeccion)
                    @click="$dispatch('{{ $dispatchEvent }}', { productoId: {{ $productoId }} })"
                @elseif ($actionMethod)
                    wire:click="{{ $actionMethod }}({{ $esColeccion ? $productoId : $item->id ?? 0 }})"
                @endif
                class="w-full text-left bg-white border border-gray-200 rounded-lg px-3 py-2.5 flex items-center gap-3 hover:shadow-sm transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                @if ($color) hover:border-{{ $color }}-400 @endif">

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $producto?->nombre ?? 'Producto' }}</p>
                    @if ($showCantidad && $conteo !== null)
                        <p class="text-xs text-gray-500">{{ $conteo }} unidad(es) <span class="text-gray-300">|</span> {{ $sede }}</p>
                    @elseif ($showSede)
                        <p class="text-xs text-gray-500">{{ $sede }}</p>
                    @endif
                </div>

                @if ($conteo !== null)
                    <span class="px-2 py-0.5 {{ $badgeClass }} text-xs font-bold rounded-full tabular-nums">
                        {{ $conteo }}
                    </span>
                @endif
            </button>

        @empty

            <p class="text-gray-400 text-sm text-center py-6">Sin {{ strtolower($titulo) }}</p>

        @endforelse

    </div>
</section>
