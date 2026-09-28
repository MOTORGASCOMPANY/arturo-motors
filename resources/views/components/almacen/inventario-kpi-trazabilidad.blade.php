{{-- KPI 3: Trazabilidad de piezas con serie --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 transition-all duration-300 hover:shadow-md">

    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center shrink-0">
            <i class="fas fa-barcode text-cyan-600 text-xl"></i>
        </div>
        <div>
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider">Trazabilidad de piezas con serie</h3>
            <p class="text-slate-400 text-xs mt-0.5">Porcentaje con lote Produce registrado</p>
        </div>
    </div>

    <div class="flex items-center gap-6 mb-4">
        <div class="text-3xl font-bold text-slate-900">{{ $trazabilidad['porcentaje'] }}%</div>
        <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full bg-cyan-500 transition-all duration-500"
                style="width: {{ $trazabilidad['porcentaje'] }}%"></div>
        </div>
    </div>

    <p class="text-sm text-slate-600 mb-4">
        <span class="font-semibold">{{ $trazabilidad['conProduce'] }}</span> de <span class="font-semibold">{{ $trazabilidad['total'] }}</span> con lote Produce
    </p>

    {{-- Botón ver pendientes --}}
    @if ($trazabilidad['pendientes']->isNotEmpty())
        <button type="button"
            wire:click="$dispatch('abrir-modal-trazabilidad', { pendientes: @json($trazabilidad['pendientes']) })"
            class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-800 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 rounded-full px-3 py-1.5 transition-colors">
            <i class="fas fa-list text-[10px]"></i>
            Ver {{ $trazabilidad['pendientes']->count() }} pendientes
        </button>
    @endif
</div>

{{-- Modal pendientes (Alpine) --}}
<div x-data="{ open: false, items: [] }"
    @abrir-modal-trazabilidad.window="open = true; items = $event.detail.pendientes"
    x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150"
    class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
    <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden" x-trap.noscroll="open">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Piezas sin lote Produce</h3>
            <button @click="open = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 max-h-64 overflow-y-auto divide-y divide-slate-100">
            <template x-for="item in items" :key="item.id">
                <div class="py-2 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-barcode text-cyan-600 text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800 truncate" x-text="item.producto.nombre"></p>
                        <p class="text-xs text-slate-500 truncate">
                            <span x-text="item.serie || '—'"></span> ·
                            <span x-text="item.sede.nombre"></span>
                        </p>
                    </div>
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full">Sin Produce</span>
                </div>
            </template>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 flex justify-end">
            <button @click="open = false" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Cerrar</button>
        </div>
    </div>
</div>