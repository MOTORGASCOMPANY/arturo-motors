<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">

    {{-- Header --}}
    <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl w-full">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                    <i class="fas fa-calendar-check text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-xl leading-tight">Reporte de Citas</h2>
                    <span class="text-slate-400 text-xs">Análisis de agendamiento y conversión</span>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <input type="date" wire:model.live="desde" class="text-sm rounded-lg border-gray-600 bg-white/10 text-white placeholder-gray-400">
                <span class="text-slate-400 text-sm">a</span>
                <input type="date" wire:model.live="hasta" class="text-sm rounded-lg border-gray-600 bg-white/10 text-white placeholder-gray-400">
                <select wire:model.live="sedeId" class="text-sm rounded-lg border-gray-600 bg-white/10 text-white">
                    <option value="todos">Todas las sedes</option>
                    @foreach($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
                <select wire:model.live="estado" class="text-sm rounded-lg border-gray-600 bg-white/10 text-white">
                    <option value="todos">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="aceptada">Aceptada</option>
                    <option value="rechazada">Rechazada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
                <button wire:click="descargarPdf" class="bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg py-2 px-3 transition-colors flex items-center gap-1.5 text-sm">
                    <i class="fas fa-file-pdf text-red-400"></i> PDF
                </button>
                <button wire:click="descargarExcel" class="bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg py-2 px-3 transition-colors flex items-center gap-1.5 text-sm">
                    <i class="fas fa-file-excel text-emerald-400"></i> Excel
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

    {{-- Gráfico de citas por día --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6" wire:ignore wire:key="chart-citas">
        <h3 class="text-sm font-bold text-slate-500 uppercase mb-4">Citas por día</h3>
        @if ($total > 0)
            <div class="relative w-full" style="height: 300px;">
                <canvas id="chartCitas"
                    data-labels='@json($labels)'
                    data-pendientes='@json($pendientesPorDia ?? [])'
                    data-aceptadas='@json($aceptadasPorDia ?? [])'
                    data-rechazadas='@json($rechazadasPorDia ?? [])'
                    data-canceladas='@json($canceladasPorDia ?? [])'></canvas>
            </div>
            <div class="flex flex-wrap gap-4 mt-3 justify-center text-xs">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-500"></span> Pendientes</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-500"></span> Aceptadas</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-500"></span> Rechazadas</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-slate-400"></span> Canceladas</div>
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
            const pendientes = JSON.parse(canvas.dataset.pendientes || '[]');
            const aceptadas = JSON.parse(canvas.dataset.aceptadas || '[]');
            const rechazadas = JSON.parse(canvas.dataset.rechazadas || '[]');
            const canceladas = JSON.parse(canvas.dataset.canceladas || '[]');

            if (window.chartCitasInstance) window.chartCitasInstance.destroy();

            window.chartCitasInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Pendientes', data: pendientes, backgroundColor: '#f59e0b', borderRadius: 3, borderSkipped: false },
                        { label: 'Aceptadas', data: aceptadas, backgroundColor: '#10b981', borderRadius: 3, borderSkipped: false },
                        { label: 'Rechazadas', data: rechazadas, backgroundColor: '#ef4444', borderRadius: 3, borderSkipped: false },
                        { label: 'Canceladas', data: canceladas, backgroundColor: '#94a3b8', borderRadius: 3, borderSkipped: false },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed.y } } },
                    scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } }
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
            canvas.dataset.pendientes = JSON.stringify(data.pendientes);
            canvas.dataset.aceptadas = JSON.stringify(data.aceptadas);
            canvas.dataset.rechazadas = JSON.stringify(data.rechazadas);
            canvas.dataset.canceladas = JSON.stringify(data.canceladas);
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
