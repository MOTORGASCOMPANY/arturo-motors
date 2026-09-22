@props(['icon', 'color' => 'gray', 'title', 'count' => null, 'hint' => null, 'highlight' => false, 'slot' => null])

@php
    $borderClass = $highlight ? 'border-b border-orange-200 bg-orange-50' : 'border-b border-gray-100';
    $iconColorMap = [
        'orange' => 'text-orange-500',
        'amber'  => 'text-amber-500',
        'green'  => 'text-green-500',
        'red'    => 'text-red-500',
        'purple' => 'text-purple-500',
        'blue'   => 'text-blue-500',
        'indigo' => 'text-indigo-500',
        'gray'   => 'text-gray-500',
    ];
    $iconClass = $iconColorMap[$color] ?? 'text-gray-500';
@endphp

<div class="flex items-center gap-2 px-4 py-3 {{ $borderClass }}">
    <i class="fas {{ $icon }} {{ $iconClass }}"></i>
    <h3 class="text-sm font-bold text-gray-800">{{ $title }}</h3>
    @if ($count !== null)
        <span class="ml-auto px-2 py-0.5 bg-{{ $color }}-100 text-{{ $color }}-700 text-xs font-bold rounded-full tabular-nums">{{ $count }}</span>
    @endif
    @if ($hint)
        <span class="ml-auto text-xs text-orange-700">{{ $hint }}</span>
    @endif
    {{ $slot }}
</div>
