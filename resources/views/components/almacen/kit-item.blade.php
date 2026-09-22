@props(['producto', 'sede', 'count' => null, 'icono' => 'fa-box', 'iconoColor' => 'text-indigo-500', 'cajaBg' => 'bg-indigo-50', 'hoverClass' => 'hover:bg-indigo-50', 'badgeClass' => 'bg-indigo-100 text-indigo-700', 'href' => null, 'tag' => null, 'tagClass' => null, 'sublabel' => null])

<li>
    <button type="button"
        @if ($href) wire:click="{{ $href }}" @endif
        class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors {{ $hoverClass }} focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
        
        <div class="w-9 h-9 rounded-lg {{ $cajaBg }} flex items-center justify-center shrink-0">
            <i class="fas {{ $icono }} {{ $iconoColor }} text-sm"></i>
        </div>
        
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-gray-800 truncate">{{ $producto ?? 'Producto' }}</p>
            @if ($sublabel)
                <p class="text-xs text-gray-500">{{ $sublabel }}</p>
            @elseif ($sede)
                <p class="text-xs text-gray-500">{{ $sede }}</p>
            @endif
        </div>

        @if ($tag)
            <span class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $tagClass ?? 'bg-gray-100 text-gray-600' }}">
                {{ $tag }}
            </span>
        @endif

        @if ($count !== null)
            <span class="px-2 py-0.5 {{ $badgeClass }} text-xs font-bold rounded-full tabular-nums">
                {{ $count }}
            </span>
        @endif
    </button>
</li>
