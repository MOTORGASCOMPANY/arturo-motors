<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-10 space-y-6">

    <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl w-full">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                    <i class="fas fa-warehouse text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-xl leading-tight">Reporte de almacén</h2>
                    <span class="text-slate-400 text-xs">Stock actual en tiempo real · Todas las sedes</span>
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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                <i class="fas fa-boxes-stacked text-blue-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 leading-none">{{ number_format($totalItems) }}</p>
                <span class="text-xs text-slate-500">Total de items</span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                <i class="fas fa-circle-check text-emerald-600"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 leading-none">{{ number_format($productosConStock) }}</p>
                <span class="text-xs text-slate-500">
                    Productos con stock
                    @if ($totalItems > 0)
                        · {{ round(($productosConStock / $totalItems) * 100) }}%
                    @endif
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl border {{ $stockBajo->count() > 0 ? 'border-red-200' : 'border-slate-200' }} p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg {{ $stockBajo->count() > 0 ? 'bg-red-50' : 'bg-slate-50' }} flex items-center justify-center shrink-0">
                <i class="fas fa-triangle-exclamation {{ $stockBajo->count() > 0 ? 'text-red-600' : 'text-slate-400' }}"></i>
            </div>
            <div>
                <p class="text-2xl font-bold {{ $stockBajo->count() > 0 ? 'text-red-600' : 'text-slate-800' }} leading-none">
                    {{ $stockBajo->count() }}
                </p>
                <span class="text-xs text-slate-500">Productos en stock bajo</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-slate-400 flex items-center gap-1.5 mr-1">
            <i class="fas fa-sliders"></i> Filtros
        </span>

        <div class="flex items-center gap-1.5">
            <label for="filtroSede" class="text-xs text-slate-500">Sede</label>
            <select id="filtroSede" wire:model.live="filtroSede"
                class="rounded-lg border-slate-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Todas</option>
                @foreach ($sedes as $s)
                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-1.5">
            <label for="filtroStock" class="text-xs text-slate-500">Stock</label>
            <select id="filtroStock" wire:model.live="filtroStock"
                class="rounded-lg border-slate-200 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="todos">Todos</option>
                <option value="con_stock">Con stock</option>
                <option value="sin_stock">Sin stock</option>
                <option value="stock_bajo">Stock bajo</option>
            </select>
        </div>

        @if ($filtroSede || $filtroStock !== 'todos')
            <button wire:click="$set('filtroSede', null); $set('filtroStock', 'todos')"
                class="text-xs text-red-600 hover:text-red-800 font-semibold ml-auto flex items-center gap-1">
                <i class="fas fa-xmark"></i> Limpiar filtros
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 p-5" wire:ignore>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Stock por sede</h3>
                <span class="text-xs text-slate-400">unidades</span>
            </div>
            <div class="relative" style="height: 260px;">
                <canvas id="chartStockSedes"></canvas>
                <div id="emptySedes" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                    Sin datos para mostrar
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5" wire:ignore>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-700">Stock por categoría</h3>
            </div>
            <div class="relative" style="height: 260px;">
                <canvas id="chartStockCategorias"></canvas>
                <div id="emptyCategorias" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                    Sin datos para mostrar
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 text-sm">Distribución de stock por sede</h3>
            <span class="text-xs text-slate-400">{{ count($distribucion) }} productos</span>
        </div>
        <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Producto</th>
                        <th class="px-4 py-2.5 text-left">Categoría</th>
                        @foreach ($sedes as $s)
                            <th class="px-4 py-2.5 text-right">{{ $s->nombre }}</th>
                        @endforeach
                        <th class="px-4 py-2.5 text-right font-bold text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($distribucion as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-2.5 font-medium text-slate-700">{{ $row['producto']->nombre }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
                                    {{ $row['producto']->categoria->nombre }}
                                </span>
                            </td>
                            @foreach ($sedes as $s)
                                <td class="px-4 py-2.5 text-right tabular-nums {{ $row['por_sede'][$s->id] > 0 ? 'text-slate-700' : 'text-slate-300' }}">
                                    {{ number_format($row['por_sede'][$s->id]) }}
                                </td>
                            @endforeach
                            <td class="px-4 py-2.5 text-right font-bold text-slate-800 tabular-nums">{{ number_format($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $sedes->count() + 3 }}" class="px-4 py-10 text-center text-slate-400">
                                <i class="fas fa-inbox text-xl mb-2 block"></i>
                                Sin resultados para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($stockBajo->count())
        <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
            <div class="p-4 border-b border-red-100 bg-red-50/50 flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-amber-500"></i>
                <h3 class="font-semibold text-slate-800 text-sm">
                    Stock bajo{{ $filtroSede ? ' · ' . optional($sedes->firstWhere('id', $filtroSede))->nombre : '' }}
                </h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2.5 text-left">Producto</th>
                        <th class="px-4 py-2.5 text-right">Disponible</th>
                        <th class="px-4 py-2.5 text-right">Mínimo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($stockBajo as $p)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-slate-700">{{ $p->nombre }}</td>
                            <td class="px-4 py-2.5 text-right text-red-600 font-semibold tabular-nums">{{ $p->stockEnSede($filtroSede ?: 1) }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-500 tabular-nums">{{ $p->stock_minimo }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- KITS INSTALADOS — Historial de conversiones por vehículo --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if ($kitsInstalados->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-car text-slate-400 text-sm"></i>
                    <h3 class="font-semibold text-slate-800 text-sm">Kits instalados por vehículo</h3>
                </div>
                <span class="text-xs text-slate-400">{{ $kitsInstalados->count() }} conversiones</span>
            </div>
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-2.5 text-left">Cliente</th>
                            <th class="px-4 py-2.5 text-left">Vehículo</th>
                            <th class="px-4 py-2.5 text-left">Placa</th>
                            <th class="px-4 py-2.5 text-left">Kit</th>
                            <th class="px-4 py-2.5 text-left">Gen.</th>
                            <th class="px-4 py-2.5 text-left">Items Serializados</th>
                            <th class="px-4 py-2.5 text-center">Técnico</th>
                            <th class="px-4 py-2.5 text-left">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($kitsInstalados as $ki)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-slate-700">{{ $ki['cliente'] }}</td>
                                <td class="px-4 py-2.5 text-slate-600">{{ $ki['vehiculo'] }}</td>
                                <td class="px-4 py-2.5 font-bold text-slate-800">{{ $ki['placa'] }}</td>
                                <td class="px-4 py-2.5 text-xs text-slate-600">{{ $ki['kit']->producto->nombre }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-0.5 text-xs text-purple-700 font-semibold">
                                        {{ $ki['generacion'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    @forelse ($ki['seriales'] as $s)
                                        <div class="text-xs leading-relaxed">
                                            <span class="font-medium text-slate-700">{{ $s['nombre'] }}:</span>
                                            <span class="font-mono text-slate-500">{{ $s['serie'] }}</span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-300">—</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-2.5 text-center text-xs text-slate-500">{{ $ki['tecnico'] }}</td>
                                <td class="px-4 py-2.5 text-xs text-slate-400">{{ $ki['fecha'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script>
        function renderCharts() {
            const sedeColors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
            const categoriaColors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ec4899', '#64748b'];

            // Gráfico de barras: Stock por sede
            const ctxSedes = document.getElementById('chartStockSedes');
            const dataSedes = @json($dataSedes);
            const emptySedes = document.getElementById('emptySedes');
            if (ctxSedes) {
                if (window.chartSedes) window.chartSedes.destroy();
                const hasData = dataSedes.some(v => v > 0);
                emptySedes.classList.toggle('hidden', hasData);
                ctxSedes.classList.toggle('hidden', !hasData);
                if (hasData) {
                    window.chartSedes = new Chart(ctxSedes, {
                        type: 'bar',
                        data: {
                            labels: @json($labelsSedes),
                            datasets: [{
                                label: 'Items',
                                data: dataSedes,
                                backgroundColor: sedeColors,
                                borderRadius: 6,
                                maxBarThickness: 48
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => ` ${ctx.formattedValue} unidades`
                                    }
                                }
                            },
                            scales: {
                                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }

            // Gráfico de dona: Stock por categoría
            const ctxCategorias = document.getElementById('chartStockCategorias');
            const dataCategorias = @json($dataCategorias);
            const emptyCategorias = document.getElementById('emptyCategorias');
            if (ctxCategorias) {
                if (window.chartCategorias) window.chartCategorias.destroy();
                const hasData = dataCategorias.some(v => v > 0);
                emptyCategorias.classList.toggle('hidden', hasData);
                ctxCategorias.classList.toggle('hidden', !hasData);
                if (hasData) {
                    window.chartCategorias = new Chart(ctxCategorias, {
                        type: 'doughnut',
                        data: {
                            labels: @json($labelsCategorias),
                            datasets: [{
                                data: dataCategorias,
                                backgroundColor: categoriaColors,
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { boxWidth: 10, padding: 12, font: { size: 11 } }
                                }
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
        window.exportarPDF = function() {
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

        window.exportarExcel = function() {
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