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
</div>
