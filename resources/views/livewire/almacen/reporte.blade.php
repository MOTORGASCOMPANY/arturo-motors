<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Header --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-warehouse text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Dashboard de almacén</h2>
                        <a href="{{ route('almacen.productos.listado') }}"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-full px-3 py-1 transition-colors">
                            <i class="fas fa-boxes-stacked"></i>
                            Productos
                        </a>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Stock, kits y movimientos · {{ $sedeLabel }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                <button type="button" onclick="exportarPDF()"
                    class="bg-white hover:bg-red-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-file-pdf text-red-500"></i>
                    PDF
                </button>
                <button type="button" onclick="exportarExcel()"
                    class="bg-white hover:bg-emerald-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-file-excel text-emerald-500"></i>
                    Excel
                </button>
            </div>
        </div>
    </div>

    {{-- Filtros (afectan KPIs, gráficos y tablas) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-sliders text-slate-400"></i>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filtros</span>
            <span class="ml-auto inline-flex items-center gap-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1 text-xs font-bold">
                <i class="fas fa-filter text-[10px]"></i>
                {{ $filtroBadge }}
            </span>
        </div>

        <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1.5">
                <label for="filtroSede" class="text-xs font-semibold text-slate-500">Sede</label>
                <select id="filtroSede" wire:model.live="filtroSede"
                    class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 min-w-[10rem]">
                    <option value="">Todas</option>
                    @foreach ($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="filtroStock" class="text-xs font-semibold text-slate-500">Stock</label>
                <select id="filtroStock" wire:model.live="filtroStock"
                    class="rounded-xl border-slate-200 text-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 min-w-[10rem]">
                    <option value="todos">Todos</option>
                    <option value="con_stock">Con stock</option>
                    <option value="sin_stock">Sin stock</option>
                    <option value="stock_bajo">Stock bajo</option>
                </select>
            </div>

            @if ($filtroSede || $filtroStock !== 'todos')
                <button type="button" wire:click="limpiarFiltros"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-full px-3.5 py-2 ml-auto transition-colors">
                    <i class="fas fa-xmark"></i>
                    Limpiar filtros
                </button>
            @endif
        </div>
    </div>

    {{-- KPIs --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-gauge-high text-slate-400"></i>
            Indicadores generales
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <x-almacen.reporte-kpi
                :value="number_format($totalItems)"
                label="Total de items"
                icon="fa-boxes-stacked"
                color="blue"
            />
            <x-almacen.reporte-kpi
                :value="number_format($productosConStock)"
                :label="'Productos con stock · ' . $porcentajeStock . '%'"
                icon="fa-circle-check"
                color="emerald"
            />
            <x-almacen.reporte-kpi
                :value="$stockBajo->count()"
                label="Productos en stock bajo"
                icon="fa-triangle-exclamation"
                :color="$stockBajo->count() > 0 ? 'red' : 'slate'"
            />
            <x-almacen.reporte-kpi
                :value="number_format($valorTotal, 0, ',', '.')"
                label="Valor del inventario"
                icon="fa-sack-dollar"
                color="indigo"
                prefix="S/ "
            />
        </div>
    </div>

    {{-- 6 gráficos --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-chart-simple text-slate-400"></i>
            Gráficos
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- 1. Stock por sede --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-column text-slate-400"></i>
                        Stock por sede
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartStockSedes"></canvas>
                    <div id="emptySedes" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 2. Stock por categoría --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-pie text-slate-400"></i>
                        Stock por categoría
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartStockCategorias"></canvas>
                    <div id="emptyCategorias" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 3. Top productos --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-ranking-star text-slate-400"></i>
                        Top productos
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartTopProductos"></canvas>
                    <div id="emptyTop" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 4. Nivel de stock --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-gauge text-slate-400"></i>
                        Nivel de stock
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }} · OK {{ $nivelOk }} / Bajo {{ $nivelBajo }} / Sin {{ $nivelSin }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartNivelStock"></canvas>
                    <div id="emptyNivel" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 5. Estados de kits --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-box-open text-slate-400"></i>
                        Estados de kits
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartKitsEstado"></canvas>
                    <div id="emptyKitsEstado" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin datos para mostrar
                    </div>
                </div>
            </div>

            {{-- 6. Entradas vs salidas 30 días --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                <div class="flex items-center justify-between gap-2 mb-5 flex-wrap">
                    <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-arrow-trend-up text-slate-400"></i>
                        Entradas vs salidas · 30 días
                    </h3>
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-2.5 py-1 text-[11px] font-bold shrink-0">
                        <i class="fas fa-filter text-[9px]"></i>
                        {{ $filtroBadge }}
                        <span class="text-slate-400 font-normal">·</span>
                        <span class="text-emerald-600">+{{ number_format($totalEntradas30) }}</span>
                        <span class="text-slate-400 font-normal">/</span>
                        <span class="text-red-500">-{{ number_format($totalSalidas30) }}</span>
                    </span>
                </div>
                <div wire:ignore class="relative" style="height: 260px;">
                    <canvas id="chartMovDias"></canvas>
                    <div id="emptyMovDias" class="hidden absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Sin movimientos en el período
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla distribución --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                <i class="fas fa-table-cells text-slate-400"></i>
                Distribución de stock por sede
            </h3>
            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                {{ count($distribucion) }} productos
            </span>
        </div>
        <div class="overflow-x-auto max-h-[420px] overflow-y-auto custom-scrollbar">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200 shadow-sm">
                    <tr>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3">Categoría</th>
                        @foreach ($sedes as $s)
                            <th class="px-4 py-3 text-right">{{ $s->nombre }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-right font-bold text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($distribucion as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-medium text-slate-700">{{ $row['producto']->nombre }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs text-slate-600 font-semibold">
                                    {{ $row['producto']->categoria->nombre }}
                                </span>
                            </td>
                            @foreach ($sedes as $s)
                                <td class="px-4 py-3.5 text-right tabular-nums {{ ($row['por_sede'][$s->id] ?? 0) > 0 ? 'text-slate-700 font-medium' : 'text-slate-300' }}">
                                    {{ number_format($row['por_sede'][$s->id] ?? 0) }}
                                </td>
                            @endforeach
                            <td class="px-4 py-3.5 text-right font-bold text-slate-800 tabular-nums">{{ number_format($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $sedes->count() + 3 }}" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block text-slate-300"></i>
                                <span class="font-medium">Sin resultados para los filtros seleccionados.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($movimientosRecientes->count())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3">
                <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                    <i class="fas fa-arrows-turn-to-dots text-slate-400"></i>
                    Últimos movimientos de stock
                </h3>
                <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                    Últimos 15
                </span>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Sede</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3">Motivo</th>
                            <th class="px-4 py-3">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($movimientosRecientes as $mov)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 text-xs text-slate-500">{{ $mov['fecha'] }}</td>
                                <td class="px-4 py-3.5 font-medium text-slate-700">{{ $mov['producto'] }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $mov['sede'] }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center rounded-md border {{ $mov['tipo'] === 'entrada' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-600' }} px-2.5 py-1 text-xs font-bold uppercase tracking-wider">
                                        {{ ucfirst($mov['tipo']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold tabular-nums {{ $mov['tipo'] === 'entrada' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $mov['tipo'] === 'entrada' ? '+' : '-' }}{{ $mov['cantidad'] }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">{{ $mov['motivo'] ?: '—' }}</td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">{{ $mov['usuario'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($stockBajo->count())
        <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
            <div class="p-5 border-b border-red-100 bg-red-50/50 flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-amber-500"></i>
                <h3 class="font-bold text-slate-700 text-base">
                    Stock bajo{{ $filtroSede ? ' · ' . optional($sedes->firstWhere('id', $filtroSede))->nombre : '' }}
                </h3>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 text-right">Disponible</th>
                            <th class="px-4 py-3 text-right">Mínimo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($stockBajo as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-slate-700">{{ $p->nombre }}</td>
                                <td class="px-4 py-3.5 text-right text-red-600 font-bold tabular-nums">{{ $p->stockSueltoEnSede($sedeStock) }}</td>
                                <td class="px-4 py-3.5 text-right text-slate-500 tabular-nums">{{ $p->stock_minimo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($kitsInstalados->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3">
                <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                    <i class="fas fa-car text-slate-400"></i>
                    Kits instalados por vehículo
                </h3>
                <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                    {{ $kitsInstalados->count() }} conversiones
                </span>
            </div>
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200 shadow-sm">
                        <tr>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Vehículo</th>
                            <th class="px-4 py-3">Placa</th>
                            <th class="px-4 py-3">Kit</th>
                            <th class="px-4 py-3">Gen.</th>
                            <th class="px-4 py-3">Items Serializados</th>
                            <th class="px-4 py-3 text-center">Técnico</th>
                            <th class="px-4 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($kitsInstalados as $ki)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-slate-700">{{ $ki['cliente'] }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $ki['vehiculo'] }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="border border-slate-200 bg-slate-50 px-2 py-1 rounded text-xs font-mono font-bold text-slate-800">
                                        {{ $ki['placa'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-600">{{ $ki['kit']->producto->nombre }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center rounded-md border border-purple-200 bg-purple-50 px-2 py-1 text-xs text-purple-700 font-bold">
                                        {{ $ki['generacion'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @forelse ($ki['seriales'] as $s)
                                        <div class="text-xs leading-relaxed bg-slate-50 rounded-md px-2 py-1 mb-1 last:mb-0">
                                            <span class="font-semibold text-slate-700">{{ $s['nombre'] }}:</span>
                                            <span class="font-mono text-slate-500">{{ $s['serie'] }}</span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-300">—</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs text-slate-500">{{ $ki['tecnico'] }}</td>
                                <td class="px-4 py-3.5 text-xs text-slate-400">{{ $ki['fecha'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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

    {{-- Payload de charts: DEBE estar fuera de wire:ignore y re-renderizar con Livewire.
         @script solo corre 1 vez en LW3; este div sí se actualiza en cada filtro. --}}
    <div id="reportePayload" class="hidden" aria-hidden="true">@json($charts)</div>

    @push('js')
        <script src="{{ asset('js/components/reporte-charts.js') }}"></script>
    @endpush

    @script
    <script>
        (function () {
            function syncPayload() {
                var el = document.getElementById('reportePayload');
                if (el && el.textContent) {
                    try { window.reporteData = JSON.parse(el.textContent); } catch (e) { /* keep old */ }
                }
            }
            syncPayload();
            function go() {
                if (typeof window.renderReporteCharts === 'function') {
                    syncPayload();
                    window.renderReporteCharts();
                    return true;
                }
                return false;
            }
            if (!go()) {
                var tries = 0;
                var wait = setInterval(function () {
                    if (go() || ++tries > 50) clearInterval(wait);
                }, 40);
            }
        })();
    </script>
    @endscript
</div>
