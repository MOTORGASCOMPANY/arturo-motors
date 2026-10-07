{{-- Gráfico: Barras agrupadas Sellados / Completados / Consumidos por sede --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">

    <div class="flex items-center justify-between mb-6">
        <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-chart-bar text-slate-400"></i>
            Estados de kits por sede
        </h3>
        <span class="text-sm font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
            {{ $grafico['labels'][0] ?? 'Todas' }}
        </span>
    </div>

    @if (!empty($grafico['labels']) &&
        array_sum($grafico['sellados'] ?? []) + array_sum($grafico['completados'] ?? []) + array_sum($grafico['consumidos'] ?? []) > 0)
        {{-- Este div lleva los datos. Livewire lo actualiza y el JS los detecta --}}
        <div x-data="inventarioGraficoBarras()"
             data-grafico="{{ json_encode($grafico) }}">

            {{-- wire:ignore evita que Livewire toque el canvas. Altura fija en el contenedor --}}
            <div wire:ignore class="relative w-full" style="height: 288px;">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        {{-- Leyenda --}}
        <div class="flex flex-wrap justify-center gap-4 mt-4 text-sm">
            <span class="flex items-center gap-1.5"><i class="fas fa-circle text-cyan-500 text-[10px]"></i> Sellados</span>
            <span class="flex items-center gap-1.5"><i class="fas fa-circle text-emerald-500 text-[10px]"></i> Completados</span>
            <span class="flex items-center gap-1.5"><i class="fas fa-circle text-red-500 text-[10px]"></i> Consumidos</span>
        </div>
    @else
        <x-reportes.empty-state
            icon="fa-chart-bar"
            titulo="Sin kits por sede"
            mensaje="No hay kits registrados para mostrar con el filtro aplicado." />
    @endif
</div>

