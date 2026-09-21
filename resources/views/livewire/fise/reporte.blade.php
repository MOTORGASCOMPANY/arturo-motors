<div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8 font-sans">

    {{-- Header --}}
    <div class="bg-slate-900 p-6 sm:p-8 rounded-2xl w-full shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

            {{-- Título --}}
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/10 border border-white/5 flex items-center justify-center shrink-0 shadow-inner">
                    <i class="fas fa-hand-holding-dollar text-white text-2xl"></i>
                </div>

                <div>
                    <h2 class="text-white font-bold text-2xl tracking-tight">Reporte FISE</h2>
                    <p class="text-slate-400 text-sm mt-1">Solicitudes, pagos y rendimiento del programa</p>
                </div>
            </div>

            {{-- Controles y Botones --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-6 w-full lg:w-auto">

                {{-- Filtros de Fecha --}}
                <div class="flex items-center gap-3 bg-white/5 p-2 rounded-xl border border-white/10 w-full sm:w-auto">
                    <input
                        type="date"
                        wire:model.live="desde"
                        class="text-sm rounded-lg border-transparent focus:border-slate-500 focus:ring-slate-500 bg-white/10 text-white shadow-sm py-2 px-3"
                    >

                    <span class="text-slate-400 text-sm font-medium">a</span>

                    <input
                        type="date"
                        wire:model.live="hasta"
                        class="text-sm rounded-lg border-transparent focus:border-slate-500 focus:ring-slate-500 bg-white/10 text-white shadow-sm py-2 px-3"
                    >
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button
                        wire:click="descargarPdf"
                        class="bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 w-full sm:w-auto text-sm"
                    >
                        <i class="fas fa-file-pdf text-red-400"></i>
                        PDF
                    </button>

                    <button
                        wire:click="descargarExcel"
                        class="bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold rounded-xl py-2.5 px-4 shadow-sm transition-all duration-200 flex items-center justify-center gap-2 w-full sm:w-auto text-sm"
                    >
                        <i class="fas fa-file-excel text-emerald-400"></i>
                        Excel
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- KPIs Solicitudes --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-file-signature text-slate-400"></i>
            Solicitudes FISE
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total solicitudes</span>
                    <i class="fas fa-folder-open text-slate-200 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-slate-800">{{ $totalSolicitudes }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aprobadas</span>
                    <i class="fas fa-check-circle text-emerald-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-emerald-600">{{ $solicitudesAprobadas }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-red-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rechazadas</span>
                    <i class="fas fa-times-circle text-red-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-red-600">{{ $solicitudesRechazadas }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-amber-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendientes</span>
                    <i class="fas fa-clock text-amber-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-amber-600">{{ $solicitudesPendientes }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-indigo-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">% Aprobación</span>
                    <i class="fas fa-percent text-indigo-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-indigo-600">{{ $tasaAprobacion }}%</p>
            </div>

        </div>
    </div>

    {{-- KPIs Pagos --}}
    <div>
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1 flex items-center gap-2">
            <i class="fas fa-money-check-dollar text-slate-400"></i>
            Pagos FISE
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total pagos</span>
                    <i class="fas fa-file-invoice-dollar text-slate-200 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-slate-800">{{ $totalPagos }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-indigo-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Monto total</span>
                    <i class="fas fa-sack-dollar text-indigo-100 text-lg"></i>
                </div>
                <p class="text-2xl font-extrabold text-indigo-600">
                    S/ {{ number_format($montoTotalFise, 2) }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Monto pagado</span>
                    <i class="fas fa-money-bill-wave text-emerald-100 text-lg"></i>
                </div>
                <p class="text-2xl font-extrabold text-emerald-600">
                    S/ {{ number_format($montoPagadoFise, 2) }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center border-l-4 border-l-amber-500">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Saldo pendiente</span>
                    <i class="fas fa-hand-holding-dollar text-amber-100 text-lg"></i>
                </div>
                <p class="text-2xl font-extrabold text-amber-600">
                    S/ {{ number_format($saldoPendiente, 2) }}
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pagados (Cant.)</span>
                    <i class="fas fa-check-double text-emerald-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-emerald-600">{{ $pagosPagados }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Parciales (Cant.)</span>
                    <i class="fas fa-hourglass-half text-amber-100 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-amber-600">{{ $pagosParciales }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-5 flex flex-col justify-center md:col-span-2 lg:col-span-2 border-l-4 border-l-slate-700 bg-slate-50/50">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ingresos caja FISE</span>
                    <i class="fas fa-cash-register text-slate-300 text-lg"></i>
                </div>
                <p class="text-3xl font-extrabold text-slate-700">
                    S/ {{ number_format($ingresosCajaFise, 2) }}
                </p>
            </div>

        </div>
    </div>

    {{-- Layout Mixto: Gráfico y Tabla Técnicos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Gráfico de solicitudes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col h-full" wire:ignore wire:key="chart-fise">

            <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-6 flex items-center gap-2">
                <i class="fas fa-chart-line text-slate-400"></i>
                Solicitudes FISE por día
            </h3>

            @if ($totalSolicitudes > 0)

                <div class="relative w-full flex-grow" style="min-height: 280px;">
                    <canvas
                        id="chartFise"
                        data-labels='@json($labels)'
                        data-aprobadas='@json($aprobadasPorDia ?? [])'
                        data-rechazadas='@json($rechazadasPorDia ?? [])'
                        data-pendientes='@json($pendientesPorDia ?? [])'
                    ></canvas>
                </div>

                <div class="flex flex-wrap gap-4 mt-6 justify-center">

                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-inner"></span>
                        Aprobadas
                    </div>

                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-red-500 shadow-inner"></span>
                        Rechazadas
                    </div>

                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-semibold text-slate-600 shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-amber-500 shadow-inner"></span>
                        Pendientes
                    </div>

                </div>

            @else

                <div class="flex flex-col items-center justify-center py-16 text-center flex-grow bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 bg-white shadow-sm rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-file-circle-question text-slate-300 text-2xl"></i>
                    </div>

                    <p class="text-slate-600 font-semibold text-lg">Sin datos</p>
                    <p class="text-slate-400 text-sm mt-1">No hay solicitudes FISE en este periodo</p>
                </div>

            @endif

        </div>

        {{-- Pagos por técnico --}}
        @if ($pagosPorTecnico && count($pagosPorTecnico) > 0)

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col h-full">

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

    </div>

    {{-- Tabla solicitudes --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">

        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">

            <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                <i class="fas fa-list-check text-slate-400"></i>
                Detalle de solicitudes
            </h3>

            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                {{ $totalSolicitudes }} registros
            </span>

        </div>

        <div class="overflow-x-auto max-h-[400px] overflow-y-auto custom-scrollbar">

            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200 shadow-sm">
                    <tr>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3">Cliente</th>
                        <th class="px-5 py-3">Vehículo</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3">Observaciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">

                    @if (count($solicitudes) > 0)

                        @foreach ($solicitudes as $sol)

                            @php
                                $colorSol = match ($sol->estado) {
                                    'aprobado' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'rechazado' => 'bg-red-50 text-red-700 border border-red-200',
                                    default => 'bg-amber-50 text-amber-700 border border-amber-200',
                                };
                            @endphp

                            <tr
                                wire:key="solicitud-{{ $sol->id ?? $loop->index }}"
                                class="hover:bg-slate-50/80 transition-colors"
                            >

                                <td class="px-5 py-3.5 text-xs text-slate-500">
                                    {{ $sol->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-5 py-3.5 font-medium text-slate-800">
                                    {{ trim(($sol->cliente->nombre ?? '') . ' ' . ($sol->cliente->apellido ?? '')) ?: 'N/A' }}
                                </td>

                                <td class="px-5 py-3.5 text-slate-600">
                                    <span class="border border-slate-200 bg-slate-50 px-2 py-1 rounded text-xs font-mono font-bold">
                                        {{ $sol->vehiculo->placa ?? 'N/A' }}
                                    </span>
                                </td>

                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-md {{ $colorSol }} px-2.5 py-1 text-xs font-bold uppercase tracking-wider">
                                        {{ ucfirst($sol->estado) }}
                                    </span>
                                </td>

                                <td
                                    class="px-5 py-3.5 text-xs text-slate-500 max-w-[200px] truncate"
                                    title="{{ $sol->observaciones }}"
                                >
                                    {{ $sol->observaciones ?: '---' }}
                                </td>

                            </tr>

                        @endforeach

                    @else

                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block text-slate-300"></i>
                                <span class="font-medium">Sin solicitudes en este periodo.</span>
                            </td>
                        </tr>

                    @endif

                </tbody>

            </table>

        </div>
    </div>

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

                            <tr
                                wire:key="pago-{{ $pago->id ?? $loop->index }}"
                                class="hover:bg-slate-50/80 transition-colors"
                            >

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

                                <td class="px-5 py-3.5 text-right font-bold {{ ($pago->monto_total - $pago->monto_pagado) > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                    S/ {{ number_format($pago->monto_total - $pago->monto_pagado, 2) }}
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
        window.renderChartFise = function () {
            const canvas = document.getElementById('chartFise');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const aprobadas = JSON.parse(canvas.dataset.aprobadas || '[]');
            const rechazadas = JSON.parse(canvas.dataset.rechazadas || '[]');
            const pendientes = JSON.parse(canvas.dataset.pendientes || '[]');

            if (window.chartFiseInstance) {
                window.chartFiseInstance.destroy();
            }

            window.chartFiseInstance = new Chart(canvas, {
                type: 'bar',

                data: {
                    labels: labels,

                    datasets: [
                        {
                            label: 'Aprobadas',
                            data: aprobadas,
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Rechazadas',
                            data: rechazadas,
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Pendientes',
                            data: pendientes,
                            backgroundColor: '#f59e0b',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        mode: 'index',
                        intersect: false
                    },

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#0f172a',
                            bodyColor: '#475569',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,

                            titleFont: {
                                size: 14,
                                weight: 'bold',
                                family: "'Inter', sans-serif"
                            },

                            bodyFont: {
                                size: 13,
                                family: "'Inter', sans-serif"
                            },

                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y}`
                            }
                        }
                    },

                    scales: {
                        x: {
                            stacked: true,

                            grid: {
                                display: false,
                                drawBorder: false
                            },

                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif"
                                },
                                color: '#64748b'
                            }
                        },

                        y: {
                            stacked: true,
                            beginAtZero: true,

                            ticks: {
                                stepSize: 1,

                                font: {
                                    family: "'Inter', sans-serif"
                                },

                                color: '#94a3b8'
                            },

                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false,
                                borderDash: [5, 5]
                            }
                        }
                    }
                }
            });
        };

        window.renderChartFise();

        // Nota: el <div> que contiene el canvas tiene wire:ignore, así que Livewire
        // NO actualizará sus atributos data-* en re-renders posteriores.
        // Este hook solo es útil si algo fuerza un remount completo del componente;
        // la actualización real del gráfico debe llegar por el evento 'chart-data-fise'.
        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'fise.reporte') {
                window.renderChartFise();
            }
        });

        $wire.on('chart-data-fise', (data) => {
            const canvas = document.getElementById('chartFise');

            if (!canvas) {
                return;
            }

            canvas.dataset.labels = JSON.stringify(data.labels);
            canvas.dataset.aprobadas = JSON.stringify(data.aprobadas);
            canvas.dataset.rechazadas = JSON.stringify(data.rechazadas);
            canvas.dataset.pendientes = JSON.stringify(data.pendientes);

            window.renderChartFise();
        });

        Livewire.on('descargar-pdf', (params) => {
            Swal.fire({
                title: 'Próximamente',
                text: 'La exportación PDF del reporte FISE está en desarrollo.',
                icon: 'info',
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        });

        Livewire.on('descargar-excel', (params) => {
            Swal.fire({
                title: 'Próximamente',
                text: 'La exportación Excel del reporte FISE está en desarrollo.',
                icon: 'info',
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        });
    </script>

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
    @endscript

</div>