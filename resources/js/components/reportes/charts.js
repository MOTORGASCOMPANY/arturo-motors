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
        // Si no se pasa emptyId, usar convención: 'empty-' + canvasId
        if (arguments.length < 2 || emptyId === undefined || emptyId === null) {
            emptyId = 'empty-' + canvasId;
        }
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

    // ====== CAJA REPORT (6 gráficos específicos) ======
    // Utilidad para formatear moneda en tooltips
    function cajaMoney(v) {
        return 'S/ ' + Number(v || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // 1. Ingresos vs Egresos (barra agrupada/apilada)
    function renderCajaIngresosEgresos(id, key, labels, ingresosData, egresosData, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Ingresos', data: ingresosData, backgroundColor: '#10b981', borderRadius: 4, barPercentage: 0.85, categoryPercentage: 0.7 },
                    { label: 'Egresos', data: egresosData, backgroundColor: '#ef4444', borderRadius: 4, barPercentage: 0.85, categoryPercentage: 0.7 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false, animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': S/ ' + Number(ctx.parsed.y || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 45, autoSkip: true }, border: { display: false } },
                    y: { beginAtZero: true, stacked: true, ticks: { callback: v => 'S/ ' + v.toLocaleString(), color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { display: false } }
                }
            }
        });
    }

    // 2. Ticket promedio por día (scatter)
    function renderCajaTicketDia(id, key, labels, ticketData, opsData, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        const has = (ticketData || []).some(v => v !== null && v !== undefined);
        if (!has) return;
        const opsDia = Array.isArray(opsData)
            ? opsData
            : (opts && Array.isArray(opts.opsDia) ? opts.opsDia : []);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'scatter',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ticket promedio',
                    data: Array.from({length: ticketData.length}, (_, i) => ({ x: i, y: ticketData[i] })),
                    backgroundColor: '#4f46e5',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1.5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, animation: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: {
                        label: ctx => {
                            const ops = opsDia[ctx.dataIndex];
                            if (ops === null || ops === undefined) return 'Día aún no ocurrido';
                            if (ops === 0) return 'Sin operaciones';
                            return 'S/ ' + Number(ctx.parsed.y || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' · ' + (ops || 0) + ' operación' + (ops === 1 ? '' : 'es');
                        }
                    } }
                },
                scales: {
                    x: { type: 'category', grid: { display: false, drawBorder: false }, ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 45, autoSkip: true }, border: { display: false } },
                    y: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v.toLocaleString(), color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { display: false } }
                }
            }
        });
    }

    // 3. Métodos de pago (dona)
    function renderCajaMetodosPago(id, key, labels, data, colors, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
            options: {
                responsive: true, maintainAspectRatio: false, animation: false, cutout: '58%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } } },
                    tooltip: { callbacks: { label: ctx => ctx.label + ': ' + 'S/ ' + Number(ctx.parsed || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } }
                }
            }
        });
    }

    // 6. FISE vs No FISE (barras apiladas)
    function renderCajaFiseNoFise(id, key, labels, fiseData, noFiseData, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'FISE', data: fiseData, backgroundColor: '#f59e0b', borderRadius: 3, barPercentage: 0.9, categoryPercentage: 0.75 },
                    { label: 'No FISE', data: noFiseData, backgroundColor: '#10b981', borderRadius: 3, barPercentage: 0.9, categoryPercentage: 0.75 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false, animation: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': S/ ' + Number(ctx.parsed.y || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } } },
                scales: { x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { color: '#94a3b8', font: { size: 10 } }, border: { display: false } }, y: { beginAtZero: true, stacked: true, ticks: { callback: v => 'S/ ' + v.toLocaleString(), color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { display: false } } }
            }
        });
    }

    // 4. Egresos por categoría (barras horizontales)
    function renderCajaEgresosCat(id, key, labels, data, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: { labels: labels, datasets: [{ label: 'Egresos', data: data, backgroundColor: '#ef4444', borderRadius: 6, maxBarThickness: 28 }] },
            options: {
                responsive: true, maintainAspectRatio: false, animation: false, indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => 'S/ ' + Number(ctx.parsed.x || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } } },
                scales: { x: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v.toLocaleString(), color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { display: false } }, y: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { display: false }, border: { display: false } } }
            }
        });
    }

    // 5. Ingresos de la semana (line con spanGaps)
    function renderCajaIngresosSemana(id, key, labels, data, opts) {
        const ctx = document.getElementById(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets: [{ label: 'Ingresos', data: data, borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.12)', borderWidth: 2.5, fill: true, tension: 0.4, spanGaps: false, pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderColor: '#0ea5e9', pointBorderWidth: 2 }] },
            options: {
                responsive: true, maintainAspectRatio: false, animation: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false }, tooltip: { callbacks: { title: items => { const l = items[0]?.label; return Array.isArray(l) ? l.join(' ') : String(l ?? ''); }, label: ctx => ctx.parsed.y === null ? 'Día aún no ocurrido' : 'S/ ' + Number(ctx.parsed.y || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } } },
                scales: { x: { grid: { display: false, drawBorder: false }, ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 45, autoSkip: true }, border: { display: false } }, y: { beginAtZero: true, ticks: { callback: v => 'S/ ' + v.toLocaleString(), color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { display: false } } }
            }
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
        // Reportes caja (6 gráficos específicos)
        renderCajaIngresosEgresos: renderCajaIngresosEgresos,
        renderCajaTicketDia: renderCajaTicketDia,
        renderCajaMetodosPago: renderCajaMetodosPago,
        renderCajaFiseNoFise: renderCajaFiseNoFise,
        renderCajaEgresosCat: renderCajaEgresosCat,
        renderCajaIngresosSemana: renderCajaIngresosSemana,
        // Utilidades
        destroyChart: destroyChart,
        getCtxWithEmpty: getCtxWithEmpty
    };
})();