@props([
    'method',
    'label',
    'icon' => 'fa-truck',
    'color' => 'indigo',
    'cancelAction' => null,
    'align' => 'end',
    'data' => [],
])

@php
    $bg    = $color === 'amber' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-indigo-600 hover:bg-indigo-700';
    $row   = $align === 'between' ? 'justify-between items-center' : 'justify-end';
@endphp

<div {{ $attributes->merge(['class' => 'flex ' . $row . ' gap-3 mt-6 pt-4 border-t']) }}>
    @if ($cancelAction)
        <button type="button" wire:click="{{ $cancelAction }}"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
    @else
        <button type="button" x-on:click="almacenAcciones.cancelarComponentes($wire)"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
    @endif

    <button type="button" wire:loading.attr="disabled"
        data-metodo="{{ $method }}"
        @foreach ($data as $k => $v){{ $k }}="{{ $v }}" @endforeach
        x-on:click="almacenAcciones.accionConfirmada($wire, $el.dataset)"
        class="px-6 py-2.5 text-sm font-bold text-white {{ $bg }} rounded-lg transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
        <span wire:loading.remove wire:target="{{ $method }}">
            <i class="fas {{ $icon }} mr-1"></i> {{ $label }}
        </span>
        <span wire:loading wire:target="{{ $method }}">Guardando...</span>
    </button>
</div>
