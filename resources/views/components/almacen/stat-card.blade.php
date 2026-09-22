@props(['icono', 'label', 'hint', 'valor', 'color', 'iconoColor', 'hover', 'tipo', 'clickAction' => null])

<button type="button"
    @if ($clickAction) wire:click="{{ $clickAction }}" @endif
    class="bg-white p-3.5 text-left transition-colors {{ $hover }} focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
    <span class="flex items-center gap-2 text-sm font-medium text-gray-600">
        <i class="fas {{ $icono }} {{ $iconoColor }}"></i>
        {{ $label }}
    </span>
    <p class="text-3xl font-black {{ $color }} mt-1 leading-none tabular-nums">{{ $valor }}</p>
    <span class="text-xs text-gray-400 mt-1 block">{{ $hint }}</span>
</button>
