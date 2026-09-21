<div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Header --}}
    <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl w-full shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/10 border border-white/5 flex items-center justify-center shrink-0 shadow-inner">
                    <i class="fas fa-car text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-white font-bold text-2xl tracking-tight">Reporte de Conversiones GNV</h2>
                    <p class="text-slate-400 text-sm mt-1">Kits instalados · Componentes · Balance de almacén</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                <button onclick="exportarPDF()"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-file-pdf text-red-400"></i>
                    PDF
                </button>
                <button onclick="exportarExcel()"
                    class="bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-file-excel text-emerald-400"></i>
                    Excel
                </button>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-gauge-high text-slate-400"></i>
            Indicadores generales
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 border-l-4 border-l-blue-500 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-car text-blue-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ number_format($totalConversiones) }}</p>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Conversiones</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 border-l-4 border-l-emerald-500 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-circle-check text-emerald-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold text-emerald-600 leading-none">{{ number_format($completadas) }}</p>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Completadas</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 border-l-4 border-l-indigo-500 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-percent text-indigo-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold text-indigo-600 leading-none">{{ $tasaCompletado }}%</p>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tasa completado</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 border-l-4 border-l-amber-500 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold text-amber-600 leading-none">{{ $duracionPromedio }}h</p>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Duración promedio</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 border-l-4 border-l-amber-500 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-gears text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ number_format($itemsInstalados) }}</p>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Piezas instaladas</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 border-l-4 border-l-indigo-500 p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <i class="fas fa-box text-indigo-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold text-slate-800 leading-none">{{ number_format($kitsEnStock) }}</p>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kits en almacén</span>
                </div>
            </div>

        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-sliders text-slate-400"></i>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filtros</span>
        </div>

        <div class="flex flex-wrap items-end gap-4">

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500">Sede</label>
                <select wire:model.live="filtroSede"
                    class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 min-w-[10rem]">
                    <option value="">Todas</option>
                    @foreach ($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500">Estado</label>
                <select wire:model.live="filtroEstado"
                    class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 min-w-[10rem]">
                    <option value="todos">Todos</option>
                    <option value="conversion_completada">Completadas</option>
                    <option value="en_conversion">En proceso</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500">Desde</label>
                <input type="date" wire:model.live="filtroFechaDesde"
                    class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-slate-500">Hasta</label>
                <input type="date" wire:model.live="filtroFechaHasta"
                    class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50">
            </div>

            @if ($filtroSede || $filtroEstado !== 'todos' || $filtroFechaDesde || $filtroFechaHasta)
                <button wire:click="$set('filtroSede', null); $set('filtroEstado', 'todos'); $set('filtroFechaDesde', null); $set('filtroFechaHasta', null)"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-full px-3.5 py-2 ml-auto transition-colors">
                    <i class="fas fa-xmark"></i>
                    Limpiar filtros
                </button>
            @endif

        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6" wire:ignore>
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-chart-column text-slate-400"></i>
                Conversiones por mes
            </h3>
            <div class="relative" style="height: 280px;">
                <canvas id="chartConversionesMes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6" wire:ignore>
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-chart-pie text-slate-400"></i>
                Kits más utilizados
            </h3>
            <div class="relative" style="height: 280px;">
                <canvas id="chartKitsTipo"></canvas>
            </div>
        </div>
    </div>

    @if (!empty($reportesPorPieza))
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6" wire:ignore>
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-slate-400"></i>
                Piezas más reportadas (no calzan)
            </h3>
            <div class="relative" style="height: 260px;">
                <canvas id="chartReportesPieza"></canvas>
            </div>
        </div>
    @endif

    {{-- Tabla detalle --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                <i class="fas fa-list-check text-slate-400"></i>
                Detalle de conversiones
            </h3>
            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                {{ count($detalleOrdenes) }} órdenes
            </span>
        </div>

        <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200 shadow-sm">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Placa</th>
                        <th class="px-4 py-3">Kit</th>
                        <th class="px-4 py-3">Items Serializados</th>
                        <th class="px-4 py-3 text-center">Gen.</th>
                        <th class="px-4 py-3 text-center">Comp.</th>
                        <th class="px-4 py-3 text-center">Inst.</th>
                        <th class="px-4 py-3 text-center">Reportes</th>
                        <th class="px-4 py-3">Inicio</th>
                        <th class="px-4 py-3">Fin</th>
                        <th class="px-4 py-3 text-center">Duración</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($detalleOrdenes as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-slate-700">{{ $d['orden']->id }}</td>
                            <td class="px-4 py-3.5 font-medium text-slate-700">{{ $d['cliente'] }}</td>
                            <td class="px-4 py-3.5">
                                <span class="border border-slate-200 bg-slate-50 px-2 py-1 rounded text-xs font-mono font-bold text-slate-800">
                                    {{ $d['placa'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs text-slate-600">{{ $d['kit_nombre'] }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @forelse ($d['items_serializados'] as $item)
                                    <div class="text-xs leading-relaxed bg-slate-50 rounded-md px-2 py-1 mb-1 last:mb-0">
                                        <span class="font-semibold text-slate-700">{{ $item['nombre'] }}:</span>
                                        <span class="font-mono text-slate-500">{{ $item['serie'] }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-slate-300">—</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center rounded-md border border-purple-200 bg-purple-50 px-2.5 py-1 text-xs text-purple-700 font-bold">
                                    {{ $d['kit_generacion'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center tabular-nums text-slate-600 font-medium">{{ $d['total_componentes'] }}</td>
                            <td class="px-4 py-3.5 text-center tabular-nums font-bold text-emerald-700">{{ $d['instalados'] }}</td>
                            <td class="px-4 py-3.5 text-center tabular-nums">
                                @if ($d['reportes'] > 0)
                                    <span class="inline-flex items-center rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs text-amber-700 font-bold">
                                        {{ $d['reportes'] }}
                                    </span>
                                @else
                                    <span class="text-slate-300 font-medium">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 text-xs">{{ $d['fecha_inicio'] ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-slate-500 text-xs">{{ $d['fecha_fin'] ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($d['duracion_horas'] !== null)
                                    <span class="inline-flex items-center rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs text-amber-700 font-bold tabular-nums">
                                        {{ $d['duracion_horas'] }}h
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($d['orden']->estado === 'conversion_completada')
                                    <span class="inline-flex items-center rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs text-emerald-700 font-bold uppercase tracking-wider">
                                        Completada
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs text-amber-700 font-bold uppercase tracking-wider">
                                        En proceso
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block text-slate-300"></i>
                                <span class="font-medium">Sin conversiones para los filtros seleccionados.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Balance del almacén --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-warehouse text-slate-400"></i>
                Balance de almacén
            </h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-box text-indigo-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-600">Kits en stock</span>
                    </div>
                    <span class="text-lg font-extrabold text-slate-800">{{ number_format($kitsEnStock) }}</span>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-puzzle-piece text-emerald-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-600">Piezas sueltas</span>
                    </div>
                    <span class="text-lg font-extrabold text-slate-800">{{ number_format($stockPiezasSueltas) }}</span>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-wrench text-amber-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-600">Piezas por cantidad instaladas</span>
                    </div>
                    <span class="text-lg font-extrabold text-slate-800">{{ number_format($piezasCantidad) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-chart-simple text-slate-400"></i>
                Resumen
            </h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-car text-blue-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-600">Total conversiones</span>
                    </div>
                    <span class="text-lg font-extrabold text-slate-800">{{ number_format($totalConversiones) }}</span>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-check-circle text-emerald-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-600">Completadas</span>
                    </div>
                    <span class="text-lg font-extrabold text-emerald-600">{{ number_format($completadas) }}</span>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                            <i class="fas fa-gears text-amber-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-600">Piezas instaladas</span>
                    </div>
                    <span class="text-lg font-extrabold text-amber-600">{{ number_format($itemsInstalados) }}</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        function renderCharts() {
            const colors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

            // Chart: Conversiones por mes
            const ctxMes = document.getElementById('chartConversionesMes');
            if (ctxMes) {
                const porMes = @json($porMes);
                const labels = Object.keys(porMes);
                const completadas = labels.map(k => porMes[k]['completadas']);
                const enProceso = labels.map(k => porMes[k]['en_proceso']);

                if (window.chartMes) window.chartMes.destroy();
                if (labels.length > 0) {
                    window.chartMes = new Chart(ctxMes, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'Completadas', data: completadas, backgroundColor: '#10b981', borderRadius: 6, barPercentage: 0.6, categoryPercentage: 0.8 },
                                { label: 'En proceso', data: enProceso, backgroundColor: '#f59e0b', borderRadius: 6, barPercentage: 0.6, categoryPercentage: 0.8 }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } } }
                            },
                            scales: {
                                y: { beginAtZero: true, stacked: true, grid: { color: '#f1f5f9', drawBorder: false } },
                                x: { stacked: true, grid: { display: false } }
                            }
                        }
                    });
                }
            }

            // Chart: Kits más utilizados
            const ctxKits = document.getElementById('chartKitsTipo');
            if (ctxKits) {
                const kitsPorTipo = @json($kitsPorTipo);
                const labels = Object.keys(kitsPorTipo);
                const data = Object.values(kitsPorTipo);

                if (window.chartKits) window.chartKits.destroy();
                if (labels.length > 0) {
                    window.chartKits = new Chart(ctxKits, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{ data: data, backgroundColor: colors, borderWidth: 2, borderColor: '#ffffff' }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } } }
                            }
                        }
                    });
                }
            }

            // Chart: Piezas más reportadas
            const ctxReportes = document.getElementById('chartReportesPieza');
            if (ctxReportes) {
                const reportesPorPieza = @json($reportesPorPieza);
                const labels = Object.keys(reportesPorPieza);
                const data = Object.values(reportesPorPieza);

                if (window.chartReportes) window.chartReportes.destroy();
                if (labels.length > 0) {
                    window.chartReportes = new Chart(ctxReportes, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{ label: 'Reportes', data: data, backgroundColor: '#ef4444', borderRadius: 6, maxBarThickness: 40 }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false } },
                                y: { grid: { display: false } }
                            }
                        }
                    });
                }
            }
        }

        document.addEventListener('livewire:navigated', renderCharts);
        document.addEventListener('livewire:updated', renderCharts);
    </script>

    <script>
        window.exportarPDF = function () {
            Swal.fire({
                title: 'Exportando PDF',
                text: 'Generando el reporte...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = '{{ $this->exportPdfUrl() }}';
                    setTimeout(() => { Swal.close(); }, 3000);
                }
            });
        };
        window.exportarExcel = function () {
            Swal.fire({
                title: 'Exportando Excel',
                text: 'Generando el reporte...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = '{{ $this->exportExcelUrl() }}';
                    setTimeout(() => { Swal.close(); }, 3000);
                }
            });
        };
    </script>
</div>