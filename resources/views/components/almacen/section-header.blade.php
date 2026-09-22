@props(['icono', 'iconoColor', 'titulo', 'count' => null, 'badgeClass' => null, 'hint' => null, 'slot' => null])

<div class="flex items-center gap-2 px-4 py-3 {{ $attributes->get('headerClass', 'border-b border-gray-100') }}">
    <i class="fas {{ $icono }} {{ $iconoColor }}"></i>
    <h3 class="text-sm font-bold text-gray-800">{{ $titulo }}</h3>
    @if ($count !== null)
        <span class="text-sm text-gray-500">({{ $count }})</span>
    @endif
    @if ($badgeClass)
        <span class="ml-auto px-2 py-0.5 {{ $badgeClass }} text-xs font-bold rounded-full tabular-nums">
            {{ $count ?? '' }}
        </span>
    @endif
    @if ($hint)
        <span class="ml-auto text-xs text-gray-500">{{ $hint }}</span>
    @endif
    {{ $slot }}
</div>
