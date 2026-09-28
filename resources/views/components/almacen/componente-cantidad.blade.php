@props(['idx', 'comp'])

<div wire:key="comp-cant-{{ $idx }}" class="flex items-center gap-2 p-2.5 rounded-xl border bg-amber-50 border-amber-200">
    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
        <i class="fas fa-cubes text-xs"></i>
    </div>
    <p class="flex-1 text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</p>
    <div class="flex items-center gap-1.5 shrink-0">
        <button type="button" wire:click="$set('modalComponentes.{{ $idx }}.cantidad', Math.max(1, {{ $comp['cantidad'] }} - 1))"
                class="w-7 h-7 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-sm">−</button>
        <input type="number" min="1" max="99" wire:model.live="modalComponentes.{{ $idx }}.cantidad"
               class="w-14 text-center text-sm font-bold border border-amber-300 rounded-lg py-1 focus:ring-2 focus:ring-amber-500">
        <button type="button" wire:click="$set('modalComponentes.{{ $idx }}.cantidad', {{ $comp['cantidad'] }} + 1)"
                class="w-7 h-7 rounded-lg bg-amber-200 hover:bg-amber-300 flex items-center justify-center text-amber-700 font-bold text-sm">+</button>
    </div>
    <button type="button"
            data-idx="{{ $idx }}" data-nombre="{{ $comp['nombre'] }}"
            x-on:click="recepcionSwal.quitarComponente($wire, $el.dataset)"
            class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center shrink-0 transition" title="Quitar componente">
        <i class="fas fa-times text-xs"></i>
    </button>
</div>
