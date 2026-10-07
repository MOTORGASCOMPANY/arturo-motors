@props(['idx', 'comp'])

<div wire:key="comp-ser-{{ $idx }}" class="p-3 rounded-xl border bg-indigo-50 border-indigo-200">
    <div class="flex items-center gap-2 mb-2">
        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
            <i class="fas fa-microchip text-xs"></i>
        </div>
        <p class="flex-1 text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</p>
        <span class="text-[10px] px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-semibold">{{ count($comp['unidades'] ?? []) }} uds</span>
        <button type="button"
                data-idx="{{ $idx }}" data-nombre="{{ $comp['nombre'] }}"
                x-on:click="almacenAcciones.quitarComponente($wire, $el.dataset)"
                class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center shrink-0 transition" title="Quitar componente">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    <div class="space-y-2 ml-9">
        @foreach (($comp['unidades'] ?? []) as $uIdx => $unidad)
            <div wire:key="comp-{{ $idx }}-u-{{ $uIdx }}" class="flex flex-wrap items-center gap-2 p-2 bg-white rounded-lg border border-indigo-100">
                <span class="text-[10px] font-bold text-indigo-400 w-6">#{{ $uIdx + 1 }}</span>
                @foreach ($comp['campos_esquema'] ?? ['serie'] as $campo)
                    @php
                        $label = match ($campo) {
                            'capacidad' => 'Capac.',
                            default     => ucfirst(str_replace('_', ' ', $campo)),
                        };
                        $esSerie = $campo === 'serie';
                    @endphp
                    <label class="flex items-center gap-1 text-xs text-gray-500">
                        <span class="text-[10px] {{ $esSerie ? 'font-bold text-indigo-700' : '' }}">{{ $label }}</span>
                        <input type="text"
                            wire:model.live="modalComponentes.{{ $idx }}.unidades.{{ $uIdx }}.{{ $campo }}"
                            @if ($esSerie) data-serie data-etiqueta="{{ $comp['nombre'] }} #{{ $uIdx + 1 }}" @endif
                            placeholder="{{ $label }}"
                            class="text-sm border {{ $esSerie ? 'border-indigo-400 bg-white font-semibold' : 'border-indigo-200 bg-white' }} rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-500"
                            maxlength="50"
                            style="min-width: {{ $esSerie ? '140px' : '100px' }};">
                    </label>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
