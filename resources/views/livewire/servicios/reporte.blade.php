<div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Cabecera y Filtros --}}
    <div class="bg-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm border border-gray-300/50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            {{-- Título --}}
            <div>
                <h2 class="text-gray-700 font-bold text-2xl tracking-tight flex items-center">
                    <div class="bg-white p-2 rounded-lg shadow-sm mr-3">
                        <i class="fas fa-chart-simple text-indigo-600 text-xl"></i>
                    </div>
                    Reporte de servicios
                </h2>
                <p class="text-gray-500 text-sm mt-1.5 ml-1">Conversiones, servicios simples y tendencia del período</p>
            </div>

            {{-- Controles y Botones --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-6 w-full lg:w-auto">
                
                {{-- Filtros --}}
                <div class="flex flex-wrap items-center gap-3 bg-white/50 p-2 rounded-xl border border-gray-300/50 w-full sm:w-auto">
                    <div class="flex items-center gap-2">
                        <input type="date" wire:model.live="desde" class="text-sm text-gray-700 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm py-2">
                        <span class="text-gray-500 text-sm font-medium">a</span>
                        <input type="date" wire:model.live="hasta" class="text-sm text-gray-700 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm py-2">
                    </div>
                    
                    <div class="w-full sm:w-auto h-px sm:h-6 w-px bg-gray-300 sm:mx-1 hidden sm:block"></div>

                    <select wire:model.live="tipoServicio" class="w-full sm:w-auto text-sm text-gray-700 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm py-2 cursor-pointer">
                        <option value="todos">Todos los servicios</option>
                        <option value="simple">Solo simples</option>
                        <option value="conversion">Solo conversión</option>
                    </select>
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button wire:click="descargarPdf" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl py-2.5 px-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-2 w-full sm:w-auto">
                        <i class="fas fa-file-pdf"></i>
                        <span>PDF</span>
                    </button>
                    <button wire:click="descargarExcel" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl py-2.5 px-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-2 w-full sm:w-auto">
                        <i class="fas fa-file-excel"></i>
                        <span>Excel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total ventas</span>
                <i class="fas fa-sack-dollar text-indigo-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-indigo-600">S/ {{ number_format($totalVentas, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Órdenes</span>
                <i class="fas fa-file-invoice text-gray-200 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-700">{{ $totalOrdenes }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ticket promedio</span>
                <i class="fas fa-receipt text-gray-200 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-700">S/ {{ number_format($ticketPromedio, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Descuentos</span>
                <i class="fas fa-tags text-red-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-red-500">S/ {{ number_format($totalDescuentos, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Conv. pendientes</span>
                <i class="fas fa-clock text-amber-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-amber-600">{{ $totalConversionesPendientes }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-emerald-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Conv. completadas</span>
                <i class="fas fa-check-circle text-emerald-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-emerald-600">{{ $totalConversionesCompletadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-gray-400">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Simples completados</span>
                <i class="fas fa-wrench text-gray-200 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-700">{{ $totalSimplesCompletadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">T. Prom. Conversión</span>
                <i class="fas fa-stopwatch text-amber-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-amber-600">{{ $tiempoPromedio }}h</p>
        </div>
    </div>

    {{-- Gráfico de servicios por día --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8" wire:ignore wire:key="reporte-servicios-chart">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-chart-column text-gray-400"></i>
                Órdenes por día (desglose)
            </h3>
        </div>

        <div class="relative w-full" style="height: 360px;">
            <canvas id="chartReporteServicios"
                data-labels='@json($labels)'
                data-conversion-pendientes='@json($conversionPendientes)'
                data-conversion-completadas='@json($conversionCompletadas)'
                data-simple-completadas='@json($simpleCompletadas)'></canvas>
        </div>
        
        @if ($hayDatos)
            <div class="flex flex-wrap gap-4 mt-6 justify-center">
                @if ($totalConversionesPendientes > 0)
                    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full border border-gray-100 shadow-sm text-sm">
                        <span class="w-3.5 h-3.5 rounded-full shadow-inner" style="background: #d97706"></span>
                        <span class="font-semibold text-gray-600">Pendientes</span>
                        <span class="text-gray-400 font-medium">({{ $totalConversionesPendientes }})</span>
                    </div>
                @endif
                @if ($totalConversionesCompletadas > 0)
                    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full border border-gray-100 shadow-sm text-sm">
                        <span class="w-3.5 h-3.5 rounded-full shadow-inner" style="background: #059669"></span>
                        <span class="font-semibold text-gray-600">Completadas</span>
                        <span class="text-gray-400 font-medium">({{ $totalConversionesCompletadas }})</span>
                    </div>
                @endif
                @if ($totalSimplesCompletadas > 0)
                    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full border border-gray-100 shadow-sm text-sm">
                        <span class="w-3.5 h-3.5 rounded-full shadow-inner" style="background: #6b7280"></span>
                        <span class="font-semibold text-gray-600">Simples</span>
                        <span class="text-gray-400 font-medium">({{ $totalSimplesCompletadas }})</span>
                    </div>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-center bg-gray-50/50 rounded-xl mt-4 border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-white shadow-sm rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-600 font-semibold text-lg">Todavía no hay movimiento registrado</p>
                <p class="text-gray-400 text-sm mt-1">Las órdenes de servicio aparecerán aquí cuando se creen en el rango de fechas.</p>
            </div>
        @endif
    </div>

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

            if (window.chartReporteServiciosInstance) window.chartReporteServiciosInstance.destroy();

            // Configuración visual mejorada para Chart.js
            window.chartReporteServiciosInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Conversión pendiente', data: conversionPendientes, backgroundColor: '#d97706', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                        { label: 'Conversión completada', data: conversionCompletadas, backgroundColor: '#059669', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                        { label: 'Simple completado', data: simpleCompletadas, backgroundColor: '#6b7280', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
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
                            callbacks: { 
                                label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y}` 
                            } 
                        }
                    },
                    scales: {
                        x: { 
                            stacked: true,
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { family: "'Inter', sans-serif" }, color: '#6b7280' }
                        },
                        y: { 
                            stacked: true, 
                            beginAtZero: true, 
                            ticks: { stepSize: 1, font: { family: "'Inter', sans-serif" }, color: '#9ca3af' },
                            grid: { color: '#f3f4f6', drawBorder: false, borderDash: [5, 5] }
                        }
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

        // Update canvas data-* attrs when server dispatches new chart data
        $wire.on('chart-data-updated', (data) => {
            const canvas = document.getElementById('chartReporteServicios');
            if (!canvas) return;
            canvas.dataset.labels = JSON.stringify(data.labels);
            canvas.dataset.conversionPendientes = JSON.stringify(data.conversionPendientes);
            canvas.dataset.conversionCompletadas = JSON.stringify(data.conversionCompletadas);
            canvas.dataset.simplePendientes = JSON.stringify(data.simplePendientes);
            canvas.dataset.simpleCompletadas = JSON.stringify(data.simpleCompletadas);
            window.renderReporteServiciosChart();
        });

        Livewire.on('descargar-pdf', (params) => {
            const url = params.url;
            if (!url || url === '#') {
                Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar en este período.', icon: 'warning', timer: 3000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                return;
            }
            Swal.fire({
                title: 'Exportando PDF',
                text: 'Generando el reporte, por favor espera...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' },
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = url;
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({ title: 'Descarga iniciada', text: 'El archivo PDF se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                    }, 3000);
                }
            });
        });

        Livewire.on('descargar-excel', (params) => {
            const url = params.url;
            if (!url || url === '#') {
                Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar en este período.', icon: 'warning', timer: 3000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                return;
            }
            Swal.fire({
                title: 'Exportando Excel',
                text: 'Generando el reporte, por favor espera...',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                customClass: { popup: 'rounded-2xl' },
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = url;
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({ title: 'Descarga iniciada', text: 'El archivo Excel se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                    }, 3000);
                }
            });
        });
    </script>
    @endscript
</div>