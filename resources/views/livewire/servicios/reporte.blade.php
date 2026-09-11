<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">

    <div class="bg-gray-200 p-8 rounded-xl w-full">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-chart-simple mr-2"></i>Reporte de servicios
                </h2>
                <span class="text-xs">Conversiones, servicios simples y tendencia del período</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <input type="date" wire:model.live="desde" class="text-sm rounded-lg border-gray-300">
                <span class="text-gray-500 text-sm">a</span>
                <input type="date" wire:model.live="hasta" class="text-sm rounded-lg border-gray-300">
                <select wire:model.live="tipoServicio" class="text-sm rounded-lg border-gray-300">
                    <option value="todos">Todos los servicios</option>
                    <option value="simple">Solo simples</option>
                    <option value="conversion">Solo conversión</option>
                </select>
            </div>
            <div class="flex items-center gap-2 mt-3">
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

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Convers. pendientes</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $totalConversionesPendientes }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Convers. completadas</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalConversionesCompletadas }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Simples completados</span>
            <p class="text-2xl font-bold text-gray-700 mt-1">{{ $totalSimplesCompletadas }}</p>
        </div>
    </div>

    {{-- Gráfico de servicios por día --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="reporte-servicios-chart">
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Órdenes por día (desglose por tipo y estado)</h3>

        <div class="relative w-full" style="height: 320px;">
            <canvas id="chartReporteServicios"
                data-labels='@json($labels)'
                data-conversion-pendientes='@json($conversionPendientes)'
                data-conversion-completadas='@json($conversionCompletadas)'
                data-simple-completadas='@json($simpleCompletadas)'></canvas>
        </div>
        @if ($hayDatos)
            <div class="flex flex-wrap gap-4 mt-3 justify-center">
                @if ($totalConversionesPendientes > 0)
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-3 h-3 rounded-sm" style="background: #d97706"></span>
                        <span class="font-medium text-gray-600">Conversión pendiente</span>
                        <span class="text-gray-400">{{ $totalConversionesPendientes }}</span>
                    </div>
                @endif
                @if ($totalConversionesCompletadas > 0)
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-3 h-3 rounded-sm" style="background: #059669"></span>
                        <span class="font-medium text-gray-600">Conversión completada</span>
                        <span class="text-gray-400">{{ $totalConversionesCompletadas }}</span>
                    </div>
                @endif
                @if ($totalSimplesCompletadas > 0)
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-3 h-3 rounded-sm" style="background: #6b7280"></span>
                        <span class="font-medium text-gray-600">Simple completado</span>
                        <span class="text-gray-400">{{ $totalSimplesCompletadas }}</span>
                    </div>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">Todavía no hay movimiento registrado</p>
                <p class="text-gray-400 text-sm mt-1">Las órdenes de servicio aparecerán aquí cuando se creen</p>
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

            window.chartReporteServiciosInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Conversión pendiente', data: conversionPendientes, backgroundColor: '#d97706', borderRadius: 3, borderSkipped: false },
                        { label: 'Conversión completada', data: conversionCompletadas, backgroundColor: '#059669', borderRadius: 3, borderSkipped: false },
                        { label: 'Simple completado', data: simpleCompletadas, backgroundColor: '#6b7280', borderRadius: 3, borderSkipped: false },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed.y } }
                    },
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
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
        // (wire:ignore prevents morph from updating them)
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

        Livewire.on('descargar-excel', (params) => {
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
