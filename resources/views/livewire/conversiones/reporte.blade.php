<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Header (mismo patrón almacén: blanco + acento indigo) --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-car text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-indigo-600 font-bold text-2xl tracking-tight">Reporte de Conversiones GNV</h2>
                        <a href="{{ route('ordenes.listado') }}" wire:navigate
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-full px-3 py-1 transition-colors">
                            <i class="fas fa-clipboard-list"></i>
                            Órdenes
                        </a>
                        <a href="{{ route('almacen.productos.listado') }}" wire:navigate
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-full px-3 py-1 transition-colors">
                            <i class="fas fa-boxes-stacked"></i>
                            Productos
                        </a>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">Conversiones · kits · stock · {{ $filtroBadge }}</p>
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

    {{-- 4 tarjetas de indicadores --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-gauge-high text-slate-400"></i>
            Indicadores de conversión
            <span class="ml-auto inline-flex items-center gap-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1 text-xs font-bold normal-case tracking-normal">
                {{ $filtroBadge }}
            </span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            {{-- 1. Conversiones --}}
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-slate-200/80 border-l-4 border-l-blue-500 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-car text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-slate-800 leading-none">{{ number_format($totalConversiones) }}</p>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Conversiones</span>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Completadas</span>
                        <span class="font-extrabold text-emerald-600">{{ number_format($completadas) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">En proceso</span>
                        <span class="font-extrabold text-amber-600">{{ number_format($enProceso) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Tasa completado</span>
                        <span class="font-extrabold text-indigo-600">{{ $tasaCompletado }}%</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Duración prom.</span>
                        <span class="font-extrabold text-amber-600">{{ $duracionPromedio }}h</span>
                    </div>
                </div>
            </div>

            {{-- 2. Instalados --}}
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-slate-200/80 border-l-4 border-l-cyan-500 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-gears text-cyan-600"></i>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-slate-800 leading-none">{{ number_format($itemsInstalados) }}</p>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Instalados</span>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Con serie</span>
                        <span class="font-extrabold text-cyan-700">{{ number_format($instaladosSerializados) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Por cantidad</span>
                        <span class="font-extrabold text-purple-600">{{ number_format($instaladosCantidad) }}</span>
                    </div>
                    <div class="p-2.5 bg-cyan-50/60 border border-cyan-100 rounded-xl text-xs text-cyan-800 leading-snug">
                        Despachados a órdenes filtradas · serie = con número de serie · cantidad = por unidad
                    </div>
                </div>
            </div>

            {{-- 3. Kits --}}
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-slate-200/80 border-l-4 border-l-indigo-500 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-box text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-slate-800 leading-none">{{ number_format($kitsDisponibles) }}</p>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kits disponibles</span>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Sellados</span>
                        <span class="font-extrabold text-indigo-600">{{ number_format($kitsSellados) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Completados</span>
                        <span class="font-extrabold text-purple-600">{{ number_format($kitsCompletados) }}</span>
                    </div>
                    <div class="p-2.5 bg-indigo-50/60 border border-indigo-100 rounded-xl text-xs text-indigo-800 leading-snug">
                        En stock en almacén · mismos criterios que /almacen/productos
                    </div>
                </div>
            </div>

            {{-- 4. Sueltos --}}
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-slate-200/80 border-l-4 border-l-emerald-500 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <i class="fas fa-puzzle-piece text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-slate-800 leading-none">{{ number_format($piezasSueltas) }}</p>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Piezas sueltas</span>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Con serie</span>
                        <span class="font-extrabold text-emerald-600">{{ number_format($sueltosSerializados) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Por cantidad</span>
                        <span class="font-extrabold text-amber-600">{{ number_format($sueltosCantidadTotal) }}</span>
                    </div>
                    <div class="p-2.5 bg-emerald-50/60 border border-emerald-100 rounded-xl text-xs text-emerald-800 leading-snug">
                        Stock real · cantidad = unidades sueltas menos componentes en kits
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-sliders text-slate-400"></i>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filtros</span>
            <span class="ml-auto inline-flex items-center gap-1.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1 text-xs font-bold">
                {{ $filtroBadge }}
            </span>
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
                    <option value="en_evaluacion">En evaluación</option>
                    <option value="aprobado_conversion">Aprobadas</option>
                    <option value="en_conversion">En proceso</option>
                    <option value="conversion_completada">Completadas</option>
                    <option value="listo_para_entrega">Listas entrega</option>
                    <option value="entregado">Entregadas</option>
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
                <button wire:click="limpiarFiltros"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-full px-3.5 py-2 ml-auto transition-colors">
                    <i class="fas fa-xmark"></i>
                    Limpiar filtros
                </button>
            @endif
        </div>
    </div>

    {{-- Charts row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chart-column text-slate-400"></i>
                    Conversiones por mes
                </h3>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-1">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 280px;" wire:ignore>
                <canvas id="chartConvMes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chart-pie text-slate-400"></i>
                    Por estado
                </h3>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 280px;" wire:ignore>
                <canvas id="chartConvEstado"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <div class="flex items-center justify-between gap-2 mb-2 flex-wrap">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-box-open text-slate-400"></i>
                    Kits en almacén
                </h3>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
            </div>
            <p class="text-xs text-slate-500 mb-4">
                Total <span class="font-extrabold text-slate-800">{{ number_format($kitsTotal) }}</span>
                · Sellados <span class="font-bold text-indigo-600">{{ number_format($kitsSelladosChart) }}</span>
                · Completados <span class="font-bold text-violet-600">{{ number_format($kitsCompletadosChart) }}</span>
                · Asignados a clientes <span class="font-bold text-emerald-600">{{ number_format($kitsAsignadosChart) }}</span>
            </p>
            <div class="relative" style="height: 240px;" wire:ignore>
                <canvas id="chartKitsUsados"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-gears text-slate-400"></i>
                    Componentes instalados
                </h3>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 260px;" wire:ignore>
                <canvas id="chartComponentes"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <div class="flex items-center justify-between gap-2 mb-5">
                <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-truck text-slate-400"></i>
                    Despachados: serie vs cantidad
                </h3>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-1 shrink-0">{{ $filtroBadge }}</span>
            </div>
            <div class="relative" style="height: 260px;" wire:ignore>
                <canvas id="chartDespachados"></canvas>
            </div>
            <p class="text-xs text-slate-400 mt-3">
                Items instalados/despachados a las órdenes filtradas: con número de serie vs por unidad de cantidad.
            </p>
        </div>
    </div>

    {{-- Chart 5: balance --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
        <div class="flex items-center justify-between gap-2 mb-5">
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-warehouse text-slate-400"></i>
                Balance de almacén por sede
            </h3>
            <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-2.5 py-1">{{ $filtroBadge }}</span>
        </div>
        <div class="relative" style="height: 280px;" wire:ignore>
            <canvas id="chartBalanceSedes"></canvas>
        </div>
        <p class="text-xs text-slate-400 mt-3">
            Kits = disponibles (sellados + completados) · Sueltos = mismos criterios que
            <a href="{{ route('almacen.productos.listado') }}" class="text-indigo-600 font-semibold hover:underline">/almacen/productos</a>
            (stock real menos componentes dentro de kits de esa sede).
        </p>
    </div>

    {{-- Tabla detalle --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                <i class="fas fa-list-check text-slate-400"></i>
                Detalle de conversiones
            </h3>
            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                {{ count($detalleOrdenes) }} órdenes · {{ $filtroBadge }}
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
                                @php
                                    $badgeEstado = match ($d['estado']) {
                                        'conversion_completada' => ['Completada', 'emerald'],
                                        'en_conversion' => ['En proceso', 'amber'],
                                        'entregado' => ['Entregada', 'blue'],
                                        'listo_para_entrega' => ['Lista entrega', 'cyan'],
                                        'aprobado_conversion' => ['Aprobada', 'indigo'],
                                        'en_evaluacion' => ['Evaluación', 'slate'],
                                        default => [$d['estado'], 'slate'],
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md border border-{{ $badgeEstado[1] }}-200 bg-{{ $badgeEstado[1] }}-50 px-2.5 py-1 text-xs text-{{ $badgeEstado[1] }}-700 font-bold uppercase tracking-wider">
                                    {{ $badgeEstado[0] }}
                                </span>
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

    {{-- Payload charts: fuera de wire:ignore, se morph con cada filtro --}}
    <div id="reporteConvPayload" class="hidden" aria-hidden="true">@json($charts)</div>

    @push('js')
        <script src="{{ asset('js/components/reporte-conversiones-charts.js') }}"></script>
    @endpush

    @script
    <script>
        (function () {
            function go() {
                if (typeof window.renderReporteConvCharts === 'function') {
                    window.renderReporteConvCharts();
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
</div>
