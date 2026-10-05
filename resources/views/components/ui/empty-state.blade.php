@props([
    'icon' => 'fa-circle-info',
    'message' => 'No hay datos disponibles para mostrar en este momento',
    'size' => 'md',           // sm | md | lg
    'variant' => 'default',   // default | dashed | card
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => 'w-10 h-10 text-lg px-4 py-8',
        'md' => 'w-14 h-14 text-xl px-6 py-14',
        'lg' => 'w-16 h-16 text-2xl px-8 py-16',
    ];

    $variantClasses = [
        'default' => 'bg-white border border-gray-200',
        'dashed'  => 'bg-white border-2 border-dashed border-gray-300',
        'card'    => 'bg-gray-50 border border-gray-200 shadow-sm',
    ];

    $textSizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-sm',
        'lg' => 'text-base',
    ];

    $iconSizeClasses = [
        'sm' => 'text-lg',
        'md' => 'text-xl',
        'lg' => 'text-2xl',
    ];
@endphp

<div class="rounded-xl {{ $variantClasses[$variant] ?? 'bg-white border border-gray-200' }} {{ $class }} {{ $sizeClasses[$size] ?? 'px-6 py-14' }} text-center">
    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
        <i class="fas {{ $icon }} text-gray-400 {{ $iconSizeClasses[$size] ?? 'text-xl' }}"></i>
    </div>
    <p class="text-gray-500 font-medium {{ $textSizeClasses[$size] ?? 'text-sm' }}">{{ $message }}</p>
</div>