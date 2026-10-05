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
        // La lógica de gráficos está en resources/js/components/conversiones/reporte-charts.js
        // Se carga vía Vite en app.js y se auto-inicializa
        // Solo necesitamos escuchar actualizaciones de Livewire
        $wire.on('chart-data-updated', (payload) => {
            const el = document.getElementById('reporteConvPayload');
            if (el && payload && payload.charts) {
                el.textContent = JSON.stringify(payload.charts);
            }
            if (window.renderReporteConvCharts) {
                window.renderReporteConvCharts();
            }
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
