{{-- KPI 1: Capacidad de armado --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 transition-all duration-300 hover:shadow-md">
    
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
            <i class="fas fa-cogs text-indigo-600 text-xl"></i>
        </div>
        <div>
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider">Capacidad de armado</h3>
            <p class="text-slate-400 text-xs mt-0.5">Kits completos armables con stock actual</p>
        </div>
    </div>

    <div class="flex items-baseline gap-4 mb-4">
        <span class="text-4xl font-bold tabular-nums text-slate-900">{{ $capacidad['kitsArmables'] }}</span>
        <span class="text-slate-500 text-sm">kits armables</span>
    </div>

    {{-- Mini barras horizontales por componente --}}
    <div class="space-y-2.5">
        @foreach ($capacidad['barras'] as $barra)
            <div class="flex items-center gap-3">
                <div class="w-24 text-xs font-medium text-slate-600 truncate pr-2">
                    {{ $barra['nombre'] }}
                    @if ($barra['esCuello'])
                        <span class="ml-1.5 text-[10px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">frena</span>
                    @endif
                </div>
                <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                    @php
                        $max = max(array_column($capacidad['barras'], 'cantidad')) ?: 1;
                        $pct = $max > 0 ? min(100, ($barra['cantidad'] / $max) * 100) : 0;
                    @endphp
                    <div class="h-full rounded-full transition-all duration-500
                        {{ $barra['esCuello'] ? 'bg-red-500' : 'bg-indigo-500' }}"
                        style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-sm font-semibold tabular-nums text-slate-700 w-10 text-right">{{ $barra['cantidad'] }}</span>
            </div>
        @endforeach
    </div>

    @if ($capacidad['kitsArmables'] === 0 && $capacidad['cuello'] !== 'Sin receta')
        <p class="mt-3 text-xs text-red-600 bg-red-50 rounded-xl px-3 py-2 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            Te frena: <strong>{{ $capacidad['cuello'] }}</strong> ({{ collect($capacidad['barras'])->firstWhere('nombre', $capacidad['cuello'])['cantidad'] ?? 0 }} disp.)
        </p>
    @elseif ($capacidad['kitsArmables'] > 0)
        <p class="mt-3 text-xs text-emerald-600 bg-emerald-50 rounded-xl px-3 py-2 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            Stock suficiente para <strong>{{ $capacidad['kitsArmables'] }} kit{{ $capacidad['kitsArmables'] > 1 ? 's' : '' }}</strong>
        </p>
    @endif
</div>