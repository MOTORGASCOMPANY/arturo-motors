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

            // 1. Kits sellados vs completados (doughnut)
            try {
                const ctx = document.getElementById('chartKitsSelladosVsCompletados');
                if (ctx && window.chartKitsSelladosVsCompletadosInstance) window.chartKitsSelladosVsCompletadosInstance.destroy();
                window.chartKitsSelladosVsCompletadosInstance = window.CHART_DEFS.renderConvDoughnut(
                    'chartKitsSelladosVsCompletados',
                    'chartKitsSelladosVsCompletadosInstance',
                    ['Sellados', 'Completados'],
                    [d.kitsSellados || 0, d.kitsCompletados || 0],
                    ['#2563eb', '#10b981']
                );
            } catch (e) { console.error('[conversiones] chart1', e); }

            // 2. Kits completados por tipo (GNV/GLP) - doughnut
            try {
                const ctx = document.getElementById('chartKitsCompletadosTipo');
                if (ctx && window.chartKitsCompletadosTipoInstance) window.chartKitsCompletadosTipoInstance.destroy();
                window.chartKitsCompletadosTipoInstance = window.CHART_DEFS.renderConvDoughnut(
                    'chartKitsCompletadosTipo',
                    'chartKitsCompletadosTipoInstance',
                    ['GNV', 'GLP'],
                    [d.kitsCompletadosGNV || 0, d.kitsCompletadosGLP || 0],
                    ['#2563eb', '#f59e0b']
                );
            } catch (e) { console.error('[conversiones] chart2', e); }

            // 3. Kits asignados por técnico (horizontal bar)
            try {
                const ctx = document.getElementById('chartKitsAsignadosTecnico');
                if (ctx && window.chartKitsAsignadosTecnicoInstance) window.chartKitsAsignadosTecnicoInstance.destroy();
                const labels = d.kitsAsignadosTecnicoLabels || [];
                const data = d.kitsAsignadosTecnicoData || [];
                if (labels.length) {
                    window.chartKitsAsignadosTecnicoInstance = window.CHART_DEFS.renderConvBar(
                        'chartKitsAsignadosTecnico',
                        'chartKitsAsignadosTecnicoInstance',
                        labels,
                        [{ label: 'Kits asignados', data: data, backgroundColor: '#8b5cf6', borderRadius: 6, maxBarThickness: 28 }],
                        { indexAxis: 'y' }
                    );
                }
            } catch (e) { console.error('[conversiones] chart3', e); }
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
