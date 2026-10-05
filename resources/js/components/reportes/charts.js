(function () {
    // Paletas unificadas
    const palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'];
    const convPalette = ['#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16', '#f97316'];
    const kitColors = ['#4f46e5', '#8b5cf6', '#10b981', '#f59e0b'];
    const despachadoColors = ['#10b981', '#f59e0b'];
    const estadoColors = ['#06b6d4', '#10b981', '#f59e0b', '#4f46e5', '#ec4899', '#ef4444'];

    // Utilidades compartidas
    function destroyChart(key) {
        if (window[key]) {
            try { window[key].destroy(); } catch (e) { }
            window[key] = null;
        }
    }

    function getCtx(id) {
        const el = document.getElementById(id);
        if (!el) return null;
        return el.getContext('2d');
    }

    function getCtxWithEmpty(canvasId, emptyId, hasData) {
        const canvas = document.getElementById(canvasId);
        const empty = document.getElementById(emptyId);
        if (empty) empty.classList.toggle('hidden', !!hasData);
        if (canvas) canvas.classList.toggle('hidden', !hasData);
        if (!canvas || !hasData) return null;
        if (typeof Chart === 'undefined') return null;
        return canvas.getContext('2d');
    }

    function safe(fn) {
        try { fn(); } catch (e) { console.warn('[charts]', e); }
    }

    // ====== REPORTES ALMACÉN (reporte-charts.js original) ======
    const repoPalette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'];

    function renderBar(id, emptyId, chartKey, labels, data, color, label) {
        destroyChart(chartKey);
        const arr = Array.isArray(data) ? data : [];
        const has = arr.some(function (v) { return Number(v) > 0; });
        const ctx = getCtxWithEmpty(id, arguments[1], has);
        if (!ctx) return;
        window[chartKey] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Array.isArray(labels) ? labels : [],
                datasets: [{
                    label: label || 'Unidades',
                    data: arr,
                    backgroundColor: color || repoPalette[0],
                    borderRadius: 6,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (c) { return c.formattedValue; } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 0 } }
                }
            }
        });
    }

    function renderDoughnut(id, emptyId, chartKey, labels, data) {
        destroyChart(chartKey);
        const arr = Array.isArray(data) ? data : [];
        const has = arr.some(function (v) { return Number(v) > 0; });
        const ctx = getCtxWithEmpty(id, arguments[1], has);
        if (!ctx) return;
        window[chartKey] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Array.isArray(labels) ? labels : [],
                datasets: [{
                    data: arr,
                    backgroundColor: repoPalette,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }

    function renderHorizontalBar(id, emptyId, chartKey, labels, data) {
        destroyChart(chartKey);
        const arr = Array.isArray(data) ? data : [];
        const has = arr.some(function (v) { return Number(v) > 0; });
        const ctx = getCtxWithEmpty(id, arguments[1], has);
        if (!ctx) return;
        window[chartKey] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Array.isArray(labels) ? labels : [],
                datasets: [{
                    label: 'Unidades',
                    data: arr,
                    backgroundColor: repoPalette[0],
                    borderRadius: 6,
                    maxBarThickness: 28
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (c) { return c.formattedValue; } } }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // ====== REPORTES CONVERSIONES (reporte-conversiones-charts.js original) ======
    // Paletas ya declaradas arriba: convPalette, kitColors, despachadoColors, estadoColors

    function renderConvBar(id, key, labels, datasets, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: { labels: labels, datasets: datasets },
            options: Object.assign({
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } }
                    }
                },
                scales: {
                    y: { beginAtZero: true, stacked: !!(opts && opts.stacked), grid: { color: '#f1f5f9', drawBorder: false } },
                    x: { stacked: !!(opts && opts.stacked), grid: { display: false } }
                }
            }, (opts && opts.options) || {})
        });
    }

    function renderConvDoughnut(id, key, labels, data, colors) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        const bg = (colors && colors.length) ? colors : convPalette;
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: data, backgroundColor: bg, borderWidth: 2, borderColor: '#ffffff' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } }
                    }
                }
            }
        });
    }

    // ====== LINE CHART (para montos, tendencias) ======
    function renderConvLine(id, key, labels, datasets, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: Object.assign({
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#64748b' } },
                    y: { beginAtZero: true, ticks: { color: '#94a3b8' }, grid: { color: '#f1f5f9', drawBorder: false, borderDash: [5, 5] } }
                }
            }, (opts && opts.options) || {})
        });
    }

    // ====== REGISTRO GLOBAL ======
    window.CHART_DEFS = {
        // Reportes almacen
        renderBar: renderBar,
        renderDoughnut: renderDoughnut,
        renderHorizontalBar: renderHorizontalBar,
        // Reportes conversiones
        renderConvBar: renderConvBar,
        renderConvDoughnut: renderConvDoughnut,
        renderConvLine: renderConvLine,
        // Utilidades
        destroyChart: destroyChart
    };
})();