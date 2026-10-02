<div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Cabecera: identificación + exportación arriba, filtros debajo --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col gap-6">

            {{-- Fila 1: identidad + exportación --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4 shrink-0">
                    <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-calendar-check text-indigo-600 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Reporte de Citas</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ $total }} citas · {{ $periodoLabel }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="descargarPdf" wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 bg-white hover:bg-red-50 border border-gray-200 text-gray-700 font-semibold text-sm rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-file-pdf text-red-500"></i> PDF
                    </button>
                    <button wire:click="descargarExcel" wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 bg-white hover:bg-emerald-50 border border-gray-200 text-gray-700 font-semibold text-sm rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-file-excel text-emerald-600"></i> Excel
                    </button>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            {{-- Fila 2: filtros con etiqueta, una columna por campo --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="citas-desde" class="text-xs font-semibold text-gray-500">Desde</label>
                    <input id="citas-desde" type="date" wire:model.live="desde"
                        class="w-full text-sm text-gray-700 rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="citas-hasta" class="text-xs font-semibold text-gray-500">Hasta</label>
                    <input id="citas-hasta" type="date" wire:model.live="hasta"
                        class="w-full text-sm text-gray-700 rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="citas-sede" class="text-xs font-semibold text-gray-500">Sede</label>
                    <select id="citas-sede" wire:model.live="sedeId"
                        class="w-full text-sm text-gray-700 rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors cursor-pointer">
                        <option value="todos">Todas las sedes</option>
                        @foreach($sedes as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="citas-estado" class="text-xs font-semibold text-gray-500">Estado</label>
                    <select id="citas-estado" wire:model.live="estado"
                        class="w-full text-sm text-gray-700 rounded-lg border border-gray-200 bg-gray-50 py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors cursor-pointer">
                        <option value="todos">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aceptada">Aceptada</option>
                        <option value="rechazada">Rechazada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5">
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total citas</span>
                <i class="fas fa-calendar-check text-indigo-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-indigo-600">{{ $total }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-amber-500">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pendientes</span>
                <i class="fas fa-clock text-amber-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-amber-600">{{ $pendientes }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-emerald-500">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Aceptadas</span>
                <i class="fas fa-check-circle text-emerald-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-emerald-600">{{ $aceptadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-red-500">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rechazadas</span>
                <i class="fas fa-times-circle text-red-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-red-500">{{ $rechazadas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center border-l-4 border-l-gray-400">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Canceladas</span>
                <i class="fas fa-ban text-gray-200 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-gray-700">{{ $canceladas }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200/80 p-6 flex flex-col justify-center">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Conversión a OS</span>
                <i class="fas fa-share-nodes text-indigo-100 text-lg"></i>
            </div>
            <p class="text-3xl font-extrabold text-indigo-600">{{ $porcentajeConversion }}%</p>
        </div>
    </div>

    {{-- Gráfico: citas del rango seleccionado --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8" wire:key="chart-citas">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="min-w-0 flex items-center gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                <i class="fas fa-chart-line text-gray-400 shrink-0"></i><span>{{ $chartTitle }}</span>
            </h3>
            <div class="flex flex-wrap items-center gap-2">
                <span class="max-w-full text-[11px] font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded-full px-3 py-1.5 break-words">
                    <i class="fas fa-filter text-gray-400 mr-1"></i>{{ $filtroBadge }}
                </span>
                <span class="text-[13px] font-extrabold text-emerald-600">+{{ $sumAceptadas }}</span>
                <span class="text-[13px] font-extrabold text-red-500">-{{ $sumNoAceptadas }}</span>
                <span class="text-[13px] font-bold text-indigo-600" title="Con orden de servicio">{{ $sumConversion }} OS</span>
            </div>
        </div>

        {{-- Navegación por semana: lunes a viernes --}}
        <div class="flex items-center justify-center gap-3 mb-6 flex-wrap">
            <button type="button" wire:click="semanaAnterior"
                class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 flex items-center justify-center transition shadow-sm"
                title="Semana anterior">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <div class="text-center min-w-[300px] px-5 py-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <div class="text-sm font-extrabold text-gray-800">{{ $periodoLabel }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">
                    @if($esRangoSemanal)Lunes a viernes · @endif{{ $total }} citas
                </div>
            </div>
            <button type="button" wire:click="semanaSiguiente"
                class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 flex items-center justify-center transition shadow-sm"
                title="Semana siguiente">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
            <button type="button" wire:click="semanaActual"
                class="rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold px-4 py-2 transition shadow-sm"
                title="Volver a la semana en curso (lunes a viernes)">
                <i class="fas fa-calendar-week mr-1.5"></i>Esta semana
            </button>
            <button type="button" wire:click="mesActual"
                class="rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-4 py-2 transition shadow-sm"
                title="Volver al rango por defecto: 1 del mes hasta hoy">
                <i class="fas fa-calendar-days mr-1.5"></i>Este mes
            </button>
        </div>

        {{-- El gráfico se dibuja siempre: aunque el rango no tenga citas se mantienen
             las 24 franjas horarias en pantalla en vez de ocultar el canvas. --}}
        <div class="relative w-full" style="height: 300px;" wire:ignore>
            <canvas id="chartCitas"
                data-labels='@json($labels)'
                data-aceptadas='@json($aceptadasPorPeriodo ?? [])'
                data-noaceptadas='@json($noAceptadasPorPeriodo ?? [])'
                data-conversion='@json($conversionPorPeriodo ?? [])'></canvas>
        </div>
        <div class="flex flex-wrap gap-5 mt-4 justify-center text-xs font-semibold text-gray-600">
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm border-2 border-emerald-500 bg-emerald-400"></span> Aceptadas</div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm border-2 border-red-500 bg-red-400"></span> No aceptadas</div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm border-2 border-indigo-500 bg-indigo-400"></span> Con OS (conversión)</div>
        </div>

        @if ($total === 0)
            <div class="mt-4 flex items-center gap-2 text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5">
                <i class="fas fa-circle-info text-gray-400"></i>
                Sin citas en el rango seleccionado. Mové las fechas o usá
                <span class="font-semibold text-indigo-600">Este mes</span> para cargar otro período.
            </div>
        @endif
    </div>

    {{-- Citas por asesor --}}
    @if($porAsesor->count())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8">
        <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2 mb-5">
            <i class="fas fa-user-tie text-gray-400"></i>Citas por asesor
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Asesor</th>
                        <th class="text-center py-3 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="text-center py-3 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Aceptadas</th>
                        <th class="text-center py-3 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Rechazadas</th>
                        <th class="text-center py-3 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Canceladas</th>
                        <th class="text-center py-3 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Con OS</th>
                        <th class="text-center py-3 px-3 text-xs font-bold text-gray-500 uppercase tracking-wider">% Aceptación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porAsesor as $nombre => $dato)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-3 font-medium text-gray-700">{{ $nombre }}</td>
                        <td class="py-3 px-3 text-center font-bold text-gray-800">{{ $dato['total'] }}</td>
                        <td class="py-3 px-3 text-center text-emerald-600">{{ $dato['aceptadas'] }}</td>
                        <td class="py-3 px-3 text-center text-red-600">{{ $dato['rechazadas'] }}</td>
                        <td class="py-3 px-3 text-center text-gray-400">{{ $dato['canceladas'] }}</td>
                        <td class="py-3 px-3 text-center text-indigo-600 font-semibold">{{ $dato['con_orden'] }}</td>
                        <td class="py-3 px-3 text-center">
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8">
        <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2 mb-5">
            <i class="fas fa-building text-gray-400"></i>Citas por sede
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($porSede as $nombre => $dato)
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                <div class="font-semibold text-gray-700 text-sm mb-3">{{ $nombre }}</div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs">
                    <span class="text-gray-600">Total: <strong class="text-gray-800">{{ $dato['total'] }}</strong></span>
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8">
        <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider flex items-center gap-2 mb-5">
            <i class="fas fa-comment-dots text-gray-400"></i>Principales motivos de consulta
        </h3>
        <div class="space-y-2">
            @foreach($motivos as $motivo => $cantidad)
            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                <span class="text-sm text-gray-700 font-medium">{{ $motivo }}</span>
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

        // Carga reutilizable de todos los reportes: js/components/carga-swal.js
        Livewire.on('descargar-pdf', (params) => {
            CargaSwal.exportar({
                url: params.url,
                titulo: 'Exportando PDF',
                texto: 'Generando el reporte...',
                archivo: 'PDF'
            });
        });

        Livewire.on('descargar-excel', (params) => {
            CargaSwal.exportar({
                url: params.url,
                titulo: 'Exportando Excel',
                texto: 'Generando el reporte...',
                archivo: 'Excel'
            });
        });
    </script>
    @endscript
</div>
