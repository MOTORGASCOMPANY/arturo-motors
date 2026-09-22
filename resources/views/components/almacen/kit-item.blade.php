@props(['nombre', 'sede' => null, 'id' => null, 'icon' => 'fa-box', 'iconBg' => 'bg-indigo-50', 'iconColor' => 'text-indigo-500', 'actionRoute' => null, 'actionId' => null, 'actionLabel' => null])

<li class="flex items-center gap-3 bg-white px-4 py-3">
    <div class="w-9 h-9 rounded-lg {{ $iconBg }} flex items-center justify-center shrink-0">
        <i class="fas {{ $icon }} {{ $iconColor }} text-sm"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-bold text-gray-800 truncate">{{ $nombre }}</p>
        @if ($sede)
            <p class="text-xs text-gray-500">{{ $sede }}@if ($id) <span class="text-gray-300">|</span> #{{ $id }}@endif</p>
        @endif
    </div>
    @if ($actionRoute && $actionId)
        <button type="button"
            wire:click="{{ $actionRoute }}({{ $actionId }})"
            class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
            <i class="fas fa-plus mr-1"></i> {{ $actionLabel ?? 'Acción' }}
        </button>
    @endif
</li>
