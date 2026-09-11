<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">

    <div class="bg-gray-200 p-8 rounded-xl w-full">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-chart-line mr-2"></i>Reporte de caja
                </h2>
                <span class="text-xs">Resumen de ingresos y egresos por período</span>
            </div>
            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="desde" class="text-sm rounded-lg border-gray-300">
                <span class="text-gray-500 text-sm">a</span>
                <input type="date" wire:model.live="hasta" class="text-sm rounded-lg border-gray-300">
                <label class="flex items-center gap-2 ml-2 cursor-pointer select-none">
                    <input type="checkbox" wire:model.live="soloFise" class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                    <span class="text-sm font-medium text-gray-600">Solo FISE</span>
                </label>
                <div class="flex items-center gap-2 mt-2">
                    <button wire:click="descargarPdf"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-full py-2 px-4 shadow-sm transition-colors flex items-center gap-1.5">
                        <i class="fas fa-file-pdf mr-1"></i>
                        PDF
                    </button>
                    <button wire:click="descargarExcel"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-full py-2 px-4 shadow-sm transition-colors flex items-center gap-1.5">
                        <i class="fas fa-file-excel mr-1"></i>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
        </div>
    @endif

    {{-- Chart: SIEMPRE en el DOM (fuera del condicional) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="reporte-caja-chart">
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">{{ $soloFise ? 'FISE por día' : 'Ingresos por día (desglose por método de pago)' }}</h3>
        <div class="relative w-full" style="height: 320px;">
            <canvas id="chartReporteCaja"
                data-labels='@json($labels)'
                data-chartdata='@json($chartData)'
                data-colores='@json($colores)'></canvas>
        </div>
        <div class="flex flex-wrap gap-4 mt-3 justify-center">
            @foreach ($metodos as $metodo)
                @php $total = $ingresosPorMetodo[$metodo] ?? 0; @endphp
                @if ($total > 0)
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-3 h-3 rounded-sm" style="background: {{ $colores[$metodo] }}"></span>
                        <span class="font-medium text-gray-600">{{ ucfirst($metodo) }}</span>
                        <span class="text-gray-400">S/ {{ number_format($total, 2) }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

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

    @script
    <script>
        const METODOS_LABELS = {
            efectivo: 'Efectivo',
            tarjeta: 'Tarjeta',
            transferencia: 'Transferencia',
            fise: 'FISE',
            otro: 'Otro'
        };

        window.renderReporteCajaChart = function () {
            const canvas = document.getElementById('chartReporteCaja');
            if (!canvas) {
                console.error('[reporte-caja] No se encontró el canvas #chartReporteCaja');
                return;
            }

            if (typeof Chart === 'undefined') {
                console.error('[reporte-caja] Chart.js no está cargado en esta página. Verifica que el script de Chart.js se incluya antes de este componente.');
                return;
            }

            let labels, chartData, colores;
            try {
                labels = JSON.parse(canvas.dataset.labels || '[]');
                chartData = JSON.parse(canvas.dataset.chartdata || '{}');
                colores = JSON.parse(canvas.dataset.colores || '{}');
            } catch (e) {
                console.error('[reporte-caja] Error parseando data-* del canvas:', e, {
                    labels: canvas.dataset.labels,
                    chartdata: canvas.dataset.chartdata,
                    colores: canvas.dataset.colores,
                });
                return;
            }

            console.log('[reporte-caja] labels:', labels, 'chartData:', chartData, 'colores:', colores);

            if (window.chartReporteCajaInstance) window.chartReporteCajaInstance.destroy();

            const datasets = [];
            for (const [metodo, data] of Object.entries(chartData)) {
                const total = data.reduce((a, b) => a + b, 0);
                if (total > 0) {
                    datasets.push({
                        label: METODOS_LABELS[metodo] || metodo,
                        data: data,
                        backgroundColor: colores[metodo] || '#6b7280',
                        borderRadius: 3,
                        borderSkipped: false,
                    });
                }
            }

            if (datasets.length === 0) {
                console.warn('[reporte-caja] No hay datasets con datos > 0, no se dibuja el gráfico.');
                return;
            }

            window.chartReporteCajaInstance = new Chart(canvas, {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, pointStyle: 'rectRounded' } },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ctx.dataset.label + ': S/ ' + ctx.parsed.y.toLocaleString('es-PE', { minimumFractionDigits: 2 })
                            }
                        }
                    },
                    scales: {
                        x: { stacked: true },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: { callback: v => 'S/ ' + v.toLocaleString() }
                        }
                    }
                }
            });
        };

        window.renderReporteCajaChart();

        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'caja.reporte') {
                window.renderReporteCajaChart();
            }
        });

        // Update canvas data-* attrs when server dispatches new chart data
        // (wire:ignore prevents morph from updating them)
        $wire.on('chart-data-updated', (data) => {
            const canvas = document.getElementById('chartReporteCaja');
            if (!canvas) return;
            canvas.dataset.labels = JSON.stringify(data.labels);
            canvas.dataset.chartdata = JSON.stringify(data.chartData);
            canvas.dataset.colores = JSON.stringify(data.colores);
            window.renderReporteCajaChart();
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