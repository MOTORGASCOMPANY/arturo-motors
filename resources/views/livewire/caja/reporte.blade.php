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
        <x-ui.chart
            id="chartIngresosEgresos"
            title="Ingresos vs. egresos por día"
            icon="fa-exchange-alt"
            iconColor="text-emerald-500"
            emptyId="emptyIE"
            height="280px"
            :legend="['Ingresos' => '#10b981', 'Egresos' => '#ef4444']"
            emptyMessage="Sin ingresos ni egresos en el período seleccionado"
            emptyIcon="fa-exchange-alt"
        />

        {{-- 2. Ticket promedio por día (reemplaza al flujo acumulado) --}}
        <x-ui.chart
            id="chartTicketDia"
            title="Ticket promedio por día"
            icon="fa-ticket-alt"
            iconColor="text-indigo-500"
            emptyId="emptyTicket"
            height="280px"
            :subtitle="$operacionesPeriodo > 0 ? 'Promedio S/ ' . number_format($ticketPeriodo, 2) : 'Sin operaciones'"
            emptyMessage="Sin operaciones en el período seleccionado"
            emptyIcon="fa-ticket-alt"
        >
            <p class="text-center text-xs text-gray-400">Ingresos ÷ operaciones de cada día del período</p>
        </x-ui.chart>
        {{-- Nota: descripcion adicional se mantiene en JS --}}

        {{-- 3. Distribución por método de pago (dona) --}}
        <x-ui.chart
            id="chartMetodosPago"
            title="Distribución por método de pago"
            icon="fa-credit-card"
            iconColor="text-violet-500"
            emptyId="emptyMetodos"
            height="280px"
            emptyMessage="Sin ingresos por método en el período"
            emptyIcon="fa-credit-card"
        />

        {{-- 6. FISE vs no FISE por día --}}
        <x-ui.chart
            id="chartFiseNoFise"
            title="Ingresos FISE vs. no FISE"
            icon="fa-hand-holding-usd"
            iconColor="text-amber-500"
            emptyId="emptyFise"
            height="260px"
            :subtitle="'FISE S/ ' . number_format($fiseTotal, 2) . ' · Otros S/ ' . number_format($noFiseTotal, 2)"
            :legend="['FISE' => '#f59e0b', 'No FISE' => '#10b981']"
            emptyMessage="Sin ingresos FISE ni de otros métodos en el período"
            emptyIcon="fa-hand-holding-usd"
        />

        {{-- 4. Egresos por categoría (barras horizontales) --}}
        <x-ui.chart
            id="chartEgresosCat"
            title="Egresos"
            icon="fa-file-invoice-dollar"
            iconColor="text-red-500"
            emptyId="emptyEgresosCat"
            height="280px"
            emptyMessage="Sin egresos en el período seleccionado"
            emptyIcon="fa-file-invoice-dollar"
        />

        {{-- 5. Ingresos de la semana (Lun–Sáb, se corta en el día actual) --}}
        <x-ui.chart
            id="chartIngresosSemana"
            title="Ingresos de la semana"
            icon="fa-calendar-day"
            iconColor="text-sky-500"
            emptyId="emptySemana"
            height="280px"
            emptyMessage="Aún no hay ingresos esta semana"
            emptyIcon="fa-calendar-day"
        />
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

        function cajaReadPayload() {
            const el = document.getElementById('cajaReportPayload');
            if (!el) return null;
            try { return JSON.parse(el.textContent || 'null'); } catch (e) { return null; }
        }

        function cajaDestroy(key) {
            if (window[key]) { window[key].destroy(); window[key] = null; }
        }

        window.renderReporteCajaCharts = function () {
            if (!window.CHART_DEFS) return;
            const d = cajaReadPayload();
            if (!d || !d.labels) return;

            // 1. Ingresos vs Egresos (barras apiladas)
            try {
                cajaDestroy('chartIE');
                const hasIE = (d.ingresosData || []).some(v => Number(v) > 0) || (d.egresosData || []).some(v => Number(v) > 0);
                if (hasIE) {
                    window.CHART_DEFS.renderCajaIngresosEgresos('chartIngresosEgresos', 'chartIE', d.labels, d.ingresosData, d.egresosData);
                }
            } catch (e) { console.error('[caja] chart1', e); }

            // 2. Ticket promedio por día (scatter)
            try {
                cajaDestroy('chartTicket');
                const ticketDia = d.ticketDiaData || [];
                const opsDia = d.operacionesDia || [];
                const hasTicket = ticketDia.some(v => v !== null && v !== undefined);
                if (hasTicket) {
                    window.CHART_DEFS.renderCajaTicketDia('chartTicketDia', 'chartTicket', d.labels, ticketDia, opsDia);
                }
            } catch (e) { console.error('[caja] chart2', e); }

            // 3. Métodos de pago (dona)
            try {
                cajaDestroy('chartMetodos');
                const totals = d.metodosTotales || {};
                const metodos = (d.metodos || []).filter(m => Number(totals[m]) > 0);
                const labelsM = metodos.map(m => (d.metodosLabels && d.metodosLabels[m]) || m);
                const dataM = metodos.map(m => Number(totals[m]) || 0);
                const colorsM = metodos.map(m => (d.colores && d.colores[m]) || '#6b7280');
                const hasMetodos = dataM.length > 0;
                if (hasMetodos) {
                    window.CHART_DEFS.renderCajaMetodosPago('chartMetodosPago', 'chartMetodos', labelsM, dataM, colorsM);
                }
            } catch (e) { console.error('[caja] chart3', e); }

            // 6. FISE vs no FISE (barras apiladas)
            try {
                cajaDestroy('chartFise');
                const hasFise = (d.fiseDiaData || []).some(v => Number(v) > 0) || (d.noFiseDiaData || []).some(v => Number(v) > 0);
                if (hasFise) {
                    window.CHART_DEFS.renderCajaFiseNoFise('chartFiseNoFise', 'chartFise', d.labels, d.fiseDiaData, d.noFiseDiaData);
                }
            } catch (e) { console.error('[caja] chart6', e); }

            // 4. Egresos por categoría (barras horizontales)
            try {
                cajaDestroy('chartEgCat');
                const hasCat = (d.egresosLabels || []).length > 0 && (d.egresosDataCat || []).some(v => Number(v) > 0);
                if (hasCat) {
                    window.CHART_DEFS.renderCajaEgresosCat('chartEgresosCat', 'chartEgCat', d.egresosLabels, d.egresosDataCat);
                }
            } catch (e) { console.error('[caja] chart4', e); }

            // 5. Ingresos de la semana (line con spanGaps)
            try {
                cajaDestroy('chartSemana');
                const hasSemana = (d.ingresosSemanaData || []).some(v => v !== null && v !== undefined && Number(v) > 0);
                if (hasSemana) {
                    window.CHART_DEFS.renderCajaIngresosSemana('chartIngresosSemana', 'chartSemana', d.labelsSemana, d.ingresosSemanaData);
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

        // Alertas de exportación.
        $wire.on('descargar-pdf', (params) => {
            AppSwal.exportar({
                url: params.url,
                titulo: 'Exportando PDF',
                texto: 'Generando el reporte, por favor espera...',
                archivo: 'PDF'
            });
        });

        $wire.on('descargar-excel', (params) => {
            AppSwal.exportar({
                url: params.url,
                titulo: 'Exportando Excel',
                texto: 'Generando el reporte, por favor espera...',
                archivo: 'Excel'
            });
        });
    </script>
    @endscript
</div>