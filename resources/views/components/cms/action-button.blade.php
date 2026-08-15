@props([
    'icon',
    'variant' => 'ghost',
    'wireClick' => null,
    'title' => null,
    'disabled' => false,
])

@php
    $variantClasses = [
        'ghost'   => 'text-gray-400 hover:text-gray-600 hover:bg-gray-100',
        'warning' => 'text-amber-500 hover:text-amber-600 hover:bg-amber-50',
        'success' => 'text-green-600 hover:text-green-700 hover:bg-green-50',
        'danger'  => 'text-red-500 hover:text-red-600 hover:bg-red-50',
        'default' => 'text-gray-400 hover:text-gray-600 hover:bg-gray-100',
    ];
@endphp

<button {{ $attributes->merge([
        'class' => 'w-9 h-9 flex items-center justify-center rounded-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed '
            . ($variantClasses[$variant] ?? $variantClasses['default']),
    ]) }}
    {!! $wireClick ? "wire:click=\"{$wireClick}\"" : '' !!}
    {{ $disabled ? 'disabled' : '' }}
    title="{{ $title ?? '' }}">
    <i class="{{ $icon }} text-xs"></i>
</button>