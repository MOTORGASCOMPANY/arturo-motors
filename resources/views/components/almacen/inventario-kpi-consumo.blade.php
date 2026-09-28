{{-- KPI 2: Tasa de consumo (gauge) --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 transition-all duration-300 hover:shadow-md">

    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
            <i class="fas fa-chart-pie text-emerald-600 text-xl"></i>
        </div>
        <div>
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider">Tasa de consumo</h3>
            <p class="text-slate-400 text-xs mt-0.5">Kits consumidos vs disponibles</p>
        </div>
    </div>

    <div class="flex flex-col items-center mb-4">
        <div class="relative w-36 h-36">
            {{-- SVG Gauge --}}
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                {{-- Background circle --}}
                <circle cx="50" cy="50" r="45" fill="none" stroke="#E2E8F0" stroke-width="8"/>
                {{-- Progress circle --}}
                <circle cx="50" cy="50" r="45" fill="none" stroke="{{ $tasa['porcentaje'] > 80 ? '#DC2626' : ($tasa['porcentaje'] > 50 ? '#F59E0B' : '#1D4ED8') }}"
                    stroke-width="8" stroke-linecap="round"
                    stroke-dasharray="{{ 2 * pi() * 45 }}"
                    stroke-dashoffset="{{ 2 * pi() * 45 * (1 - $tasa['porcentaje'] / 100) }}"
                    class="transition-all duration-700 ease-out"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-3xl font-bold text-slate-900">{{ $tasa['porcentaje'] }}%</span>
            </div>
        </div>
        <p class="mt-2 text-xs text-slate-500">{{ $tasa['sellados'] }} sellados · {{ $tasa['consumidos'] }} consumidos</p>
    </div>

    <div class="grid grid-cols-2 gap-4 text-center">
        <div class="p-3 rounded-xl bg-emerald-50">
            <p class="text-2xl font-bold text-emerald-600">{{ $tasa['sellados'] }}</p>
            <p class="text-xs text-emerald-700">Sellados</p>
        </div>
        <div class="p-3 rounded-xl bg-red-50">
            <p class="text-2xl font-bold text-red-600">{{ $tasa['consumidos'] }}</p>
            <p class="text-xs text-red-700">Consumidos</p>
        </div>
    </div>
</div>