@props(['icon', 'color' => 'gray', 'title', 'items', 'emptyMessage' => null, 'dispatchEvent' => 'ver-componentes-kit', 'showCantidad' => true])

@php
    $total = $items->flatten()->count();
    $colorMap = [
        'amber'  => ['border' => 'border-amber-400', 'badge' => 'bg-amber-100 text-amber-700', 'hover' => 'hover:bg-amber-50'],
        'orange' => ['border' => 'border-orange-400', 'badge' => 'bg-orange-100 text-orange-700', 'hover' => 'hover:bg-orange-50'],
        'green'  => ['border' => 'border-green-400', 'badge' => 'bg-green-100 text-green-700', 'hover' => 'hover:bg-green-50'],
        'red'    => ['border' => 'border-red-400', 'badge' => 'bg-red-100 text-red-700', 'hover' => 'hover:bg-red-50'],
        'purple' => ['border' => 'border-purple-400', 'badge' => 'bg-purple-100 text-purple-700', 'hover' => 'hover:bg-purple-50'],
        'gray'   => ['border' => 'border-gray-200', 'badge' => 'bg-gray-100 text-gray-700', 'hover' => 'hover:bg-gray-50'],
    ];
    $colors = $colorMap[$color] ?? $colorMap['gray'];
@endphp

<section class="bg-white rounded-xl {{ $colors['border'] }} border overflow-hidden">

    <x-almacen.section-header :icon="$icon" :color="$color" :title="$title" :count="$total" />

    <div class="bg-gray-50 p-2 space-y-2 max-h-[65vh] overflow-y-auto" x-data>
        @forelse ($items as $productoId => $itemsGroup)
            @php
                $prod = $itemsGroup->first()?->producto;
            @endphp
            <button type="button"
                @click="$dispatch('{{ $dispatchEvent }}', { productoId: {{ $productoId }} })"
                class="w-full text-left bg-white border border-gray-200 rounded-lg px-3 py-2.5 flex items-center gap-3 {{ $colors['hover'] }} hover:shadow-sm transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $prod?->nombre ?? 'Producto' }}</p>
                    @if ($showCantidad)
                        <p class="text-xs text-gray-500">{{ $itemsGroup->count() }} unidad(es) <span class="text-gray-300">|</span> {{ $itemsGroup->first()?->sede?->nombre ?? '—' }}</p>
                    @else
                        <p class="text-xs text-gray-500">{{ $itemsGroup->first()?->sede?->nombre ?? '—' }}</p>
                    @endif
                </div>
                <span class="px-2 py-0.5 {{ $colors['badge'] }} text-xs font-bold rounded-full tabular-nums">
                    {{ $itemsGroup->count() }}
                </span>
            </button>
        @empty
            <x-almacen.empty-state :icon="$icon" :message="$emptyMessage ?? 'Sin ' . strtolower($title)" />
        @endforelse
    </div>
</section>
