@props(['model', 'min' => 0, 'max' => 99, 'color' => 'indigo', 'size' => 'md'])

@php
    $btnBgMap = [
        'indigo' => ['decr' => 'bg-gray-200 hover:bg-gray-300 text-gray-600', 'incr' => 'bg-indigo-100 hover:bg-indigo-200 text-indigo-700'],
        'amber'  => ['decr' => 'bg-gray-200 hover:bg-gray-300 text-gray-600', 'incr' => 'bg-amber-100 hover:bg-amber-200 text-amber-700'],
    ];
    $btns = $btnBgMap[$color] ?? $btnBgMap['indigo'];
    $sizeMap = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-9 h-9 text-sm',
        'lg' => 'w-10 h-10 text-base',
    ];
    $sz = $sizeMap[$size] ?? $sizeMap['md'];
@endphp

<div class="flex items-center gap-2 shrink-0">
    <button type="button"
        wire:click="$set('{{ $model }}', Math.max({{ $min }}, {{ $model }} - 1))"
        class="{{ $sz }} rounded-lg {{ $btns['decr'] }} flex items-center justify-center font-bold transition-colors">−</button>
    <input type="number" min="{{ $min }}" max="{{ $max }}"
        wire:model.live="{{ $model }}"
        class="w-20 text-center text-lg font-bold border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    <button type="button"
        wire:click="$set('{{ $model }}', {{ $model }} + 1)"
        class="{{ $sz }} rounded-lg {{ $btns['incr'] }} flex items-center justify-center font-bold transition-colors">+</button>
</div>
