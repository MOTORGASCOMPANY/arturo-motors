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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Efectivo del día anterior</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">S/ {{ number_format($efectivoAnterior ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Ingresos</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">S/ {{ number_format($totalIngresos, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Egresos</span>
            <p class="text-2xl font-bold text-red-600 mt-1">S/ {{ number_format($totalEgresos, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="reporte-caja-chart">
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Ingresos vs. egresos por día</h3>
        <canvas id="chartReporteCaja" height="90"></canvas>
    </div>

    @script
    <script>
        window.renderReporteCajaChart = function (labels, ingresos, egresos) {
            const ctx = document.getElementById('chartReporteCaja');
            if (!ctx) return;

            if (window.chartReporteCajaInstance) window.chartReporteCajaInstance.destroy();

            window.chartReporteCajaInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Ingresos', data: ingresos, backgroundColor: '#059669' },
                        { label: 'Egresos', data: egresos, backgroundColor: '#dc2626' }
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });
        };

        window.renderReporteCajaChart(@json($labels), @json($ingresosData), @json($egresosData));

        $wire.on('actualizar-reporte-caja', (data) => {
            window.renderReporteCajaChart(data.labels, data.ingresos, data.egresos);
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="p-6 border-b border-gray-200/60">
            <h3 class="font-semibold text-gray-800">Sesiones del período ({{ $sesiones->count() }})</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Cajero</th>
                    <th class="px-4 py-3 text-right">Apertura</th>
                    <th class="px-4 py-3 text-right">Cierre</th>
                    <th class="px-4 py-3 text-right">Diferencia</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($sesiones as $s)
                    <tr wire:key="sesion-reporte-{{ $s->id }}">
                        <td class="px-4 py-3">{{ $s->abierta_en ? $s->abierta_en->format('d/m/Y H:i') : '—' }}</td>
                        <td class="px-4 py-3">{{ $s->abiertaPor->name }}</td>
                        <td class="px-4 py-3 text-right">S/ {{ number_format($s->monto_apertura, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $s->monto_cierre !== null ? 'S/ '.number_format($s->monto_cierre, 2) : '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $s->diferencia === null ? 'text-gray-400' : ($s->diferencia == 0 ? 'text-emerald-600' : 'text-red-600') }}">
                            {{ $s->diferencia !== null ? 'S/ '.number_format($s->diferencia, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
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