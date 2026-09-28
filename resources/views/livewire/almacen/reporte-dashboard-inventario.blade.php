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
    <x-almacen.inventario-graficos
        :filtroBadge="$filtroBadge"
        :nivelOk="$nivelOk"
        :nivelBajo="$nivelBajo"
        :nivelSin="$nivelSin"
        :totalEntradas30="$totalEntradas30"
        :totalSalidas30="$totalSalidas30"
    />

    {{-- Tabla distribución --}}
    <x-almacen.inventario-distribucion :distribucion="$distribucion" :sedes="$sedes" />

    <x-almacen.inventario-movimientos :movimientosRecientes="$movimientosRecientes" />

    <x-almacen.inventario-stock-bajo
        :stockBajo="$stockBajo"
        :filtroSede="$filtroSede"
        :sedes="$sedes"
        :sedeStock="$sedeStock"
    />

    <x-almacen.inventario-kits-instalados :kitsInstalados="$kitsInstalados" />

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
