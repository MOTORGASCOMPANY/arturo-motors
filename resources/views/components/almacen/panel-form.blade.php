@props(['title', 'toggle', 'color' => 'gray'])

<div {{ $attributes->merge(['class' => "bg-{$color}-50 border border-{$color}-200 rounded-xl p-4"]) }}>
    <div class="flex items-center justify-between mb-3">
        <h5 class="text-sm font-bold text-gray-800">{{ $title }}</h5>
        <button type="button" wire:click="{{ $toggle }}" class="text-gray-500 hover:text-gray-700 text-sm">
            <i class="fas fa-times"></i> Cancelar
        </button>
    </div>
    {{ $slot }}
</div>
