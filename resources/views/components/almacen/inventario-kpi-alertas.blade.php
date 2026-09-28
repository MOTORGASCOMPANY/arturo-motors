{{-- KPI 4: Alertas de stock --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 transition-all duration-300 hover:shadow-md">

    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
            <i class="fas fa-triangle-exclamation text-amber-600 text-xl"></i>
        </div>
        <div>
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider">Alertas de stock</h3>
            <p class="text-slate-400 text-xs mt-0.5">Productos sin stock y stock bajo (≤{{ $alertas['umbral'] }})</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="p-3 rounded-xl bg-red-50 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $alertas['sinStock']->count() }}</p>
            <p class="text-xs text-red-700">Sin stock</p>
        </div>
        <div class="p-3 rounded-xl bg-amber-50 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $alertas['stockBajo']->count() }}</p>
            <p class="text-xs text-amber-700">Stock bajo</p>
        </div>
    </div>

    <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
        @foreach ($alertas['sinStock'] as $p)
            <div class="flex items-center gap-3 py-1.5">
                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-times text-red-500 text-[10px]"></i>
                </div>
                <p class="text-sm font-medium text-slate-800 truncate flex-1">{{ $p['nombre'] }}</p>
                <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-bold rounded-full">0</span>
            </div>
        @endforeach
        @foreach ($alertas['stockBajo'] as $p)
            <div class="flex items-center gap-3 py-1.5">
                <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation text-amber-500 text-[10px]"></i>
                </div>
                <p class="text-sm font-medium text-slate-800 truncate flex-1">{{ $p['nombre'] }}</p>
                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-full">{{ $p['cantidad'] }}</span>
            </div>
        @endforeach
        @if ($alertas['sinStock']->isEmpty() && $alertas['stockBajo']->isEmpty())
            <div class="py-6 text-center text-slate-400 text-sm">
                <i class="fas fa-check-circle text-emerald-500 text-2xl mb-2"></i>
                <p>Todo en orden, sin alertas</p>
            </div>
        @endif
    </div>
</div>