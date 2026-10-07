<div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Header --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl w-full shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

            {{-- Título --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-hand-holding-dollar text-indigo-600 text-2xl"></i>
                </div>

                <div>
                    <h2 class="text-gray-800 font-bold text-2xl tracking-tight">Reporte FISE</h2>
                    <p class="text-gray-500 text-sm mt-1">Solicitudes, pagos y rendimiento del programa</p>
                </div>
            </div>

            {{-- Controles y Botones --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-6 w-full lg:w-auto">

                {{-- Filtros de Fecha --}}
                <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-xl border border-gray-200 w-full sm:w-auto">
                    <input
                        type="date"
                        wire:model.live="desde"
                        class="text-sm rounded-lg border-gray-200 bg-white text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2 px-3"
                    >

                    <span class="text-slate-400 text-sm font-medium">a</span>

                    <input
                        type="date"
                        wire:model.live="hasta"
                        class="text-sm rounded-lg border-gray-200 bg-white text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2 px-3"
                    >
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    {{-- Alertas de exportación. --}}
                    <a href="{{ route('fise.reporte.pdf', ['desde' => $desde, 'hasta' => $hasta]) }}"
                       onclick="AppSwal.exportar({ url: this.href, titulo: 'Exportando PDF', texto: 'Generando el reporte, por favor espera...', archivo: 'PDF' }); return false;"
                       class="bg-white hover:bg-red-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 w-full sm:w-auto text-sm">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        PDF
                    </a>

                    <a href="{{ route('fise.reporte.excel', ['desde' => $desde, 'hasta' => $hasta]) }}"
                       onclick="AppSwal.exportar({ url: this.href, titulo: 'Exportando Excel', texto: 'Generando el reporte, por favor espera...', archivo: 'Excel' }); return false;"
                       class="bg-white hover:bg-emerald-50 border border-gray-200 text-gray-700 font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 w-full sm:w-auto text-sm">
                        <i class="fas fa-file-excel text-emerald-500"></i>
                        Excel
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- KPIs Solicitudes --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-wallet text-slate-400"></i>
            Pagos FISE
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total pagos</span>
                    <i class="fas fa-receipt text-slate-200 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-slate-800">{{ $totalPagos }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-emerald-500">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pagados</span>
                    <i class="fas fa-check-circle text-emerald-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-emerald-600">{{ $pagosPagados }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-indigo-500">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Parciales</span>
                    <i class="fas fa-hourglass-half text-indigo-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-indigo-600">{{ $pagosParciales }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-amber-500">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendientes</span>
                    <i class="fas fa-clock text-amber-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-amber-600">{{ $pagosPendientes }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-purple-500">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tasa de pago</span>
                    <i class="fas fa-percent text-purple-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-purple-600">{{ $tasaPago }}%</p>
            </div>

        </div>
    </div>

    {{-- Gráficos: Pagos por día (estados y montos) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Gráfico de pagos por día (estados y tasa de pago) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col h-full" wire:ignore wire:key="chart-pagos-estados-container">

            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-6 flex items-center gap-2">
                <i class="fas fa-chart-column text-slate-400"></i>
                Pagos FISE por día
            </h3>

            @if ($totalPagos > 0)
                <div class="relative w-full flex-grow" style="min-height: 280px;">
                    <canvas
                        id="chartPagosEstados"
                        data-labels='@json($labels)'
                        data-pagados='@json($pagosPagadosPorDia ?? [])'
                        data-parciales='@json($pagosParcialesPorDia ?? [])'
                        data-pendientes='@json($pagosPendientesPorDia ?? [])'
                        data-tasa='@json($tasaPagoPorDia ?? [])'
                    ></canvas>
                </div>

                <div class="flex flex-wrap gap-4 mt-6 justify-center">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-inner"></span>
                        Pagados
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-inner"></span>
                        Parciales
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-amber-500 shadow-inner"></span>
                        Pendientes
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-purple-500 shadow-inner"></span>
                        Tasa de pago
                    </div>
                </div>
            @else
                <x-reportes.empty-state icon="fa-file-circle-question" titulo="Sin datos" mensaje="No hay pagos FISE en este periodo" class="flex-grow" />
            @endif

        </div>

        {{-- Gráfico de pagos por día (montos) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col h-full" wire:ignore wire:key="chart-pagos-fise-container">

            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-6 flex items-center gap-2">
                <i class="fas fa-chart-area text-slate-400"></i>
                Montos FISE por día
            </h3>

            @if ($totalPagos > 0)
                <div class="relative w-full flex-grow" style="min-height: 280px;">
                    <canvas
                        id="chartPagosFise"
                        data-labels='@json($labels)'
                        data-monto-total='@json($montoTotalPorDia ?? [])'
                        data-monto-pagado='@json($montoPagadoPorDia ?? [])'
                        data-saldo-pendiente='@json($saldoPendientePorDia ?? [])'
                    ></canvas>
                </div>

                <div class="flex flex-wrap gap-4 mt-6 justify-center">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-inner"></span>
                        Monto total
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-inner"></span>
                        Monto pagado
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-amber-500 shadow-inner"></span>
                        Saldo pendiente
                    </div>
                </div>
            @else
                <x-reportes.empty-state icon="fa-chart-line" titulo="Sin datos" mensaje="No hay pagos FISE en este periodo" class="flex-grow" />
            @endif

        </div>

    </div>

    {{-- Tabla pagos por técnico --}}
    @if ($pagosPorTecnico && count($pagosPorTecnico) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col h-full mb-6">
            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-user-gear text-slate-400"></i>
                Pagos FISE por técnico
            </h3>

            <div class="overflow-x-auto rounded-xl border border-slate-200 flex-grow">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-semibold uppercase text-xs tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Técnico</th>
                            <th class="py-3 px-4 text-center">Pagos</th>
                            <th class="py-3 px-4 text-right">Monto total</th>
                            <th class="py-3 px-4 text-right">Pagado</th>
                            <th class="py-3 px-4 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pagosPorTecnico as $nombre => $dato)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-medium text-slate-800 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold uppercase">
                                        {{ substr($nombre ?? 'S', 0, 1) }}
                                    </div>
                                    {{ $nombre }}
                                </td>
                                <td class="py-3 px-4 text-center text-slate-600">
                                    <span class="bg-slate-100 text-slate-600 py-0.5 px-2.5 rounded-full text-xs font-bold">
                                        {{ $dato['cantidad'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-indigo-600">
                                    S/ {{ number_format($dato['monto_total'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-medium text-emerald-600">
                                    S/ {{ number_format($dato['monto_pagado'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold {{ ($dato['monto_total'] - $dato['monto_pagado']) > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    S/ {{ number_format($dato['monto_total'] - $dato['monto_pagado'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Tabla pagos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-slate-400"></i>
                Detalle de pagos FISE
            </h3>
            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                {{ $totalPagos }} registros
            </span>
        </div>

        <div class="overflow-x-auto max-h-[400px] overflow-y-auto custom-scrollbar">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200 shadow-sm">
                    <tr>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3">Cliente</th>
                        <th class="px-5 py-3">Vehículo</th>
                        <th class="px-5 py-3 text-right">Monto total</th>
                        <th class="px-5 py-3 text-right">Pagado</th>
                        <th class="px-5 py-3 text-right">Saldo</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3">Registrado por</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @if (count($pagos) > 0)
                        @foreach ($pagos as $pago)
                            @php
                                $colorPago = match ($pago->estado) {
                                    'pagado' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'parcial' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    default => 'bg-slate-50 text-slate-600 border border-slate-200',
                                };
                            @endphp
                            <tr wire:key="pago-{{ $pago->id ?? $loop->index }}" class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5 text-xs text-slate-500">
                                    {{ $pago->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-800">
                                    {{ trim(($pago->serviceOrder->cliente->nombre ?? '') . ' ' . ($pago->serviceOrder->cliente->apellido ?? '')) ?: 'N/A' }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    <span class="border border-slate-200 bg-slate-50 px-2 py-1 rounded text-xs font-mono font-bold">
                                        {{ $pago->serviceOrder->vehiculo->placa ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-indigo-600">
                                    S/ {{ number_format($pago->monto_total, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-medium text-emerald-600">
                                    S/ {{ number_format($pago->monto_pagado, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold {{ ($pago->monto_total -$pago->monto_pagado) > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    S/ {{ number_format($pago->monto_total -$pago->monto_pagado, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center rounded-md {{ $colorPago }} px-2.5 py-1 text-xs font-bold uppercase tracking-wider">
                                        {{ ucfirst($pago->estado) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-600 font-medium">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-bold uppercase">
                                            {{ substr($pago->pagadoPor->name ?? 'U', 0, 1) }}
                                        </div>
                                        {{ $pago->pagadoPor->name ?? 'N/A' }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block text-slate-300"></i>
                                <span class="font-medium">Sin pagos FISE en este periodo.</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @script
    <script>
        function renderChartPagosEstados() {
            if (!window.CHART_DEFS) { console.error('[fise] window.CHART_DEFS no disponible'); return; }
            window.CHART_DEFS.renderFisePagosEstados();
        }

        function renderChartPagosFise() {
            if (!window.CHART_DEFS) { console.error('[fise] window.CHART_DEFS no disponible'); return; }
            window.CHART_DEFS.renderFisePagosMonto();
        }

        renderChartPagosEstados();
        renderChartPagosFise();

        $wire.on('chart-data-fise', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            const canvas = document.getElementById('chartPagosEstados');
            if (!canvas) return;

            canvas.dataset.labels = JSON.stringify(data.labels || []);
            canvas.dataset.pagados = JSON.stringify(data.pagados || []);
            canvas.dataset.parciales = JSON.stringify(data.parciales || []);
            canvas.dataset.pendientes = JSON.stringify(data.pendientes || []);
            canvas.dataset.tasa = JSON.stringify(data.tasa || []);

            renderChartPagosEstados();
        });

        $wire.on('chart-pagos-fise', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            const canvas = document.getElementById('chartPagosFise');
            if (!canvas) return;

            canvas.dataset.labels = JSON.stringify(data.labels || []);
            canvas.dataset.montoTotal = JSON.stringify(data.montoTotal || []);
            canvas.dataset.montoPagado = JSON.stringify(data.montoPagado || []);
            canvas.dataset.saldoPendiente = JSON.stringify(data.saldoPendiente || []);

            renderChartPagosFise();
        });
    </script>
    @endscript

    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</div>
