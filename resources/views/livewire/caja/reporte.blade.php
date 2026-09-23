<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">

    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-chart-line text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Reporte de caja</h2>
                    <p class="text-gray-500 text-sm mt-1">Resumen de ingresos y egresos por período</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <input type="date" wire:model.live="desde" class="text-sm rounded-lg border-gray-200 bg-slate-50 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                <span class="text-gray-500 text-sm">a</span>
                <input type="date" wire:model.live="hasta" class="text-sm rounded-lg border-gray-200 bg-slate-50 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                <label class="flex items-center gap-2 ml-2 cursor-pointer select-none">
                    <input type="checkbox" wire:model.live="soloFise" class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                    <span class="text-sm font-medium text-gray-600">Solo FISE</span>
                </label>
                <div class="flex items-center gap-2">
                    <button wire:click="descargarPdf"
                        class="bg-white hover:bg-red-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        PDF
                    </button>
                    <button wire:click="descargarExcel"
                        class="bg-white hover:bg-emerald-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                        <i class="fas fa-file-excel text-emerald-500"></i>
                        Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($sesiones->count() === 0 && $totalIngresos == 0 && $totalEgresos == 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-16 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 font-medium">{{ $soloFise ? 'No hay movimientos FISE' : 'No hay movimientos actuales' }}</p>
            <p class="text-gray-400 text-sm mt-1">{{ $soloFise ? 'No se encontraron movimientos FISE en el período seleccionado.' : 'No se encontraron ingresos, egresos ni sesiones en el período seleccionado.' }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
                <span class="text-xs font-bold text-gray-500 uppercase">{{ $soloFise ? 'FISE del día anterior' : 'Efectivo del día anterior' }}</span>
                <p class="text-2xl font-bold text-amber-600 mt-1">S/ {{ number_format($efectivoAnterior ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
                <span class="text-xs font-bold text-gray-500 uppercase">{{ $soloFise ? 'Ingresos FISE' : 'Ingresos' }}</span>
                <p class="text-2xl font-bold text-emerald-600 mt-1">S/ {{ number_format($totalIngresos, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
                <span class="text-xs font-bold text-gray-500 uppercase">{{ $soloFise ? 'Egresos FISE' : 'Egresos' }}</span>
                <p class="text-2xl font-bold text-red-600 mt-1">S/ {{ number_format($totalEgresos, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border {{ $neto >= 0 ? 'border-emerald-200' : 'border-red-200' }} p-5 text-center">
                <span class="text-xs font-bold text-gray-500 uppercase">Flujo Neto</span>
                <p class="text-2xl font-bold {{ $neto >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1">S/ {{ number_format($neto, 2) }}</p>
            </div>
        </div>
    @endif

    {{-- Charts: 3 filas — 1-2 / 3-6 / 4-5 --}}
    <script type="application/json" id="cajaReportPayload">@json($charts)</script>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- 1. Ingresos vs egresos (barras agrupadas) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="caja-c1">
            <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">
                <i class="fas fa-exchange-alt mr-1.5 text-emerald-500"></i>Ingresos vs. egresos por día
            </h3>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="chartIngresosEgresos"></canvas>
            </div>
            <div id="emptyIE" class="hidden flex flex-col items-center justify-center py-10 text-center">
                <i class="fas fa-chart-bar text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400 font-medium">No hay datos para este gráfico</p>
            </div>
            <div class="flex flex-wrap gap-4 mt-3 justify-center text-xs font-semibold text-gray-600">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-500"></span> Ingresos</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-500"></span> Egresos</span>
            </div>
        </div>

        {{-- 2. Flujo neto acumulado (línea) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="caja-c2">
            <div class="flex items-start justify-between gap-2 mb-4">
                <h3 class="text-sm font-bold text-gray-500 uppercase">
                    <i class="fas fa-chart-area mr-1.5 text-indigo-500"></i>Flujo neto acumulado
                </h3>
                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-1">
                    Inicia S/ {{ number_format($efectivoAnterior, 2) }}
                </span>
            </div>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="chartFlujoAcum"></canvas>
            </div>
            <div id="emptyFlujo" class="hidden flex flex-col items-center justify-center py-10 text-center">
                <i class="fas fa-chart-area text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400 font-medium">No hay datos para este gráfico</p>
            </div>
            <p class="text-center text-xs text-gray-400 mt-2">Saldo de caja al cierre de cada día</p>
        </div>

        {{-- 3. Distribución por método de pago (dona) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="caja-c3">
            <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">
                <i class="fas fa-credit-card mr-1.5 text-violet-500"></i>Distribución por método de pago
            </h3>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="chartMetodosPago"></canvas>
            </div>
            <div id="emptyMetodos" class="hidden flex flex-col items-center justify-center py-10 text-center">
                <i class="fas fa-credit-card text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400 font-medium">No hay datos para este gráfico</p>
                <p class="text-xs text-gray-300 mt-1">Sin ingresos por método en el período</p>
            </div>
        </div>

        {{-- 6. FISE vs no FISE por día --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="caja-c6">
            <div class="flex items-start justify-between gap-2 mb-4">
                <h3 class="text-sm font-bold text-gray-500 uppercase">
                    <i class="fas fa-hand-holding-usd mr-1.5 text-amber-500"></i>Ingresos FISE vs. no FISE
                </h3>
                <span class="text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-1">
                    FISE S/ {{ number_format($fiseTotal, 2) }} · Otros S/ {{ number_format($noFiseTotal, 2) }}
                </span>
            </div>
            <div class="relative w-full" style="height: 260px;">
                <canvas id="chartFiseNoFise"></canvas>
            </div>
            <div id="emptyFise" class="hidden flex flex-col items-center justify-center py-10 text-center">
                <i class="fas fa-chart-bar text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400 font-medium">No hay datos para este gráfico</p>
            </div>
            <div class="flex flex-wrap gap-4 mt-3 justify-center text-xs font-semibold text-gray-600">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-500"></span> FISE</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-500"></span> No FISE</span>
            </div>
        </div>

        {{-- 4. Egresos por categoría (barras horizontales) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="caja-c4">
            <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">
                <i class="fas fa-file-invoice-dollar mr-1.5 text-red-500"></i>Egresos por categoría
            </h3>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="chartEgresosCat"></canvas>
            </div>
            <div id="emptyEgresosCat" class="hidden flex flex-col items-center justify-center py-10 text-center">
                <i class="fas fa-file-invoice-dollar text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400 font-medium">No hay datos para este gráfico</p>
                <p class="text-xs text-gray-300 mt-1">Sin egresos en el período seleccionado</p>
            </div>
        </div>

        {{-- 5. Ingresos por hora del día --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="caja-c5">
            <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">
                <i class="fas fa-clock mr-1.5 text-sky-500"></i>Ingresos por hora del día
            </h3>
            <div class="relative w-full" style="height: 280px;">
                <canvas id="chartIngresosHora"></canvas>
            </div>
            <div id="emptyHora" class="hidden flex flex-col items-center justify-center py-10 text-center">
                <i class="fas fa-clock text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400 font-medium">No hay datos para este gráfico</p>
            </div>
        </div>
    </div>

    {{-- Extra: Top 5 ingresos --}}
    @if ($topIngresos->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">
            <i class="fas fa-trophy mr-1.5 text-amber-500"></i>Top 5 ingresos del período
        </h3>
        <div class="space-y-2">
            @foreach ($topIngresos as $i => $mov)
            <div class="flex items-center justify-between gap-3 bg-gray-50 rounded-lg px-4 py-3 border border-gray-100">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-7 h-7 rounded-full bg-indigo-50 text-indigo-700 text-xs font-extrabold flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $mov->concepto ?: 'Ingreso' }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $mov->created_at?->format('d/m/Y H:i') }}
                            @if($mov->usuario) · {{ $mov->usuario->name }} @endif
                            @if($mov->metodo_pago) · {{ ucfirst($mov->metodo_pago) }} @endif
                        </p>
                    </div>
                </div>
                <span class="text-sm font-extrabold text-emerald-600 shrink-0">S/ {{ number_format($mov->monto, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if ($sesionesConDescuadre->count())
        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <h3 class="text-sm font-bold text-red-700 uppercase mb-3">
                <i class="fas fa-triangle-exclamation mr-1"></i> Sesiones con descuadre ({{ $sesionesConDescuadre->count() }})
            </h3>
            <div class="space-y-1">
                @foreach ($sesionesConDescuadre as $s)
                    <div class="flex justify-between text-sm bg-white rounded-lg px-3 py-2">
                        <span>{{ $s->abierta_en ? $s->abierta_en->format('d/m/Y') : '—' }} — {{ $s->abiertaPor->name }}</span>
                        <span class="font-semibold text-red-600">S/ {{ number_format($s->diferencia, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($sesiones->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="p-6 border-b border-gray-200/60">
                <h3 class="font-semibold text-gray-800">{{ $soloFise ? 'Sesiones con FISE' : 'Sesiones del período' }} ({{ $sesiones->count() }})</h3>
            </div>
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                <table class="w-full text-sm min-w-[700px]">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left whitespace-nowrap">Fecha</th>
                            <th class="px-4 py-3 text-left whitespace-nowrap">Cajero</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Apertura</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Cierre</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Diferencia</th>
                            <th class="px-4 py-3 text-center whitespace-nowrap">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($sesiones as $s)
                            <tr wire:key="sesion-reporte-{{ $s->id }}">
                                <td class="px-4 py-3 whitespace-nowrap">{{ $s->abierta_en ? $s->abierta_en->format('d/m/Y H:i') : '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $s->abiertaPor->name }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">S/ {{ number_format($s->monto_apertura, 2) }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">{{ $s->monto_cierre !== null ? 'S/ '.number_format($s->monto_cierre, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-right font-semibold whitespace-nowrap {{ $s->diferencia === null ? 'text-gray-400' : ($s->diferencia == 0 ? 'text-emerald-600' : 'text-red-600') }}">
                                    {{ $s->diferencia !== null ? 'S/ '.number_format($s->diferencia, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $s->estado === 'abierta' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($s->estado) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No hay sesiones en este período.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Egresos por concepto --}}
    @if($egresosPorConcepto->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4"><i class="fas fa-receipt mr-1.5 text-gray-400"></i>Egresos por concepto</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2.5 px-3 font-semibold text-gray-600">Concepto</th>
                        <th class="text-center py-2.5 px-3 font-semibold text-gray-600">Cant.</th>
                        <th class="text-right py-2.5 px-3 font-semibold text-gray-600">Total</th>
                        <th class="text-right py-2.5 px-3 font-semibold text-gray-600">% del total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($egresosPorConcepto as $eg)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2.5 px-3 font-medium text-gray-700">{{ $eg->concepto ?: 'Sin concepto' }}</td>
                        <td class="py-2.5 px-3 text-center text-gray-500">{{ $eg->cantidad }}</td>
                        <td class="py-2.5 px-3 text-right font-semibold text-red-600">S/ {{ number_format($eg->total, 2) }}</td>
                        <td class="py-2.5 px-3 text-right text-gray-500">{{ $totalEgresos > 0 ? round(($eg->total / $totalEgresos) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold text-gray-700 border-t-2 border-gray-200">
                        <td class="py-2.5 px-3">Total egresos</td>
                        <td class="py-2.5 px-3 text-center">{{ $egresosPorConcepto->sum('cantidad') }}</td>
                        <td class="py-2.5 px-3 text-right text-red-600">S/ {{ number_format($totalEgresos, 2) }}</td>
                        <td class="py-2.5 px-3 text-right">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    @script
    <script>
        function cajaMoney(v) {
            return 'S/ ' + Number(v || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function cajaDestroy(key) {
            if (window[key]) { window[key].destroy(); window[key] = null; }
        }

        function cajaReadPayload() {
            const el = document.getElementById('cajaReportPayload');
            if (!el) return null;
            try { return JSON.parse(el.textContent || 'null'); } catch (e) { return null; }
        }

        function cajaToggle(idCanvas, idEmpty, showEmpty) {
            const canvas = document.getElementById(idCanvas);
            const empty = document.getElementById(idEmpty);
            const wrap = canvas ? canvas.parentElement : null;
            if (wrap) wrap.classList.toggle('hidden', !!showEmpty);
            if (empty) empty.classList.toggle('hidden', !showEmpty);
            if (empty) empty.classList.toggle('flex', !!showEmpty);
        }

        window.renderReporteCajaCharts = function () {
            if (typeof Chart === 'undefined') return;
            const d = cajaReadPayload();
            if (!d || !d.labels) return;

            const moneyScale = {
                beginAtZero: true,
                ticks: { callback: v => 'S/ ' + v.toLocaleString(), color: '#94a3b8', font: { size: 10 } },
                grid: { color: '#f1f5f9' },
                border: { display: false }
            };
            const xTicks = { ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 45, autoSkip: true }, grid: { display: false }, border: { display: false } };
            const legendBottom = {
                display: true,
                position: 'bottom',
                labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } }
            };

            // 1. Ingresos vs egresos
            try {
                cajaDestroy('chartIE');
                const hasIE = (d.ingresosData || []).some(v => Number(v) > 0) || (d.egresosData || []).some(v => Number(v) > 0);
                cajaToggle('chartIngresosEgresos', 'emptyIE', !hasIE);
                const cIE = document.getElementById('chartIngresosEgresos');
                if (cIE && hasIE) {
                    window.chartIE = new Chart(cIE, {
                        type: 'bar',
                        data: {
                            labels: d.labels,
                            datasets: [
                                { label: 'Ingresos', data: d.ingresosData, backgroundColor: '#10b981', borderRadius: 4, barPercentage: 0.85, categoryPercentage: 0.7 },
                                { label: 'Egresos', data: d.egresosData, backgroundColor: '#ef4444', borderRadius: 4, barPercentage: 0.85, categoryPercentage: 0.7 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false, animation: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + cajaMoney(ctx.parsed.y) } }
                            },
                            scales: { x: xTicks, y: moneyScale }
                        }
                    });
                }
            } catch (e) { console.error('[caja] chart1', e); }

            // 2. Flujo acumulado
            try {
                cajaDestroy('chartFlujo');
                const hasFlujo = (d.flujoAcumulado || []).some(v => Number(v) !== 0);
                cajaToggle('chartFlujoAcum', 'emptyFlujo', !hasFlujo && !d.flujoAcumulado?.length);
                const cF = document.getElementById('chartFlujoAcum');
                if (cF && d.flujoAcumulado && d.flujoAcumulado.length) {
                    window.chartFlujo = new Chart(cF, {
                        type: 'line',
                        data: {
                            labels: d.labels,
                            datasets: [{
                                label: 'Saldo acumulado',
                                data: d.flujoAcumulado,
                                borderColor: '#4f46e5',
                                backgroundColor: 'rgba(79, 70, 229, 0.12)',
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.35,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#4f46e5',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false, animation: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: ctx => 'Saldo: ' + cajaMoney(ctx.parsed.y) } }
                            },
                            scales: { x: xTicks, y: moneyScale }
                        }
                    });
                }
            } catch (e) { console.error('[caja] chart2', e); }

            // 3. Métodos de pago (dona)
            try {
                cajaDestroy('chartMetodos');
                const totals = d.metodosTotales || {};
                const metodos = (d.metodos || []).filter(m => Number(totals[m]) > 0);
                const labelsM = metodos.map(m => (d.metodosLabels && d.metodosLabels[m]) || m);
                const dataM = metodos.map(m => Number(totals[m]) || 0);
                const hasMetodos = dataM.length > 0;
                cajaToggle('chartMetodosPago', 'emptyMetodos', !hasMetodos);
                const cM = document.getElementById('chartMetodosPago');
                if (cM && hasMetodos) {
                    window.chartMetodos = new Chart(cM, {
                        type: 'doughnut',
                        data: {
                            labels: labelsM,
                            datasets: [{
                                data: dataM,
                                backgroundColor: metodos.map(m => (d.colores && d.colores[m]) || '#6b7280'),
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false, animation: false, cutout: '58%',
                            plugins: {
                                legend: legendBottom,
                                tooltip: { callbacks: { label: ctx => ctx.label + ': ' + cajaMoney(ctx.parsed) } }
                            }
                        }
                    });
                }
            } catch (e) { console.error('[caja] chart3', e); }

            // 6. FISE vs no FISE
            try {
                cajaDestroy('chartFise');
                const hasFise = (d.fiseDiaData || []).some(v => Number(v) > 0) || (d.noFiseDiaData || []).some(v => Number(v) > 0);
                cajaToggle('chartFiseNoFise', 'emptyFise', !hasFise);
                const cFi = document.getElementById('chartFiseNoFise');
                if (cFi && hasFise) {
                    window.chartFise = new Chart(cFi, {
                        type: 'bar',
                        data: {
                            labels: d.labels,
                            datasets: [
                                { label: 'FISE', data: d.fiseDiaData, backgroundColor: '#f59e0b', borderRadius: 3, barPercentage: 0.9, categoryPercentage: 0.75 },
                                { label: 'No FISE', data: d.noFiseDiaData, backgroundColor: '#10b981', borderRadius: 3, barPercentage: 0.9, categoryPercentage: 0.75 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false, animation: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + cajaMoney(ctx.parsed.y) } }
                            },
                            scales: { x: { ...xTicks, stacked: true }, y: { ...moneyScale, stacked: true } }
                        }
                    });
                }
            } catch (e) { console.error('[caja] chart6', e); }

            // 4. Egresos por categoría
            try {
                cajaDestroy('chartEgCat');
                const hasCat = (d.egresosLabels || []).length > 0 && (d.egresosDataCat || []).some(v => Number(v) > 0);
                cajaToggle('chartEgresosCat', 'emptyEgresosCat', !hasCat);
                const cE = document.getElementById('chartEgresosCat');
                if (cE && hasCat) {
                    window.chartEgCat = new Chart(cE, {
                        type: 'bar',
                        data: {
                            labels: d.egresosLabels,
                            datasets: [{
                                label: 'Egresos',
                                data: d.egresosDataCat,
                                backgroundColor: '#ef4444',
                                borderRadius: 6,
                                maxBarThickness: 28
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false, animation: false, indexAxis: 'y',
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: ctx => cajaMoney(ctx.parsed.x) } }
                            },
                            scales: {
                                x: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v.toLocaleString(), color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { display: false } },
                                y: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { display: false }, border: { display: false } }
                            }
                        }
                    });
                }
            } catch (e) { console.error('[caja] chart4', e); }

            // 5. Ingresos por hora
            try {
                cajaDestroy('chartHora');
                const hasHora = (d.ingresosPorHoraData || []).some(v => Number(v) > 0);
                cajaToggle('chartIngresosHora', 'emptyHora', !hasHora);
                const cH = document.getElementById('chartIngresosHora');
                if (cH && hasHora) {
                    window.chartHora = new Chart(cH, {
                        type: 'bar',
                        data: {
                            labels: d.labelsHora,
                            datasets: [{
                                label: 'Ingresos',
                                data: d.ingresosPorHoraData,
                                backgroundColor: '#0ea5e9',
                                borderRadius: 4,
                                barPercentage: 0.9
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false, animation: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { title: items => (items[0]?.label || '') + ':00', label: ctx => cajaMoney(ctx.parsed.y) } }
                            },
                            scales: { x: { ...xTicks, ticks: { ...xTicks.ticks, maxRotation: 0, autoSkip: false, font: { size: 9 } } }, y: moneyScale }
                        }
                    });
                }
            } catch (e) { console.error('[caja] chart5', e); }
        };

        window.renderReporteCajaCharts();

        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'caja.reporte') window.renderReporteCajaCharts();
        });

        $wire.on('chart-data-updated', (payload) => {
            const el = document.getElementById('cajaReportPayload');
            if (el && payload && payload.charts) {
                el.textContent = JSON.stringify(payload.charts);
            }
            window.renderReporteCajaCharts();
        });

        $wire.on('descargar-pdf', (params) => {
            const url = params.url;
            if (!url || url === '#') {
                Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar en este período.', icon: 'warning', timer: 3000, showConfirmButton: false });
                return;
            }
            Swal.fire({
                title: 'Exportando PDF',
                text: 'Generando el reporte, por favor espera...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = url;
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({ title: 'Descarga iniciada', text: 'El archivo PDF se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false });
                    }, 3000);
                }
            });
        });

        $wire.on('descargar-excel', (params) => {
            const url = params.url;
            if (!url || url === '#') {
                Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar en este período.', icon: 'warning', timer: 3000, showConfirmButton: false });
                return;
            }
            Swal.fire({
                title: 'Exportando Excel',
                text: 'Generando el reporte, por favor espera...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = url;
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({ title: 'Descarga iniciada', text: 'El archivo Excel se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false });
                    }, 3000);
                }
            });
        });
    </script>
    @endscript
</div>