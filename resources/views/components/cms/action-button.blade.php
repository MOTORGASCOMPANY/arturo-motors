@props([
    'icon',
    'variant' => 'ghost',
    'wireClick' => null,
    'title' => null,
    'disabled' => false,
])

@php
    $variantClasses = [
        'ghost'   => 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200',
        'warning' => 'bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-100',
        'success' => 'bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100',
        'danger'  => 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100',
        'default' => 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200',
    ];
@endphp

<button {{ $attributes->merge([
        'class' => 'w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed '
            . ($variantClasses[$variant] ?? $variantClasses['default']),
    ]) }}
    {!! $wireClick ? "wire:click=\"{$wireClick}\"" : '' !!}
    {{ $disabled ? 'disabled' : '' }}
    title="{{ $title ?? '' }}">
    <i class="{{ $icon }} text-xs"></i>
</button>
