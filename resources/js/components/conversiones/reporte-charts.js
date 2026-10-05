import '../reportes/charts.js';

function renderChart1(d) {
    try {
        window.CHART_DEFS.renderConvDoughnut(
            'chartKitsUsados',
            'chartKitsUsadosInstance',
            d.labelsKits || [],
            d.dataKits || [],
            ['#2563eb', '#10b981', '#8b5cf6']
        );
    } catch (e) { console.error('[conversiones] chart1', e); }
}

function renderChart2(d) {
    try {
        window.CHART_DEFS.renderConvDoughnut(
            'chartConvEstado',
            'chartConvEstadoInstance',
            d.labelsEstado || [],
            d.dataEstado || [],
            d.coloresEstado
        );
    } catch (e) { console.error('[conversiones] chart2', e); }
}

function renderChart3(d) {
    try {
        const estados = d.estadosMes || [];
        const nombres = d.labelsEstadosMes || [];
        const colores = d.coloresMesEstados || [];
        const datos = d.dataMesPorEstado || {};
        const datasets = estados.map((estado, i) => ({
            label: nombres[i] || estado,
            data: datos[estado] || [],
            backgroundColor: colores[i] || '#2563eb',
            borderRadius: 6,
            maxBarThickness: 48
        }));
        window.CHART_DEFS.renderConvBar(
            'chartConvMes',
            'chartConvMesInstance',
            d.labelsMes || [],
            datasets,
            { stacked: true }
        );
    } catch (e) { console.error('[conversiones] chart3', e); }
}

function renderChart4(d) {
    try {
        window.CHART_DEFS.renderConvBar(
            'chartComponentes',
            'chartComponentesInstance',
            d.labelsComponentes || [],
            [{ label: 'Componentes instalados', data: d.dataComponentes || [], backgroundColor: '#f59e0b', borderRadius: 6, maxBarThickness: 28 }],
            { options: { indexAxis: 'y' } }
        );
    } catch (e) { console.error('[conversiones] chart4', e); }
}

function renderChart5(d) {
    try {
        window.CHART_DEFS.renderConvDoughnut(
            'chartDespachados',
            'chartDespachadosInstance',
            d.labelsDespachados || [],
            d.dataDespachados || [],
            ['#8b5cf6', '#10b981']
        );
    } catch (e) { console.error('[conversiones] chart5', e); }
}

function renderChart6(d) {
    try {
        window.CHART_DEFS.renderConvBar(
            'chartBalanceSedes',
            'chartBalanceSedesInstance',
            d.labelsStockSedes || [],
            [
                { label: 'Kits', data: d.dataStockKits || [], backgroundColor: '#2563eb', borderRadius: 6, maxBarThickness: 48 },
                { label: 'Sueltos con serie', data: d.dataStockSerie || [], backgroundColor: '#8b5cf6', borderRadius: 6, maxBarThickness: 48 },
                { label: 'Sueltos por cantidad', data: d.dataStockCantidad || [], backgroundColor: '#10b981', borderRadius: 6, maxBarThickness: 48 }
            ],
            {}
        );
    } catch (e) { console.error('[conversiones] chart6', e); }
}

function renderReporteConvCharts() {
    if (!window.CHART_DEFS) {
        console.error('[conversiones] window.CHART_DEFS no existe');
        return;
    }
    if (typeof Chart === 'undefined') {
        console.error('[conversiones] Chart.js no está disponible globalmente (window.Chart)');
        return;
    }
    const payload = document.getElementById('reporteConvPayload');
    if (!payload) return;
    let d;
    try {
        d = JSON.parse(payload.textContent || '{}');
    } catch (e) {
        console.error('[conversiones] JSON inválido en el payload', e);
        return;
    }

    renderChart1(d);
    renderChart2(d);
    renderChart3(d);
    renderChart4(d);
    renderChart5(d);
    renderChart6(d);
}

window.renderReporteConvCharts = renderReporteConvCharts;

document.addEventListener('DOMContentLoaded', renderReporteConvCharts);
document.addEventListener('livewire:navigated', renderReporteConvCharts);

document.addEventListener('livewire:init', () => {
    Livewire.on('chart-data-updated', (payload) => {
        const data = Array.isArray(payload) ? payload[0] : payload;
        const el = document.getElementById('reporteConvPayload');
        if (el && data && data.charts) {
            el.textContent = JSON.stringify(data.charts);
        }
        renderReporteConvCharts();
    });

    Livewire.on('descargar-pdf', (params) => {
        const p = Array.isArray(params) ? params[0] : params;
        if (window.AppSwal) {
            AppSwal.exportar({ url: p.url, titulo: 'Exportando PDF', texto: 'Generando el reporte, por favor espera...', archivo: 'PDF' });
        }
    });

    Livewire.on('descargar-excel', (params) => {
        const p = Array.isArray(params) ? params[0] : params;
        if (window.AppSwal) {
            AppSwal.exportar({ url: p.url, titulo: 'Exportando Excel', texto: 'Generando el reporte, por favor espera...', archivo: 'Excel' });
        }
    });
});