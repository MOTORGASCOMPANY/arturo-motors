@props([
    'icon' => 'fa-chart-column',
    'titulo' => 'Sin datos para este período',
    'mensaje' => null,
    'hint' => null,
])

{{-- Estado vacío estándar de reportes: se muestra en lugar del canvas cuando no hay datos en el rango. --}}
<div data-reportes-empty="1" {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 text-center bg-slate-50/60 rounded-xl border border-dashed border-slate-200']) }}>
    <div class="w-14 h-14 bg-white shadow-sm rounded-full flex items-center justify-center mb-3">
        <i class="fas {{ $icon }} text-slate-300 text-xl"></i>
    </div>
    <p class="text-slate-600 font-semibold text-base">{{ $titulo }}</p>
    @if ($mensaje)
        <p class="text-slate-400 text-sm mt-1">{{ $mensaje }}</p>
    @endif
    @if ($hint)
        <p class="text-slate-400 text-xs mt-3">{!! $hint !!}</p>
    @endif
</div>
