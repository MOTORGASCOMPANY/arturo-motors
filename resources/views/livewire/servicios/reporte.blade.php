<div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Cabecera: identificación + exportación arriba, filtros debajo (mismo patrón que citas) --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col gap-6">

            {{-- Fila 1: identidad + exportación --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4 shrink-0">
                    <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-chart-simple text-indigo-600 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Reporte de servicios</h2>
                        <p class="text-gray-500 text-sm mt-1">Conversiones, servicios simples y tendencia del período</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="descargarPdf" wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 bg-white hover:bg-red-50 border border-gray-200 text-gray-700 font-semibold text-sm rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        PDF
                    </button>
                    <button wire:click="descargarExcel" wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 bg-white hover:bg-emerald-50 border border-gray-200 text-gray-700 font-semibold text-sm rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-file-excel text-emerald-600"></i>
                        Excel
                    </button>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            {{-- Fila 2: filtros con etiqueta, una columna por campo --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="rep-desde" class="text-xs font-semibold text-gray-500">Desde</label>
                    <input id="rep-desde" type="date" wire:model.live="desde"
                        class="w-full text-sm text-gray-700 rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="rep-hasta" class="text-xs font-semibold text-gray-500">Hasta</label>
                    <input id="rep-hasta" type="date" wire:model.live="hasta"
                        class="w-full text-sm text-gray-700 rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="rep-tipo" class="text-xs font-semibold text-gray-500">Servicio</label>
                    <select id="rep-tipo" wire:model.live="tipoServicio"
                        class="w-full text-sm text-gray-700 rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors cursor-pointer">
                        <option value="todos">Todos los servicios</option>
                        <option value="simple">Solo simples</option>
                        <option value="conversion">Solo conversión</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total ventas</span>
                <i class="fas fa-sack-dollar text-indigo-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-indigo-600">S/ {{ number_format($totalVentas, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Órdenes</span>
                <i class="fas fa-file-invoice text-gray-200 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-700">{{ $totalOrdenes }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ticket promedio</span>
                <i class="fas fa-receipt text-gray-200 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-700">S/ {{ number_format($ticketPromedio, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Descuentos</span>
                <i class="fas fa-tags text-red-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-red-500">S/ {{ number_format($totalDescuentos, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-amber-500">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Conv. pendientes</span>
                <i class="fas fa-clock text-amber-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-amber-600">{{ $totalConversionesPendientes }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-emerald-500">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Conv. completadas</span>
                <i class="fas fa-check-circle text-emerald-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-emerald-600">{{ $totalConversionesCompletadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-gray-400">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Simples completados</span>
                <i class="fas fa-wrench text-gray-200 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-700">{{ $totalSimplesCompletadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">T. Prom. Conversión</span>
                <i class="fas fa-stopwatch text-amber-100 text-lg"></i>
            </div>
                <p class="text-3xl font-extrabold text-amber-600">{{ $tiempoPromedioTexto }}</p>
        </div>
    </div>

    {{-- Gráfico de servicios por día --}}
    <x-ui.chart
        id="chartReporteServicios"
        title="Órdenes por semana"
        icon="fa-chart-column"
        iconColor="text-gray-400"
        height="360px"
        subtitle="Agrupadas en bloques de 7 días"
        emptyMessage="Sin órdenes en el período seleccionado"
        emptyIcon="fa-chart-column"
        :legend="[
            'Conversión pendiente' => '#d97706',
            'Conversión completada' => '#059669',
            'Simple completado' => '#6b7280',
        ]"
    />

    {{-- Layout de 2 columnas para las Tablas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Detalle: Ventas por servicio --}}
        @if($ventasPorServicio->count())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 flex flex-col h-full">
            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4 flex items-center">
                <div class="bg-gray-100 p-1.5 rounded-md mr-2 text-gray-500">
                    <i class="fas fa-screwdriver-wrench"></i>
                </div>
                Ingresos por tipo de servicio
            </h3>
            
            <div class="overflow-x-auto rounded-xl border border-gray-200 flex-grow">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4">Servicio</th>
                            <th class="py-3 px-4 text-center">Órdenes</th>
                            <th class="py-3 px-4 text-right">Total</th>
                            <th class="py-3 px-4 text-right">Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ventasPorServicio as $nombre =>$dato)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $nombre ?? 'Sin nombre' }}</td>
                            <td class="py-3 px-4 text-center text-gray-600">
                                <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-bold">{{ $dato['cantidad'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-indigo-600">S/ {{ number_format($dato['total'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-500 font-medium">S/ {{ number_format($dato['total'] /$dato['cantidad'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/80 font-bold text-gray-700 border-t-2 border-gray-200">
                        <tr>
                            <td class="py-3 px-4 uppercase text-xs tracking-wider text-gray-500">Total</td>
                            <td class="py-3 px-4 text-center">{{ $totalOrdenes }}</td>
                            <td class="py-3 px-4 text-right text-indigo-600 text-base">S/ {{ number_format($totalVentas, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-500">S/ {{ number_format($ticketPromedio, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- Detalle: Ventas por técnico --}}
        @if($ventasPorTecnico->count())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 flex flex-col h-full">
            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-4 flex items-center">
                <div class="bg-gray-100 p-1.5 rounded-md mr-2 text-gray-500">
                    <i class="fas fa-user-gear"></i>
                </div>
                Ingresos por técnico
            </h3>
            
            <div class="overflow-x-auto rounded-xl border border-gray-200 flex-grow">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4">Técnico</th>
                            <th class="py-3 px-4 text-center">Órdenes</th>
                            <th class="py-3 px-4 text-right">Total</th>
                            <th class="py-3 px-4 text-right">Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ventasPorTecnico as $nombre =>$dato)
                        <tr class="hover:bg-emerald-50/40 transition-colors">
                            <td class="py-3 px-4 font-medium text-gray-800 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr($nombre ?? 'S', 0, 1) }}
                                </div>
                                {{ $nombre ?? 'Sin asignar' }}
                            </td>
                            <td class="py-3 px-4 text-center text-gray-600">
                                <span class="bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-bold">{{ $dato['cantidad'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-600">S/ {{ number_format($dato['total'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-500 font-medium">S/ {{ number_format($dato['total'] /$dato['cantidad'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

@script
    <script>
        window.renderReporteServiciosChart = function () {
            const canvas = document.getElementById('chartReporteServicios');
            if (!canvas) return;

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const conversionPendientes = JSON.parse(canvas.dataset.conversionPendientes || '[]');
            const conversionCompletadas = JSON.parse(canvas.dataset.conversionCompletadas || '[]');
            const simpleCompletadas = JSON.parse(canvas.dataset.simpleCompletadas || '[]');

            // Verificar si hay datos
            const hasData = conversionPendientes.some(v => Number(v) > 0) ||
                          conversionCompletadas.some(v => Number(v) > 0) ||
                          simpleCompletadas.some(v => Number(v) > 0);

            // Usar getCtxWithEmpty para manejar estado vacío
            const ctx = window.CHART_DEFS?.getCtxWithEmpty?.('chartReporteServicios', 'empty-chartReporteServicios', hasData);
            if (!ctx) return;

            if (!window.CHART_DEFS) return;

            window.CHART_DEFS.renderConvBar('chartReporteServicios', 'chartReporteServicios', labels, [
                { label: 'Conversión pendiente', data: conversionPendientes, backgroundColor: '#d97706', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                { label: 'Conversión completada', data: conversionCompletadas, backgroundColor: '#059669', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                { label: 'Simple completado', data: simpleCompletadas, backgroundColor: '#6b7280', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
            ], {
                stacked: true,
                options: {
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#1f2937',
                            bodyColor: '#4b5563',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: { label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y}` }
                        }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { font: { family: "'Inter', sans-serif" }, color: '#6b7280' } },
                        y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1, font: { family: "'Inter', sans-serif" }, color: '#9ca3af' }, grid: { color: '#f3f4f6', drawBorder: false, borderDash: [5, 5] } }
                    }
                }
            });
        };

        // Render inicial
        window.renderReporteServiciosChart();

        // Actualizar cuando Livewire re-renderiza
        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'servicios.reporte') {
                window.renderReporteServiciosChart();
            }
        });

        // Update payload cuando servidor envía nuevos datos
        $wire.on('chart-data-updated', (payload) => {
            const data = Array.isArray(payload) ? payload[0] : payload;
            const el = document.getElementById('chartReporteServicios');
            if (el) {
                el.dataset.labels = JSON.stringify(data.labels || []);
                el.dataset.conversionPendientes = JSON.stringify(data.conversionPendientes || []);
                el.dataset.conversionCompletadas = JSON.stringify(data.conversionCompletadas || []);
                el.dataset.simpleCompletadas = JSON.stringify(data.simpleCompletadas || []);
            }
            window.renderReporteServiciosChart();
        });

        // Alertas de exportación.
        Livewire.on('descargar-pdf', (params) => {
            AppSwal.exportar({
                url: params.url,
                titulo: 'Exportando PDF',
                texto: 'Generando el reporte, por favor espera...',
                archivo: 'PDF'
            });
        });

        Livewire.on('descargar-excel', (params) => {
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