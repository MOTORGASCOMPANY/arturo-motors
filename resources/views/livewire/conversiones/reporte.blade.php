<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6 font-inter">

    {{-- Encabezado + exportes --}}
    <x-conversiones.reporte-header :filtro-badge="$filtroBadge" />

    {{-- Filtros: por encima de todos los gráficos --}}
    <x-conversiones.reporte-filtros
        :sedes="$sedes"
        :estados="$estadosDisponibles"
        :filtro-sede="$filtroSede"
        :filtro-estado="$filtroEstado"
        :filtro-fecha-desde="$filtroFechaDesde"
        :filtro-fecha-hasta="$filtroFechaHasta"
        :filtro-badge="$filtroBadge"
        :puede-limpiar="$filtroSede || $filtroEstado !== 'todos' || $filtroFechaDesde || $filtroFechaHasta" />

    {{-- 4 tarjetas de indicadores --}}
    <x-conversiones.reporte-indicadores
        :filtro-badge="$filtroBadge"
        :total-conversiones="$totalConversiones"
        :completadas="$completadas"
        :en-proceso="$enProceso"
        :tasa-completado="$tasaCompletado"
        :duracion-promedio="$duracionPromedio"
        :kits-instalados="$kitsInstalados"
        :instalados-por-combustible="$instaladosPorCombustible"
        :kits-disponibles="$kitsDisponibles"
        :kits-sellados="$kitsSellados"
        :kits-completados="$kitsCompletados"
        :piezas-sueltas="$piezasSueltas"
        :sueltos-serializados="$sueltosSerializados"
        :sueltos-cantidad-total="$sueltosCantidadTotal" />

    
    <x-conversiones.reporte-graficos
        :filtro-badge="$filtroBadge"
        :kits-total="$kitsTotal"
        :kits-sellados-chart="$kitsSelladosChart"
        :kits-completados-chart="$kitsCompletadosChart"
        :kits-asignados-chart="$kitsAsignadosChart" />

<x-conversiones.reporte-tabla :detalle-ordenes="$detalleOrdenes" :filtro-badge="$filtroBadge" />
    <div id="reporteConvPayload" class="hidden" aria-hidden="true">@json($charts)</div>

    @script
    <script>
        window.renderReporteConvCharts = function () {
            if (!window.CHART_DEFS) return;
            const payload = document.getElementById('reporteConvPayload');
            if (!payload) return;
            let d;
            try { d = JSON.parse(payload.textContent || '{}'); } catch (e) { return; }
            if (!d.labels) return;

            // 1. Kits en almacén (doughnut: sellados, completados, asignados, otros)
            try {
                const ctx = document.getElementById('chartKitsUsados');
                if (ctx && window.chartKitsUsadosInstance) window.chartKitsUsadosInstance.destroy();
                const kitsLabels = ['Sellados', 'Completados', 'Asignados a clientes', 'Otros'];
                const kitsData = [
                    d.kitsSelladosChart || 0,
                    d.kitsCompletadosChart || 0,
                    d.kitsAsignadosChart || 0,
                    d.kitsOtrosChart || 0
                ];
                const kitsColors = ['#2563eb', '#10b981', '#8b5cf6', '#94a3b8'];
                if (window.chartKitsUsadosInstance) window.chartKitsUsadosInstance.destroy();
                window.chartKitsUsadosInstance = window.CHART_DEFS.renderConvDoughnut(
                    'chartKitsUsados',
                    'chartKitsUsadosInstance',
                    kitsLabels,
                    kitsData,
                    kitsColors
                );
            } catch (e) { console.error('[conversiones] chart1', e); }

            // 2. Kits completados por tipo GNV/GLP (doughnut) - si existen los datos
            try {
                const ctx = document.getElementById('chartConvEstado');
                if (ctx && d.kitsCompletadosGNV !== undefined && d.kitsCompletadosGLP !== undefined) {
                    if (window.chartConvEstadoInstance) window.chartConvEstadoInstance.destroy();
                    window.chartConvEstadoInstance = window.CHART_DEFS.renderConvDoughnut(
                        'chartConvEstado',
                        'chartConvEstadoInstance',
                        ['GNV', 'GLP'],
                        [d.kitsCompletadosGNV || 0, d.kitsCompletadosGLP || 0],
                        ['#2563eb', '#f59e0b']
                    );
                }
            } catch (e) { console.error('[conversiones] chart2', e); }

            // 3. Kits asignados por técnico (horizontal bar) - si existen datos
            try {
                const ctx = document.getElementById('chartComponentes');
                if (ctx && d.kitsAsignadosTecnicoLabels && d.kitsAsignadosTecnicoData) {
                    if (window.chartKitsAsignadosTecnicoInstance) window.chartKitsAsignadosTecnicoInstance.destroy();
                    const labels = d.kitsAsignadosTecnicoLabels || [];
                    const data = d.kitsAsignadosTecnicoData || [];
                    if (labels.length) {
                        window.chartKitsAsignadosTecnicoInstance = window.CHART_DEFS.renderConvBar(
                            'chartComponentes',
                            'chartKitsAsignadosTecnicoInstance',
                            labels,
                            [{ label: 'Kits asignados', data: data, backgroundColor: '#8b5cf6', borderRadius: 6, maxBarThickness: 28 }],
                            { indexAxis: 'y' }
                        );
                    }
                } catch (e) { console.error('[conversiones] chart3', e); }

            // 4. Conversiones por mes (bar chart)
            try {
                const ctx = document.getElementById('chartConvMes');
                if (ctx && d.conversionesMesLabels && d.conversionesMesData) {
                    if (window.chartConvMesInstance) window.chartConvMesInstance.destroy();
                    window.chartConvMesInstance = window.CHART_DEFS.renderConvBar(
                        'chartConvMes',
                        'chartConvMesInstance',
                        d.conversionesMesLabels,
                        [{ label: 'Conversiones', data: d.conversionesMesData, backgroundColor: '#2563eb', borderRadius: 6 }],
                        {}
                    );
                } catch (e) { console.error('[conversiones] chart4', e); }

            // 5. Balance por sede (bar chart)
            try {
                const ctx = document.getElementById('chartBalanceSedes');
                if (ctx && d.balanceSedesLabels && d.balanceSedesData) {
                    if (window.chartBalanceSedesInstance) window.chartBalanceSedesInstance.destroy();
                    window.chartBalanceSedesInstance = window.CHART_DEFS.renderConvBar(
                        'chartBalanceSedes',
                        'chartBalanceSedesInstance',
                        d.balanceSedesLabels,
                        [{ label: 'Kits disponibles', data: d.balanceSedesData, backgroundColor: '#2563eb', borderRadius: 6 }],
                        {}
                    );
                } catch (e) { console.error('[conversiones] chart5', e); }

            // 6. Despachados: serie vs cantidad
            try {
                const ctx = document.getElementById('chartDespachados');
                if (ctx && d.despachadosSerieData && d.despachadosCantidadData) {
                    if (window.chartDespachadosInstance) window.chartDespachadosInstance.destroy();
                    window.chartDespachadosInstance = window.CHART_DEFS.renderConvBar(
                        'chartDespachados',
                        'chartDespachadosInstance',
                        ['Serie', 'Cantidad'],
                        [
                            { label: 'Serie', data: d.despachadosSerieData, backgroundColor: '#8b5cf6', borderRadius: 6 },
                            { label: 'Cantidad', data: d.despachadosCantidadData, backgroundColor: '#10b981', borderRadius: 6 }
                        ],
                        { stacked: true }
                    );
                } catch (e) { console.error('[conversiones] chart6', e); }

            // 7. Componentes instalados
            try {
                const ctx = document.getElementById('chartComponentes');
                if (ctx && d.componentesLabels && d.componentesData) {
                    if (window.chartComponentesInstance) window.chartComponentesInstance.destroy();
                    window.chartComponentesInstance = window.CHART_DEFS.renderConvBar(
                        'chartComponentes',
                        'chartComponentesInstance',
                        d.componentesLabels,
                        [{ label: 'Componentes instalados', data: d.componentesData, backgroundColor: '#f59e0b', borderRadius: 6 }],
                        {}
                    );
                } catch (e) { console.error('[conversiones] chart7', e); }
        };

        window.renderReporteConvCharts();

        document.addEventListener('livewire:navigated', window.renderReporteConvCharts);

        $wire.on('chart-data-updated', (payload) => {
            const el = document.getElementById('reporteConvPayload');
            if (el && payload && payload.charts) {
                el.textContent = JSON.stringify(payload.charts);
            }
            window.renderReporteConvCharts();
        });

        // Export alerts
        Livewire.on('descargar-pdf', (params) => {
            AppSwal.exportar({ url: params.url, titulo: 'Exportando PDF', texto: 'Generando el reporte, por favor espera...', archivo: 'PDF' });
        });
        Livewire.on('descargar-excel', (params) => {
            AppSwal.exportar({ url: params.url, titulo: 'Exportando Excel', texto: 'Generando el reporte, por favor espera...', archivo: 'Excel' });
        });
    </script>
    @endscript
</div>
