<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Header --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-warehouse text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Dashboard de almacén</h2>
                    <p class="text-gray-500 text-sm mt-1">Kits, piezas y stock · <span class="font-medium text-indigo-600">{{ $sedeLabel }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                {{-- Exportaciones con el componente de carga reutilizable (js/components/carga-swal.js) --}}
                <a href="{{ route('ReporteAlmacen.Pdf', ['sede_id' => $sedeActual]) }}"
                   onclick="CargaSwal.exportar({ url: this.href, titulo: 'Exportando PDF', texto: 'Generando el reporte, por favor espera...', archivo: 'PDF' }); return false;"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 border border-red-700 shadow-sm transition-colors">
                    <i class="fas fa-file-pdf text-[14px]"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('ReporteAlmacen.Excel', ['sede_id' => $sedeActual]) }}"
                   onclick="CargaSwal.exportar({ url: this.href, titulo: 'Exportando Excel', texto: 'Generando el reporte, por favor espera...', archivo: 'Excel' }); return false;"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-700 shadow-sm transition-colors">
                    <i class="fas fa-file-excel text-[14px]"></i>
                    <span>Excel</span>
                </a>
                <a href="{{ route('almacen.productos.listado') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 border border-orange-700 shadow-sm transition-colors"
                    target="_blank">
                    <i class="fas fa-boxes-stacked text-[14px]"></i>
                    <span>Ver productos</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Selector de sede + badge --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-map-marker-alt text-slate-400"></i>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sede</span>
            <span class="ml-auto inline-flex items-center gap-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1 text-xs font-bold">
                <i class="fas fa-filter text-[10px]"></i>
                {{ $sedeLabel }}
            </span>
        </div>
        <x-almacen.inventario-selector-sede :sedes="$sedes" :filtroSede="$sedeActual" :sedeLabel="$sedeLabel" :showBadge="false" />
    </div>

    {{-- 2 KPIs en grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-5">
        <x-almacen.inventario-kpi-consumo :tasa="$tasa" />
        <x-almacen.inventario-kpi-alertas :alertas="$alertas" />
    </div>

    {{-- Gráfico de barras --}}
    <x-almacen.inventario-grafico-barras :grafico="$grafico" :sedeActual="$sedeActual" />

    {{-- Tabla de productos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-2">
                <i class="fas fa-list text-slate-400"></i>
                Productos en {{ $sedeLabel }}
            </h3>
            <p class="text-xs text-slate-400">Stock suelto disponible (kits = unidades; serializados = items sueltos; cantidad = stock - en kits)</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider w-28">Stock</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider w-28">Mínimo</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-36">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($productos as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-slate-800">{{ $p['nombre'] }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-slate-500">{{ $p['categoria'] }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-xl font-bold tabular-nums {{ $p['sin_stock'] ? 'text-red-600' : ($p['es_bajo'] ? 'text-amber-600' : 'text-emerald-600') }}">{{ $p['stock'] }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-sm text-slate-500">{{ $p['stock_minimo'] ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if ($p['sin_stock'])
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full">
                                        <i class="fas fa-times-circle text-[10px]"></i> Sin stock
                                    </span>
                                @elseif ($p['es_bajo'])
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-full">
                                        <i class="fas fa-exclamation-triangle text-[10px]"></i> Stock bajo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full">
                                        <i class="fas fa-check-circle text-[10px]"></i> OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-box-open text-3xl mb-2"></i>
                                <p>No hay productos con stock, alertas o stock bajo en esta sede</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Resumen al pie --}}
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                <span>
                    Total: <strong class="text-slate-700">{{ $productos->count() }}</strong> productos
                </span>
                <span class="flex items-center gap-4">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-circle text-red-500 text-[8px]"></i> Sin stock: <strong>{{ $productos->where('sin_stock', true)->count() }}</strong>
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-circle text-amber-500 text-[8px]"></i> Stock bajo: <strong>{{ $productos->where('es_bajo', true)->count() }}</strong>
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-circle text-emerald-500 text-[8px]"></i> OK: <strong>{{ $productos->where('sin_stock', false)->where('es_bajo', false)->count() }}</strong>
                    </span>
                </span>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('js/components/reporte-charts.js') }}"></script>
    @endpush

    @script
    <script>
        (function() {
            // Auto-hide skeletons when data arrives
            const observer = new MutationObserver(() => {
                document.querySelectorAll('[x-data]').forEach(el => {
                    if (el.__alpine) return;
                });
            });
        })();
    </script>
    @endscript
</div>