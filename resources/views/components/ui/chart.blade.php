@props([
    'id',
    'title',
    'icon' => 'fa-chart-bar',
    'iconColor' => 'text-gray-400',
    'height' => '280px',
    'emptyMessage' => null,
    'emptyIcon' => 'fa-circle-info',
    'emptyId' => null,           // opcional: si no se pasa, usa 'empty-' + id
    'legend' => null,           // array de ['label' => 'color'] o string HTML
    'subtitle' => null,
    'class' => '',
    'wireIgnore' => true,
])

@php
    $emptyId = $emptyId ?? 'empty-' . $id;
    $canvasId = $id;
    $defaultMessage = $emptyMessage ?? 'No hay datos disponibles para mostrar en este momento';
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6 {{ $class }}" {{ $wireIgnore ? 'wire:ignore' : '' }} wire:key="{{ $id }}">
    <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2 mb-4">
        <h3 class="min-w-0 flex items-start gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
            <i class="fas {{ $icon }} {{ $iconColor }} shrink-0 mt-0.5"></i>
            <span>{{ $title }}</span>
        </h3>
        @if($subtitle)
            <span class="max-w-full text-[11px] font-bold text-brand-700 bg-brand-50 border border-brand-200 rounded-full px-2.5 py-1 break-words">{{ $subtitle }}</span>
        @endif
    </div>

    <div class="relative w-full" style="height: {{ $height }};">
        <canvas id="{{ $canvasId }}"></canvas>

        {{-- Empty State (inicialmente oculto, charts.js lo muestra si no hay datos) --}}
        <div id="{{ $emptyId }}" class="hidden flex flex-col items-center justify-center py-10 text-center h-full">
            <x-ui.empty-state
                :icon="$emptyIcon"
                :message="$defaultMessage"
                size="md"
                variant="default"
            />
        </div>
    </div>

    {{-- Leyenda opcional --}}
    @if($legend)
        <div class="flex flex-wrap gap-4 mt-3 justify-center text-xs font-semibold text-gray-600">
            @if(is_array($legend))
                @foreach($legend as $label => $color)
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm" style="background: {{ $color }}"></span>
                        {{ $label }}
                    </span>
                @endforeach
            @else
                {!! $legend !!}
            @endif
        </div>
    @endif
</div>