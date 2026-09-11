<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-10 space-y-6">

    {{-- Header --}}
    <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl w-full">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                    <i class="fas fa-car text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-xl leading-tight">Reporte de Conversiones GNV</h2>
                    <span class="text-slate-400 text-xs">Kits instalados · Componentes · Balance almacén</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="exportarPDF()"
                    class="bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg py-2 px-4 transition-colors flex items-center gap-2 text-sm">
                    <i class="fas fa-file-pdf text-red-400"></i> PDF
                </button>
                <button onclick="exportarExcel()"
                    class="bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg py-2 px-4 transition-colors flex items-center gap-2 text-sm">
                    <i class="fas fa-file-excel text-emerald-400"></i> Excel
                </button>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                <i class="fas fa-car text-blue-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 leading-none">{{ number_format($totalConversiones) }}</p>
                <span class="text-xs text-slate-500">Conversiones</span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="fas fa-circle-check text-emerald-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 leading-none">{{ number_format($completadas) }}</p>
                <span class="text-xs text-slate-500">Completadas</span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                <i class="fas fa-gears text-amber-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 leading-none">{{ number_format($itemsInstalados) }}</p>
                <span class="text-xs text-slate-500">Piezas instaladas</span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                <i class="fas fa-box text-indigo-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 leading-none">{{ number_format($kitsEnStock) }}</p>
                <span class="text-xs text-slate-500">Kits en almacén</span>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-slate-400 flex items-center gap-1.5 mr-1">
            <i class="fas fa-sliders"></i> Filtros
        </span>

        <div class="flex items-center gap-1.5">
            <label class="text-xs text-slate-500">Sede</label>
            <select wire:model.live="filtroSede"
                class="rounded-lg border-slate-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todas</option>
                @foreach ($sedes as $s)
                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-1.5">
            <label class="text-xs text-slate-500">Estado</label>
            <select wire:model.live="filtroEstado"
                class="rounded-lg border-slate-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="todos">Todos</option>
                <option value="conversion_completada">Completadas</option>
                <option value="en_conversion">En proceso</option>
            </select>
        </div>

        <div class="flex items-center gap-1.5">
            <label class="text-xs text-slate-500">Desde</label>
            <input type="date" wire:model.live="filtroFechaDesde"
                class="rounded-lg border-slate-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="flex items-center gap-1.5">
            <label class="text-xs text-slate-500">Hasta</label>
            <input type="date" wire:model.live="filtroFechaHasta"
                class="rounded-lg border-slate-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        @if ($filtroSede || $filtroEstado !== 'todos' || $filtroFechaDesde || $filtroFechaHasta)
            <button wire:click="$set('filtroSede', null); $set('filtroEstado', 'todos'); $set('filtroFechaDesde', null); $set('filtroFechaHasta', null)"
                class="text-xs text-red-600 hover:text-red-800 font-semibold ml-auto flex items-center gap-1">
                <i class="fas fa-xmark"></i> Limpiar filtros
            </button>
        @endif
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5" wire:ignore>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Conversiones por mes</h3>
            </div>
            <div class="relative" style="height: 260px;">
                <canvas id="chartConversionesMes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5" wire:ignore>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Kits más utilizados</h3>
            </div>
            <div class="relative" style="height: 260px;">
                <canvas id="chartKitsTipo"></canvas>
            </div>
        </div>
    </div>

    @if (!empty($reportesPorPieza))
    <div class="bg-white rounded-xl border border-slate-200 p-5" wire:ignore>
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-slate-700">Piezas más reportadas (no calzan)</h3>
        </div>
        <div class="relative" style="height: 240px;">
            <canvas id="chartReportesPieza"></canvas>
        </div>
    </div>
    @endif

    {{-- Tabla detalle --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 text-sm">Detalle de conversiones</h3>
            <span class="text-xs text-slate-400">{{ count($detalleOrdenes) }} órdenes</span>
        </div>
        <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-2.5 text-left">#</th>
                        <th class="px-4 py-2.5 text-left">Cliente</th>
                        <th class="px-4 py-2.5 text-left">Placa</th>
                        <th class="px-4 py-2.5 text-left">Kit</th>
                        <th class="px-4 py-2.5 text-left">Items Serializados</th>
                        <th class="px-4 py-2.5 text-center">Gen.</th>
                        <th class="px-4 py-2.5 text-center">Comp.</th>
                        <th class="px-4 py-2.5 text-center">Inst.</th>
                        <th class="px-4 py-2.5 text-center">Reportes</th>
                        <th class="px-4 py-2.5 text-left">Inicio</th>
                        <th class="px-4 py-2.5 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($detalleOrdenes as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-2.5 font-bold text-slate-700">{{ $d['orden']->id }}</td>
                            <td class="px-4 py-2.5 font-medium text-slate-700">{{ $d['cliente'] }}</td>
                            <td class="px-4 py-2.5 font-bold text-slate-800">{{ $d['placa'] }}</td>
                            <td class="px-4 py-2.5">
                                <span class="text-xs text-slate-600">{{ $d['kit'] }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                @forelse ($d['items_serializados'] as $item)
                                    <div class="text-xs leading-relaxed">
                                        <span class="font-medium text-slate-700">{{ $item['nombre'] }}:</span>
                                        <span class="font-mono text-slate-500">{{ $item['serie'] }}</span>
                                    </div>
                                @empty
                                    <span class="text-xs text-slate-300">—</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-0.5 text-xs text-purple-700 font-semibold">
                                    {{ $d['generacion'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-center tabular-nums">{{ $d['total_componentes'] }}</td>
                            <td class="px-4 py-2.5 text-center tabular-nums font-semibold text-emerald-700">{{ $d['instalados'] }}</td>
                            <td class="px-4 py-2.5 text-center tabular-nums">
                                @if($d['reportes'] > 0)
                                    <span class="text-amber-600 font-semibold">{{ $d['reportes'] }}</span>
                                @else
                                    <span class="text-slate-300">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-slate-500 text-xs">{{ $d['fecha_inicio'] ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-center">
                                @if($d['orden']->estado === 'conversion_completada')
                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700 font-semibold">
                                        Completada
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs text-amber-700 font-semibold">
                                        En proceso
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-slate-400">
                                <i class="fas fa-inbox text-xl mb-2 block"></i>
                                Sin conversiones para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Balance del almacén --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">Balance de almacén</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-box text-indigo-500 text-xs"></i>
                        <span class="text-sm text-slate-600">Kits en stock</span>
                    </div>
                    <span class="text-lg font-bold text-slate-800">{{ number_format($kitsEnStock) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-puzzle-piece text-emerald-500 text-xs"></i>
                        <span class="text-sm text-slate-600">Piezas sueltas</span>
                    </div>
                    <span class="text-lg font-bold text-slate-800">{{ number_format($stockPiezasSueltas) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-wrench text-amber-500 text-xs"></i>
                        <span class="text-sm text-slate-600">Piezas por cantidad instaladas</span>
                    </div>
                    <span class="text-lg font-bold text-slate-800">{{ number_format($piezasCantidad) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">Resumen</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-car text-blue-500 text-xs"></i>
                        <span class="text-sm text-slate-600">Total conversiones</span>
                    </div>
                    <span class="text-lg font-bold text-slate-800">{{ number_format($totalConversiones) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                        <span class="text-sm text-slate-600">Completadas</span>
                    </div>
                    <span class="text-lg font-bold text-emerald-600">{{ number_format($completadas) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-gears text-amber-500 text-xs"></i>
                        <span class="text-sm text-slate-600">Piezas instaladas</span>
                    </div>
                    <span class="text-lg font-bold text-amber-600">{{ number_format($itemsInstalados) }}</span>
                </div>
            </div>
        </div>
    </div>

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
                                { label: 'Completadas', data: completadas, backgroundColor: '#10b981', borderRadius: 6 },
                                { label: 'En proceso', data: enProceso, backgroundColor: '#f59e0b', borderRadius: 6 }
                            ]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } } },
                            scales: { y: { beginAtZero: true, stacked: true, grid: { color: '#f1f5f9' } }, x: { stacked: true, grid: { display: false } } }
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
                            responsive: true, maintainAspectRatio: false, cutout: '60%',
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } } }
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
                            responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                            plugins: { legend: { display: false } },
                            scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9' } }, y: { grid: { display: false } } }
                        }
                    });
                }
            }
        }

        document.addEventListener('livewire:navigated', renderCharts);
        document.addEventListener('livewire:updated', renderCharts);
    </script>

    <script>
        window.exportarPDF = function() {
            Swal.fire({
                title: 'Exportando PDF', text: 'Generando el reporte...', icon: 'info',
                allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); window.location.href = '{{ $this->exportPdfUrl() }}'; setTimeout(() => { Swal.close(); }, 3000); }
            });
        };
        window.exportarExcel = function() {
            Swal.fire({
                title: 'Exportando Excel', text: 'Generando el reporte...', icon: 'info',
                allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); window.location.href = '{{ $this->exportExcelUrl() }}'; setTimeout(() => { Swal.close(); }, 3000); }
            });
        };
    </script>
</div>
