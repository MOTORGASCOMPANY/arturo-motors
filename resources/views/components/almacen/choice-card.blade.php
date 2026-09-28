@props(['icon', 'color' => 'indigo', 'title', 'subtitle', 'wireClick' => null, 'alpineClick' => null])

<button type="button"
    @if ($alpineClick) x-on:click="{{ $alpineClick }}" @else wire:click="{{ $wireClick }}" @endif
    class="flex items-center gap-4 p-5 rounded-xl border-2 border-gray-200 hover:border-{{ $color }}-400 hover:bg-{{ $color }}-50 transition text-left">
    <div class="w-12 h-12 rounded-xl bg-{{ $color }}-100 flex items-center justify-center shrink-0">
        <i class="fas {{ $icon }} text-{{ $color }}-600 text-xl"></i>
    </div>
    <div>
        <p class="text-base font-bold text-gray-800">{{ $title }}</p>
        <p class="text-sm text-gray-500">{{ $subtitle }}</p>
    </div>
</button>
