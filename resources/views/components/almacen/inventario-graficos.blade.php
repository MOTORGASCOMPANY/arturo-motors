    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-chart-simple text-slate-400"></i>
            Gráficos
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- 1. Stock por sede --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-column text-slate-400"></i>
                        Stock por sede
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartStockSedes"></canvas>
                    <div id="emptySedes" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 2. Stock por categoría --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-pie text-slate-400"></i>
                        Stock por categoría
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartStockCategorias"></canvas>
                    <div id="emptyCategorias" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 3. Top productos --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-ranking-star text-slate-400"></i>
                        Top productos
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartTopProductos"></canvas>
                    <div id="emptyTop" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 4. Nivel de stock --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-gauge text-slate-400"></i>
                        Nivel de stock
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }} · OK {{ $nivelOk }} / Bajo {{ $nivelBajo }} / Sin {{ $nivelSin }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartNivelStock"></canvas>
                    <div id="emptyNivel" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 5. Estados de kits --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-box-open text-slate-400"></i>
                        Estados de kits
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartKitsEstado"></canvas>
                    <div id="emptyKitsEstado" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 6. Entradas vs salidas 30 días --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-arrow-trend-up text-slate-400"></i>
                        Entradas vs salidas · 30 días
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                        <span class="text-slate-400 font-normal">·</span>
                        <span class="text-emerald-600">+{{ number_format($totalEntradas30) }}</span>
                        <span class="text-slate-400 font-normal">/</span>
                        <span class="text-red-500">-{{ number_format($totalSalidas30) }}</span>
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartMovDias"></canvas>
                    <div id="emptyMovDias" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin movimientos en el período
                    </div>
                </div>
            </div>
        </div>
    </div>
