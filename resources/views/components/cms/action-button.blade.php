@props([
    'icon',
    'variant' => 'ghost',
    'wireClick' => null,
    'title' => null,
    'disabled' => false,
])

@php
    $variantClasses = [
        'ghost'   => 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 hover:text-gray-800 hover:border-gray-300 hover:shadow-sm',
        'warning' => 'bg-amber-100 text-amber-700 border border-amber-200 hover:bg-amber-200 hover:text-amber-800 hover:border-amber-300 hover:shadow-amber-100/50 hover:shadow-md',
        'success' => 'bg-green-100 text-green-700 border border-green-200 hover:bg-green-200 hover:text-green-800 hover:border-green-300 hover:shadow-green-100/50 hover:shadow-md',
        'danger'  => 'bg-red-100 text-red-600 border border-red-200 hover:bg-red-200 hover:text-red-700 hover:border-red-300 hover:shadow-red-100/50 hover:shadow-md',
        'default' => 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 hover:text-gray-800 hover:border-gray-300 hover:shadow-sm',
    ];
@endphp

<button {{ $attributes->merge([
        'class' => 'w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed '
            . ($variantClasses[$variant] ?? $variantClasses['default']),
    ]) }}
    {!! $wireClick ? "wire:click=\"{$wireClick}\"" : '' !!}
    {{ $disabled ? 'disabled' : '' }}
    title="{{ $title ?? '' }}">
    <i class="{{ $icon }} text-xs"></i>
</button>