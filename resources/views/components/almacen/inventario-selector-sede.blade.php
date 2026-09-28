{{-- Selector de sede tipo pills/segmented con sync URL --}}
<div class="flex items-center gap-2" role="group" aria-label="Filtrar por sede">
    {{-- Botón "Todas" --}}
    <button type="button"
        wire:click="$set('filtroSede', null)"
        wire:loading.attr="disabled"
        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
            {{ $filtroSede === null
                ? 'bg-indigo-600 text-white shadow-md'
                : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
        aria-pressed="{{ $filtroSede === null ? 'true' : 'false' }}">
        <i class="fas fa-globe-americas text-[12px]"></i>
        <span>Todas</span>
    </button>

    {{-- Pills por cada sede --}}
    @foreach ($sedes as $s)
        <button type="button"
            wire:click="$set('filtroSede', {{ $s->id }})"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
                {{ $filtroSede === $s->id
                    ? 'bg-indigo-600 text-white shadow-md'
                    : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
            aria-pressed="{{ $filtroSede === $s->id ? 'true' : 'false' }}">
            <i class="fas fa-map-marker-alt text-[11px]"></i>
            <span>{{ $s->nombre }}</span>
        </button>
    @endforeach
</div>

{{-- Badge informativo (opcional, se usa en el header) --}}
@if (isset($showBadge) && $showBadge)
    <span class="ml-3 inline-flex items-center gap-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1 text-xs font-bold">
        <i class="fas fa-filter text-[10px]"></i>
        {{ $sedeLabel ?? ($filtroSede === null ? 'Todas las sedes' : $sedes->firstWhere('id', $filtroSede)?->nombre ?? 'Todas') }}
    </span>
@endif