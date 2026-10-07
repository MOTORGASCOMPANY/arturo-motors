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
                    @if($modo === 'semana')Lunes a viernes · @endif{{ $total }} citas
                </div>
            </div>
            <button type="button" wire:click="semanaSiguiente"
                class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 flex items-center justify-center transition shadow-sm"
                title="Semana siguiente">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
            <button type="button" wire:click="semanaActual"
                aria-pressed="{{ $modo === 'semana' ? 'true' : 'false' }}"
                @class([
                    'rounded-xl border text-xs font-semibold px-4 py-2 transition shadow-sm',
                    'border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700' => $modo === 'semana',
                    'border-gray-200 bg-white hover:bg-gray-50 text-gray-700' => $modo !== 'semana',
                ])
                title="Volver a la semana en curso (lunes a viernes)">
                <i class="fas fa-calendar-week mr-1.5"></i>Esta semana
            </button>
            <button type="button" wire:click="mesActual"
                aria-pressed="{{ $modo === 'mes' ? 'true' : 'false' }}"
                @class([
                    'rounded-xl border text-xs font-semibold px-4 py-2 transition shadow-sm',
                    'border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700' => $modo === 'mes',
                    'border-gray-200 bg-white hover:bg-gray-50 text-gray-700' => $modo !== 'mes',
                ])
                title="Ver el mes completo (1 al último día)">
                <i class="fas fa-calendar-days mr-1.5"></i>Este mes
            </button>
        </div>

        {{-- El gráfico se dibuja siempre: aunque el rango no tenga citas se mantienen
             los puntos del período (un punto por día del mes, o lunes a viernes
             en modo semana) en pantalla en vez de ocultar el canvas. --}}
        <div class="relative w-full" style="height: 300px;" wire:ignore>
            <canvas id="chartCitas"
                data-labels='@json($labels)'
                data-aceptadas='@json($aceptadasPorPeriodo ?? [])'
                data-noaceptadas='@json($noAceptadasPorPeriodo ?? [])'
                data-conversion='@json($conversionPorPeriodo ?? [])'></canvas>
            <div id="empty-chartCitas" class="hidden absolute inset-0 z-10 flex flex-col items-center justify-center p-4 text-center bg-white">
                <x-reportes.empty-state icon="fa-calendar-check" titulo="Sin datos para este período" mensaje="No hay citas en el rango seleccionado" />
            </div>
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

    {{-- Ratios del rango seleccionado: aceptación y rechazo día a día,
         enlazados al filtro de fechas (mismos labels que el gráfico principal) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8" wire:key="ratios-citas">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="min-w-0 flex items-center gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                <i class="fas fa-percent text-gray-400 shrink-0"></i><span>Ratios del período</span>
            </h3>
            <span class="max-w-full text-[11px] font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded-full px-3 py-1.5 break-words">
                <i class="fas fa-calendar-day text-gray-400 mr-1"></i>{{ $periodoLabel }}
            </span>
        </div>

        @if ($total > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ratio de aceptación</span>
                        <span class="text-xs font-extrabold text-emerald-600">{{ $porcentajeAceptacion }}%</span>
                    </div>
                    <div class="relative w-full" style="height: 240px;" wire:ignore>
                        <canvas id="chartRatioAceptadas"
                            data-labels='@json($labels)'
                            data-valores='@json($ratioAceptadas)'
                            data-color="#10b981"
                            data-fillcolor="rgba(16, 185, 129, 0.15)"></canvas>
                        <div id="empty-chartRatioAceptadas" class="hidden absolute inset-0 z-10 flex flex-col items-center justify-center p-4 text-center bg-white">
                            <x-reportes.empty-state icon="fa-chart-line" titulo="Sin datos de aceptación en el período" />
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ratio de rechazo</span>
                        <span class="text-xs font-extrabold text-red-500">{{ $porcentajeRechazo }}%</span>
                    </div>
                    <div class="relative w-full" style="height: 240px;" wire:ignore>
                        <canvas id="chartRatioRechazadas"
                            data-labels='@json($labels)'
                            data-valores='@json($ratioRechazadas)'
                            data-color="#ef4444"
                            data-fillcolor="rgba(239, 68, 68, 0.12)"></canvas>
                        <div id="empty-chartRatioRechazadas" class="hidden absolute inset-0 z-10 flex flex-col items-center justify-center p-4 text-center bg-white">
                            <x-reportes.empty-state icon="fa-chart-line" titulo="Sin datos de rechazo en el período" />
                        </div>
                    </div>
                </div>
            </div>
        @else
            <x-reportes.empty-state icon="fa-percent" titulo="Sin datos para este período" mensaje="No hay citas en el rango seleccionado." />
        @endif
    </div>

    {{-- Citas por vendedor: barras apiladas por estado (versión visual de la
         tabla de abajo). El gráfico ignora el filtro de asesor a propósito
         (facet): siempre muestra todos los vendedores para poder cambiar de
         selección; clic en una barra filtra el reporte completo por ese
         vendedor y clic de nuevo quita el filtro. --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 lg:p-8" wire:key="chart-vendedores">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="min-w-0 flex items-center gap-2 text-sm font-bold text-gray-600 uppercase tracking-wider">
                <i class="fas fa-chart-column text-gray-400 shrink-0"></i><span>Citas por vendedor</span>
                <span class="text-[11px] font-semibold text-gray-400 normal-case tracking-normal ml-1">Clic en una barra para filtrar</span>
            </h3>
            @if($asesorKey !== 'todos')
                <button type="button" wire:click="limpiarAsesor"
                    class="inline-flex items-center gap-2 max-w-full text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-full px-3 py-1.5 hover:bg-indigo-100 transition shadow-sm">
                    <i class="fas fa-filter"></i>
                    Vendedor: {{ $asesorSeleccionado ?? 'seleccionado' }}
                    <i class="fas fa-times"></i>
                </button>
            @else
                <span class="max-w-full text-[11px] font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded-full px-3 py-1.5 break-words">
                    <i class="fas fa-users text-gray-400 mr-1"></i>Todos los vendedores
                </span>
            @endif
        </div>

        <div class="relative w-full" style="height: 380px;" wire:ignore>
            <canvas id="chartAsesores"
                data-labels='@json($labelsAsesores)'
                data-claves='@json($clavesAsesores)'
                data-aceptadas='@json($asesorAceptadas)'
                data-pendientes='@json($asesorPendientes)'
                data-rechazadas='@json($asesorRechazadas)'
                data-canceladas='@json($asesorCanceladas)'
                data-seleccionado="{{ $asesorKey }}"></canvas>
            <div id="empty-chartAsesores" class="hidden absolute inset-0 z-10 flex flex-col items-center justify-center p-4 text-center bg-white">
                <x-reportes.empty-state icon="fa-users" titulo="Sin datos para este período" mensaje="Sin vendedores con citas en el rango" />
            </div>
        </div>

        @if($labelsAsesores->isEmpty())
            <p class="text-xs text-gray-400 text-center mt-3">Sin citas con vendedor asignado en el rango seleccionado.</p>
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
        window.renderTodosLosCharts = function () {
            const defs = window.CHART_DEFS;
            if (!defs) {
                console.error('[citas] window.CHART_DEFS no disponible');
                return;
            }
            defs.renderCitas();
            defs.renderRatioCitas('chartRatioAceptadasInstance', 'chartRatioAceptadas');
            defs.renderRatioCitas('chartRatioRechazadasInstance', 'chartRatioRechazadas');
            defs.renderCitasAsesores(function (clave, seleccionado) {
                $wire.set('asesorKey', clave === seleccionado ? 'todos' : clave);
            });
        };

        window.renderTodosLosCharts();

        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'reportes.reporte-citas') window.renderTodosLosCharts();
        });

        $wire.on('chart-data-citas', (data) => {
            const canvas = document.getElementById('chartCitas');
            if (canvas) {
                canvas.dataset.labels = JSON.stringify(data.labels);
                canvas.dataset.aceptadas = JSON.stringify(data.aceptadas);
                canvas.dataset.noaceptadas = JSON.stringify(data.noAceptadas);
                canvas.dataset.conversion = JSON.stringify(data.conversion || []);
            }

            const ratioAceptadas = document.getElementById('chartRatioAceptadas');
            if (ratioAceptadas) {
                ratioAceptadas.dataset.labels = JSON.stringify(data.labels);
                ratioAceptadas.dataset.valores = JSON.stringify(data.ratioAceptadas || []);
            }

            const ratioRechazadas = document.getElementById('chartRatioRechazadas');
            if (ratioRechazadas) {
                ratioRechazadas.dataset.labels = JSON.stringify(data.labels);
                ratioRechazadas.dataset.valores = JSON.stringify(data.ratioRechazadas || []);
            }

            window.renderTodosLosCharts();
        });

        $wire.on('chart-data-asesores', (data) => {
            const canvas = document.getElementById('chartAsesores');
            if (!canvas) return;
            canvas.dataset.labels = JSON.stringify(data.labels);
            canvas.dataset.claves = JSON.stringify(data.claves);
            canvas.dataset.aceptadas = JSON.stringify(data.aceptadas);
            canvas.dataset.pendientes = JSON.stringify(data.pendientes);
            canvas.dataset.rechazadas = JSON.stringify(data.rechazadas);
            canvas.dataset.canceladas = JSON.stringify(data.canceladas);
            canvas.dataset.seleccionado = data.seleccionado || 'todos';
            window.renderChartAsesores();
        });

        // Alertas de exportación.
        Livewire.on('descargar-pdf', (params) => {
            AppSwal.exportar({
                url: params.url,
                titulo: 'Exportando PDF',
                texto: 'Generando el reporte...',
                archivo: 'PDF'
            });
        });

        Livewire.on('descargar-excel', (params) => {
            AppSwal.exportar({
                url: params.url,
                titulo: 'Exportando Excel',
                texto: 'Generando el reporte...',
                archivo: 'Excel'
            });
        });
    </script>
    @endscript
</div>
