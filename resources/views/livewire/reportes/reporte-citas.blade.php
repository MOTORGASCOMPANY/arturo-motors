<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">

    {{-- Header --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-calendar-check text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Reporte de Citas</h2>
                    <p class="text-gray-500 text-sm mt-1">Análisis de agendamiento y conversión</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <input type="date" wire:model.live="desde" class="text-sm rounded-lg border-gray-200 bg-slate-50 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                <span class="text-slate-400 text-sm">a</span>
                <input type="date" wire:model.live="hasta" class="text-sm rounded-lg border-gray-200 bg-slate-50 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                <select wire:model.live="sedeId" class="text-sm rounded-lg border-gray-200 bg-slate-50 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                    <option value="todos">Todas las sedes</option>
                    @foreach($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
                <select wire:model.live="estado" class="text-sm rounded-lg border-gray-200 bg-slate-50 text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3">
                    <option value="todos">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="aceptada">Aceptada</option>
                    <option value="rechazada">Rechazada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
                <button wire:click="descargarPdf" class="bg-white hover:bg-red-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                    <i class="fas fa-file-pdf text-red-500"></i> PDF
                </button>
                <button wire:click="descargarExcel" class="bg-white hover:bg-emerald-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                    <i class="fas fa-file-excel text-emerald-500"></i> Excel
                </button>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 text-center">
            <span class="text-xs font-bold text-slate-500 uppercase">Total citas</span>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-amber-200 p-5 text-center">
            <span class="text-xs font-bold text-slate-500 uppercase">Pendientes</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendientes }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-emerald-200 p-5 text-center">
            <span class="text-xs font-bold text-slate-500 uppercase">Aceptadas</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $aceptadas }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-5 text-center">
            <span class="text-xs font-bold text-slate-500 uppercase">Rechazadas</span>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $rechazadas }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 text-center">
            <span class="text-xs font-bold text-slate-500 uppercase">Canceladas</span>
            <p class="text-2xl font-bold text-slate-500 mt-1">{{ $canceladas }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-indigo-200 p-5 text-center">
            <span class="text-xs font-bold text-slate-500 uppercase">% Conversión a OS</span>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $porcentajeConversion }}%</p>
        </div>
    </div>

    {{-- Lineal suave: horas del día seleccionado --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6" wire:key="chart-citas">
        {{-- Header: 3 filas, sin apretar --}}
        <div class="mb-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-chart-line text-emerald-600 text-sm"></i>
                    </span>
                    <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">{{ $chartTitle }}</h3>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200 rounded-full px-3 py-1.5">
                        <i class="fas fa-filter text-slate-400 mr-1"></i>{{ $filtroBadge }}
                    </span>
                    <span class="text-[13px] font-extrabold text-emerald-600">+{{ $sumAceptadas }}</span>
                    <span class="text-[13px] font-extrabold text-red-500">-{{ $sumNoAceptadas }}</span>
                    <span class="text-[13px] font-bold text-indigo-600" title="Con orden de servicio">{{ $sumConversion }} OS</span>
                </div>
            </div>

            {{-- Navegación de día --}}
            <div class="flex items-center justify-center gap-3">
                <button
                    type="button"
                    wire:click="diaAnterior"
                    class="w-9 h-9 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center transition shadow-sm"
                    title="Día anterior"
                >
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <div class="text-center min-w-[220px] px-4 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="text-base font-extrabold text-slate-800 capitalize">{{ $periodoLabel }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">00:00 – 23:59 · {{ $citasDelDia }} citas</div>
                </div>
                <button
                    type="button"
                    wire:click="diaSiguiente"
                    class="w-9 h-9 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center transition shadow-sm"
                    title="Día siguiente"
                >
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

        @if ($total > 0)
            <div class="relative w-full" style="height: 300px;" wire:ignore>
                <canvas id="chartCitas"
                    data-labels='@json($labels)'
                    data-aceptadas='@json($aceptadasPorPeriodo ?? [])'
                    data-noaceptadas='@json($noAceptadasPorPeriodo ?? [])'
                    data-conversion='@json($conversionPorPeriodo ?? [])'></canvas>
            </div>
            <div class="flex flex-wrap gap-5 mt-4 justify-center text-xs font-semibold text-slate-600">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm border-2 border-emerald-500 bg-emerald-400"></span> Aceptadas</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm border-2 border-red-500 bg-red-400"></span> No aceptadas</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm border-2 border-indigo-500 bg-indigo-400"></span> Con OS (conversión)</div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <i class="fas fa-calendar-times text-4xl text-slate-300 mb-3"></i>
                <p class="text-slate-500 font-medium">No hay citas en este período</p>
            </div>
        @endif
    </div>

    {{-- Citas por asesor --}}
    @if($porAsesor->count())
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-500 uppercase mb-4"><i class="fas fa-user-tie mr-1.5 text-slate-400"></i>Citas por asesor</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2.5 px-3 font-semibold text-slate-600">Asesor</th>
                        <th class="text-center py-2.5 px-3 font-semibold text-slate-600">Total</th>
                        <th class="text-center py-2.5 px-3 font-semibold text-slate-600">Aceptadas</th>
                        <th class="text-center py-2.5 px-3 font-semibold text-slate-600">Rechazadas</th>
                        <th class="text-center py-2.5 px-3 font-semibold text-slate-600">Canceladas</th>
                        <th class="text-center py-2.5 px-3 font-semibold text-slate-600">Con OS</th>
                        <th class="text-center py-2.5 px-3 font-semibold text-slate-600">% Aceptación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porAsesor as $nombre => $dato)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="py-2.5 px-3 font-medium text-slate-700">{{ $nombre }}</td>
                        <td class="py-2.5 px-3 text-center font-bold text-slate-800">{{ $dato['total'] }}</td>
                        <td class="py-2.5 px-3 text-center text-emerald-600">{{ $dato['aceptadas'] }}</td>
                        <td class="py-2.5 px-3 text-center text-red-600">{{ $dato['rechazadas'] }}</td>
                        <td class="py-2.5 px-3 text-center text-slate-400">{{ $dato['canceladas'] }}</td>
                        <td class="py-2.5 px-3 text-center text-indigo-600 font-semibold">{{ $dato['con_orden'] }}</td>
                        <td class="py-2.5 px-3 text-center">
                            @php $pct = $dato['total'] > 0 ? round(($dato['aceptadas'] / $dato['total']) * 100, 1) : 0; @endphp
                            <span class="inline-flex items-center rounded-md {{ $pct >= 70 ? 'bg-emerald-50 text-emerald-700' : ($pct >= 40 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }} px-2 py-0.5 text-xs font-semibold">
                                {{ $pct }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Citas por sede --}}
    @if($porSede->count())
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-500 uppercase mb-4"><i class="fas fa-building mr-1.5 text-slate-400"></i>Citas por sede</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($porSede as $nombre => $dato)
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                <div class="font-semibold text-slate-700 text-sm mb-2">{{ $nombre }}</div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="text-slate-600">Total: <strong>{{ $dato['total'] }}</strong></span>
                    <span class="text-emerald-600">Aceptadas: <strong>{{ $dato['aceptadas'] }}</strong></span>
                    <span class="text-amber-600">Pendientes: <strong>{{ $dato['pendientes'] }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Top motivos --}}
    @if($motivos->count())
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-slate-500 uppercase mb-4"><i class="fas fa-comment-dots mr-1.5 text-slate-400"></i>Principales motivos de consulta</h3>
        <div class="space-y-2">
            @foreach($motivos as $motivo => $cantidad)
            <div class="flex items-center justify-between bg-slate-50 rounded-lg px-4 py-2.5 border border-slate-100">
                <span class="text-sm text-slate-700 font-medium">{{ $motivo }}</span>
                <span class="text-sm font-bold text-indigo-600">{{ $cantidad }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @script
    <script>
        window.renderChartCitas = function () {
            const canvas = document.getElementById('chartCitas');
            if (!canvas || typeof Chart === 'undefined') return;

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const aceptadas = JSON.parse(canvas.dataset.aceptadas || '[]');
            const noAceptadas = JSON.parse(canvas.dataset.noaceptadas || '[]');
            const conversion = JSON.parse(canvas.dataset.conversion || '[]');

            if (window.chartCitasInstance) window.chartCitasInstance.destroy();

            window.chartCitasInstance = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Aceptadas',
                            data: aceptadas,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'No aceptadas',
                            data: noAceptadas,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.12)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#ef4444',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Con OS (conversión)',
                            data: conversion,
                            borderColor: '#4f46e5',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [6, 4],
                            fill: false,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 2
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12 },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed.y
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { maxRotation: 45, autoSkip: true, font: { size: 10 }, color: '#94a3b8' },
                            grid: { display: false },
                            border: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0, color: '#94a3b8', font: { size: 10 } },
                            grid: { color: '#f1f5f9' },
                            border: { display: false }
                        }
                    }
                }
            });
        };

        window.renderChartCitas();

        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'reportes.reporte-citas') window.renderChartCitas();
        });

        $wire.on('chart-data-citas', (data) => {
            const canvas = document.getElementById('chartCitas');
            if (!canvas) return;
            canvas.dataset.labels = JSON.stringify(data.labels);
            canvas.dataset.aceptadas = JSON.stringify(data.aceptadas);
            canvas.dataset.noaceptadas = JSON.stringify(data.noAceptadas);
            canvas.dataset.conversion = JSON.stringify(data.conversion || []);
            window.renderChartCitas();
        });

        Livewire.on('descargar-pdf', (params) => {
            const url = params.url;
            if (!url) { Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar.', icon: 'warning', timer: 3000, showConfirmButton: false }); return; }
            Swal.fire({ title: 'Exportando PDF', text: 'Generando el reporte...', icon: 'info', allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); window.location.href = url; setTimeout(() => { Swal.close(); Swal.fire({ title: 'Descarga iniciada', text: 'El archivo PDF se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false }); }, 3000); }
            });
        });

        Livewire.on('descargar-excel', (params) => {
            const url = params.url;
            if (!url) { Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar.', icon: 'warning', timer: 3000, showConfirmButton: false }); return; }
            Swal.fire({ title: 'Exportando Excel', text: 'Generando el reporte...', icon: 'info', allowOutsideClick: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); window.location.href = url; setTimeout(() => { Swal.close(); Swal.fire({ title: 'Descarga iniciada', text: 'El archivo Excel se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false }); }, 3000); }
            });
        });
    </script>
    @endscript
</div>
