@props(['target', 'label', 'onclick', 'icon' => 'fa-check'])

<button type="button" wire:loading.attr="disabled" x-on:click="{{ $onclick }}"
    class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
    <span wire:loading.remove wire:target="{{ $target }}"><i class="fas {{ $icon }} mr-1"></i> {{ $label }}</span>
    <span wire:loading wire:target="{{ $target }}">Guardando...</span>
</button>
