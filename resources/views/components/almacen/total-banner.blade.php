@props(['total', 'icon', 'bg' => 'gray', 'iconColor' => 'indigo', 'suffix' => ''])

@if ($total > 0)
    <div {{ $attributes->merge(['class' => "bg-{$bg}-50 border border-{$bg}-200 rounded-lg p-4 mb-6"]) }}>
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-{{ $iconColor }}-100 rounded-full flex items-center justify-center">
                <i class="fas {{ $icon }} text-{{ $iconColor }}-600 text-sm"></i>
            </div>
            <p class="text-sm font-semibold text-gray-700">
                Se recibirán <strong>{{ $total }}</strong> {{ $suffix }} {{ $slot }}
            </p>
        </div>
    </div>
@endif
