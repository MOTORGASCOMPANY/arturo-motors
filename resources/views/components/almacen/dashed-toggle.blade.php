@props(['label', 'toggle'])

<button type="button" wire:click="{{ $toggle }}"
    class="w-full flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 hover:border-gray-400 transition font-semibold text-sm">
    <i class="fas fa-plus-circle text-lg"></i> {{ $label }}
</button>
