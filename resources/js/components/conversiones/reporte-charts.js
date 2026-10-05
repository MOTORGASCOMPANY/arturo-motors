import '../reportes/charts.js';

function hasData(arr) {
    return Array.isArray(arr) && arr.some(v => Number(v) > 0);
}

function hasAnyData(...arrays) {
    return arrays.some(hasData);
}

function renderChart1(d) {
    try {
        const labels = d.labelsKits || [];
        const data = d.dataKits || [];
        const emptyId = 'empty-chartKitsUsados';
        const has = hasData(data);
        const ctx = window.CHART_DEFS.getCtxWithEmpty('chartKitsUsados', emptyId, has);
        if (!ctx) return;
        window.CHART_DEFS.renderConvDoughnut(
            'chartKitsUsados',
            'chartKitsUsadosInstance',
            labels,
            data,
            ['#2563eb', '#10b981', '#8b5cf6']
        );
    } catch (e) { console.error('[conversiones] chart1', e); }
}

function renderChart2(d) {
    try {
        const labels = d.labelsEstado || [];
        const data = d.dataEstado || [];
        const emptyId = 'empty-chartConvEstado';
        const has = hasData(data);
        const ctx = window.CHART_DEFS.getCtxWithEmpty('chartConvEstado', emptyId, has);
        if (!ctx) return;
        window.CHART_DEFS.renderConvDoughnut(
            'chartConvEstado',
            'chartConvEstadoInstance',
            labels,
            data,
            d.coloresEstado
        );
    } catch (e) { console.error('[conversiones] chart2', e); }
}

function renderChart3(d) {
    try {
        const labels = d.labelsMes || [];
        const estados = d.estadosMes || [];
        const datos = d.dataMesPorEstado || {};
        const has = hasAnyData(...Object.values(datos));
        const emptyId = 'empty-chartConvMes';
        const ctx = window.CHART_DEFS.getCtxWithEmpty('chartConvMes', emptyId, has);
        if (!ctx) return;

        const nombres = d.labelsEstadosMes || [];
        const colores = d.coloresMesEstados || [];
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
            labels,
            datasets,
            { stacked: true }
        );
    } catch (e) { console.error('[conversiones] chart3', e); }
}

function renderChart4(d) {
    try {
        const labels = d.labelsComponentes || [];
        const data = d.dataComponentes || [];
        const emptyId = 'empty-chartComponentes';
        const has = hasData(data);
        const ctx = window.CHART_DEFS.getCtxWithEmpty('chartComponentes', emptyId, has);
        if (!ctx) return;
        window.CHART_DEFS.renderConvBar(
            'chartComponentes',
            'chartComponentesInstance',
            labels,
            [{ label: 'Componentes instalados', data, backgroundColor: '#f59e0b', borderRadius: 6, maxBarThickness: 28 }],
            { options: { indexAxis: 'y' } }
        );
    } catch (e) { console.error('[conversiones] chart4', e); }
}

function renderChart5(d) {
    try {
        const labels = d.labelsDespachados || [];
        const data = d.dataDespachados || [];
        const emptyId = 'empty-chartDespachados';
        const has = hasData(data);
        const ctx = window.CHART_DEFS.getCtxWithEmpty('chartDespachados', emptyId, has);
        if (!ctx) return;
        window.CHART_DEFS.renderConvDoughnut(
            'chartDespachados',
            'chartDespachadosInstance',
            labels,
            data,
            ['#8b5cf6', '#10b981']
        );
    } catch (e) { console.error('[conversiones] chart5', e); }
}

function renderChart6(d) {
    try {
        const labels = d.labelsStockSedes || [];
        const datasets = [
            { label: 'Kits', data: d.dataStockKits || [], backgroundColor: '#2563eb', borderRadius: 6, maxBarThickness: 48 },
            { label: 'Sueltos con serie', data: d.dataStockSerie || [], backgroundColor: '#8b5cf6', borderRadius: 6, maxBarThickness: 48 },
            { label: 'Sueltos por cantidad', data: d.dataStockCantidad || [], backgroundColor: '#10b981', borderRadius: 6, maxBarThickness: 48 }
        ];
        const has = hasAnyData(...datasets.map(ds => ds.data));
        const emptyId = 'empty-chartBalanceSedes';
        const ctx = window.CHART_DEFS.getCtxWithEmpty('chartBalanceSedes', emptyId, has);
        if (!ctx) return;
        window.CHART_DEFS.renderConvBar(
            'chartBalanceSedes',
            'chartBalanceSedesInstance',
            labels,
            datasets,
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