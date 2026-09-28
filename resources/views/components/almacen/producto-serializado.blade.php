@props(['producto'])

@php
    $esquema      = $producto->categoria->esquema_atributos ?? ['serie'];
    $campos       = is_string($esquema) ? json_decode($esquema, true) : $esquema;
    $camposUnidad = \App\Livewire\Almacen\RecepcionAlta::camposPorUnidad($campos);
    $tieneProduce = in_array('produce', $campos);
    $cantProducto = (int) ($this->cantidades[$producto->id] ?? 0);
@endphp

<div {{ $attributes }}>
    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
            <i class="fas fa-barcode text-indigo-600 text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-800 truncate">{{ $producto->nombre }}</p>
            <span class="text-[10px] px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-semibold">Serializado</span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <x-almacen.quantity-stepper :model="'cantidades.' . $producto->id" :productId="$producto->id" color="indigo" />
        </div>
    </div>

    @if ($tieneProduce && $cantProducto > 0)
        <div class="mt-2 ml-14 flex items-center gap-2">
            <label class="flex items-center gap-1 text-xs text-gray-500">
                <span class="text-[10px] font-semibold text-indigo-600">Produce</span>
                <input type="text"
                    wire:model.live="produces.{{ $producto->id }}"
                    placeholder="Ej: 370-2024"
                    class="text-sm border border-indigo-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-500 bg-indigo-50"
                    maxlength="50"
                    style="min-width: 140px;">
            </label>
            <span class="text-[10px] text-gray-400 italic">— aplica a todas las unidades</span>
        </div>
    @endif

    @if ($cantProducto > 0 && count($camposUnidad) > 0)
        <div class="mt-2 ml-14 space-y-1 border-l-2 border-indigo-200 pl-3">
            @for ($i = 1; $i <= $cantProducto; $i++)
                <div wire:key="prod-{{ $producto->id }}-u-{{ $i }}" class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-400 w-8">#{{ $i }}</span>
                    @foreach ($camposUnidad as $campo)
                        @php
                            $placeholder = ucfirst(str_replace('_', ' ', $campo));
                            $label = match ($campo) {
                                'capacidad' => 'Capac.',
                                default     => ucfirst($campo),
                            };
                        @endphp
                        <label class="flex items-center gap-1 text-xs text-gray-500 w-auto">
                            <span class="w-auto text-[10px]">{{ $label }}</span>
                            <input type="text"
                                wire:model.live="series.{{ $producto->id }}.{{ $i - 1 }}.{{ $campo }}"
                                @if ($campo === 'serie') data-serie data-etiqueta="{{ $producto->nombre }} #{{ $i }}" @endif
                                placeholder="{{ $placeholder }}"
                                class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-500"
                                maxlength="50"
                                style="min-width: 120px;">
                        </label>
                    @endforeach
                </div>
            @endfor
        </div>
    @endif
</div>
