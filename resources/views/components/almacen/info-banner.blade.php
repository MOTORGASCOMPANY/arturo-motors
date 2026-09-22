@props(['icon', 'color' => 'indigo', 'title', 'actionLabel' => null, 'actionMethod' => null, 'actionEvent' => null, 'slot' => null])

@php
    $bgMap = [
        'indigo' => 'bg-indigo-50 border-indigo-100',
        'amber'  => 'bg-amber-50 border-amber-100',
        'emerald'=> 'bg-emerald-50 border-emerald-100',
        'red'    => 'bg-red-50 border-red-100',
        'gray'   => 'bg-gray-50 border-gray-100',
    ];
    $textMap = [
        'indigo' => 'text-indigo-900',
        'amber'  => 'text-amber-900',
        'emerald'=> 'text-emerald-900',
        'red'    => 'text-red-900',
        'gray'   => 'text-gray-900',
    ];
    $iconColorMap = [
        'indigo' => 'text-indigo-500',
        'amber'  => 'text-amber-500',
        'emerald'=> 'text-emerald-500',
        'red'    => 'text-red-500',
        'gray'   => 'text-gray-500',
    ];
    $actionColorMap = [
        'indigo' => 'text-indigo-600 hover:underline',
        'amber'  => 'text-amber-600 hover:underline',
        'emerald'=> 'text-emerald-600 hover:underline',
    ];
    $bg = $bgMap[$color] ?? $bgMap['indigo'];
    $txt = $textMap[$color] ?? $textMap['indigo'];
    $icn = $iconColorMap[$color] ?? $iconColorMap['indigo'];
    $act = $actionColorMap[$color] ?? 'text-indigo-600 hover:underline';
@endphp

<div class="flex items-center justify-between mb-5 p-3 {{ $bg }} rounded-lg border">
    <p class="text-sm {{ $txt }}">
        <i class="fas {{ $icon }} mr-1.5 {{ $icn }}"></i>
        {!! $title !!}
    </p>
    @if ($actionLabel)
        @if ($actionEvent)
            <button type="button" x-on:click="$dispatch('{{ $actionEvent }}')"
                class="text-xs font-semibold {{ $act }} whitespace-nowrap">
                <i class="fas fa-times mr-1"></i> {{ $actionLabel }}
            </button>
        @elseif ($actionMethod)
            <button type="button" wire:click="{{ $actionMethod }}"
                class="text-xs font-semibold {{ $act }} whitespace-nowrap">
                <i class="fas fa-arrow-left mr-1"></i> {{ $actionLabel }}
            </button>
        @endif
    @endif
    {{ $slot }}
</div>
