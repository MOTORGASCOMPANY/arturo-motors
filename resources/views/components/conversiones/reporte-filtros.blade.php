@props([
    'sedes',
    'estados',
    'filtroSede',
    'filtroEstado',
    'filtroFechaDesde',
    'filtroFechaHasta',
    'filtroBadge',
    'puedeLimpiar',
])

{{-- Filtros de sede / estado / rango de fechas --}}
<div class="bg-white rounded-card shadow-card border border-gray-200 p-5 font-inter">
    <div class="flex flex-wrap items-center gap-x-2 gap-y-2 mb-4">
        <i class="fas fa-sliders text-gray-400 shrink-0"></i>
        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Filtros</span>
        <span class="ml-auto max-w-full inline-flex items-center gap-1.5 rounded-full bg-brand-50 border border-brand-200 text-brand-700 px-3 py-1 text-xs font-bold break-words">
            <i class="fas fa-filter text-[10px] shrink-0"></i>
            {{ $filtroBadge }}
        </span>
    </div>

    <div class="flex flex-wrap items-end gap-6">
        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500">Sede</label>
            <select wire:model.live="filtroSede"
                class="rounded-card border-gray-200 text-sm py-2 px-3 focus:border-brand-500 focus:ring-brand-500 bg-gray-50 min-w-[10rem]">
                <option value="">Todas</option>
                @foreach ($sedes as $s)
                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500">Estado</label>
            <select wire:model.live="filtroEstado"
                class="rounded-card border-gray-200 text-sm py-2 px-3 focus:border-brand-500 focus:ring-brand-500 bg-gray-50 min-w-[10rem]">
                <option value="todos">Todos</option>
                {{-- Solo estados con órdenes reales en la BD (para la sede seleccionada) --}}
                @foreach ($estados as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500">Desde</label>
            <input type="date" wire:model.live="filtroFechaDesde"
                class="rounded-card border-gray-200 text-sm py-2 px-3 focus:border-brand-500 focus:ring-brand-500 bg-gray-50">
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500">Hasta</label>
            <input type="date" wire:model.live="filtroFechaHasta"
                class="rounded-card border-gray-200 text-sm py-2 px-3 focus:border-brand-500 focus:ring-brand-500 bg-gray-50">
        </div>

        @if ($puedeLimpiar)
            <button wire:click="limpiarFiltros"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-brand-700 bg-gray-100 hover:bg-brand-50 border border-gray-200 hover:border-brand-200 rounded-full px-3.5 py-2 ml-auto transition-colors">
                <i class="fas fa-xmark"></i>
                Limpiar filtros
            </button>
        @endif
    </div>
</div>
