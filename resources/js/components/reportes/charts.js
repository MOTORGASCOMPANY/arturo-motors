(function () {
    // Paletas unificadas
    const palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'];
    const convPalette = ['#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16', '#f97316'];
    const kitColors = ['#4f46e5', '#8b5cf6', '#10b981', '#f59e0b'];
    const despachadoColors = ['#10b981', '#f59e0b'];
    const estadoColors = ['#06b6d4', '#10b981', '#f59e0b', '#4f46e5', '#ec4899', '#ef4444'];

    const colors = {
        green: '#10b981',
        red: '#ef4444',
        indigo: '#4f46e5',
        amber: '#f59e0b',
        gray: '#9ca3af',
        sky: '#0ea5e9',
        slate: '#64748b'
    };

    const BASE = { responsive: true, maintainAspectRatio: false, animation: false };
    const GRID = '#f1f5f9';
    const TICK_COLOR = '#94a3b8';
    const TICK_FONT = { size: 10 };
    const FONT_FAMILY = "'Inter', sans-serif";
    const LEGEND_FONT = { size: 11, family: FONT_FAMILY };

    function money(v) {
        return 'S/ ' + Number(v || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function axisMoney(v) {
        return 'S/ ' + Number(v || 0).toLocaleString('es-PE');
    }

    const cajaMoney = money;

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
    const repoPalette = palette;

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
                ...BASE,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (c) { return c.formattedValue; } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: GRID } },
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
                ...BASE,
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
                ...BASE,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (c) { return c.formattedValue; } } }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: GRID } },
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
                ...BASE,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: LEGEND_FONT }
                    }
                },
                scales: {
                    y: { beginAtZero: true, stacked: !!(opts && opts.stacked), grid: { color: GRID, drawBorder: false } },
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
                ...BASE,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: LEGEND_FONT }
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
                ...BASE,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: LEGEND_FONT }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#64748b' } },
                    y: { beginAtZero: true, ticks: { color: TICK_COLOR }, grid: { color: GRID, drawBorder: false, borderDash: [5, 5] } }
                }
            }, (opts && opts.options) || {})
        });
    }

    // ====== CAJA REPORT (6 gráficos específicos) ======

    // 1. Ingresos vs Egresos (barra agrupada/apilada)
    function renderCajaIngresosEgresos(id, key, labels, ingresosData, egresosData, opts) {
        destroyChart(key);
        const ing = Array.isArray(ingresosData) ? ingresosData : [];
        const eg = Array.isArray(egresosData) ? egresosData : [];
        const has = !!(labels && labels.length) && (ing.some(v => Number(v) > 0) || eg.some(v => Number(v) > 0));
        const ctx = getCtxWithEmpty(id, null, has);
        if (!ctx) return;
        window[key] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Ingresos', data: ing, backgroundColor: '#10b981', borderRadius: 4, barPercentage: 0.85, categoryPercentage: 0.7 },
                    { label: 'Egresos', data: eg, backgroundColor: '#ef4444', borderRadius: 4, barPercentage: 0.85, categoryPercentage: 0.7 }
                ]
            },
            options: {
                ...BASE,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': S/ ' + Number(ctx.parsed.y || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: TICK_COLOR, font: TICK_FONT, maxRotation: 45, autoSkip: true }, border: { display: false } },
                    y: { beginAtZero: true, stacked: false, ticks: { callback: v => axisMoney(v), color: TICK_COLOR, font: TICK_FONT }, grid: { color: GRID }, border: { display: false } }
                }
            }
        });
    }

    // 2. Ticket promedio por día (scatter)
    function renderCajaTicketDia(id, key, labels, ticketData, opsData, opts) {
        destroyChart(key);
        const tickets = Array.isArray(ticketData) ? ticketData : [];
        const has = !!(labels && labels.length) && tickets.some(v => v !== null && v !== undefined);
        const ctx = getCtxWithEmpty(id, null, has);
        if (!ctx) return;
        const opsDia = Array.isArray(opsData)
            ? opsData
            : (opts && Array.isArray(opts.opsDia) ? opts.opsDia : []);
        window[key] = new Chart(ctx.getContext('2d'), {
            type: 'scatter',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ticket promedio',
                    data: Array.from({length: tickets.length}, (_, i) => ({ x: i, y: tickets[i] })),
                    backgroundColor: '#4f46e5',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1.5
                }]
            },
            options: {
                ...BASE,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: {
                        label: ctx => {
                            const ops = opsDia[ctx.dataIndex];
                            if (ops === null || ops === undefined) return 'Día aún no ocurrido';
                            if (ops === 0) return 'Sin operaciones';
                            return money(ctx.parsed.y) + ' · ' + (ops || 0) + ' operación' + (ops === 1 ? '' : 'es');
                        }
                    } }
                },
                scales: {
                    x: { type: 'category', grid: { display: false, drawBorder: false }, ticks: { color: TICK_COLOR, font: TICK_FONT, maxRotation: 45, autoSkip: true }, border: { display: false } },
                    y: { beginAtZero: true, ticks: { callback: v => axisMoney(v), color: TICK_COLOR, font: TICK_FONT }, grid: { color: GRID }, border: { display: false } }
                }
            }
        });
    }

    // 3. Métodos de pago (dona)
    function renderCajaMetodosPago(id, key, labels, data, colors, opts) {
        destroyChart(key);
        const arr = Array.isArray(data) ? data : [];
        const has = !!(labels && labels.length) && arr.some(v => Number(v) > 0);
        const ctx = getCtxWithEmpty(id, null, has);
        if (!ctx) return;
        window[key] = new Chart(ctx, {
            type: 'doughnut',
            data: { labels: labels, datasets: [{ data: arr, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
            options: {
                ...BASE, cutout: '58%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: LEGEND_FONT } },
                    tooltip: { callbacks: { label: ctx => ctx.label + ': ' + money(ctx.parsed) } }
                }
            }
        });
    }

    // 6. FISE vs No FISE (barras apiladas)
    function renderCajaFiseNoFise(id, key, labels, fiseData, noFiseData, opts) {
        destroyChart(key);
        const fise = Array.isArray(fiseData) ? fiseData : [];
        const noFise = Array.isArray(noFiseData) ? noFiseData : [];
        const has = !!(labels && labels.length) && (fise.some(v => Number(v) > 0) || noFise.some(v => Number(v) > 0));
        const ctx = getCtxWithEmpty(id, null, has);
        if (!ctx) return;
        window[key] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'FISE', data: fise, backgroundColor: '#f59e0b', borderRadius: 3, barPercentage: 0.9, categoryPercentage: 0.75 },
                    { label: 'No FISE', data: noFise, backgroundColor: '#10b981', borderRadius: 3, barPercentage: 0.9, categoryPercentage: 0.75 }
                ]
            },
            options: {
                ...BASE,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': S/ ' + Number(ctx.parsed.y || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } } },
                scales: { x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { color: TICK_COLOR, font: TICK_FONT }, border: { display: false } }, y: { beginAtZero: true, stacked: true, ticks: { callback: v => axisMoney(v), color: TICK_COLOR, font: TICK_FONT }, grid: { color: GRID }, border: { display: false } } }
            }
        });
    }

    // 4. Egresos por categoría (barras horizontales)
    function renderCajaEgresosCat(id, key, labels, data, opts) {
        destroyChart(key);
        const arr = Array.isArray(data) ? data : [];
        const has = !!(labels && labels.length) && arr.some(v => Number(v) > 0);
        const ctx = getCtxWithEmpty(id, null, has);
        if (!ctx) return;
        window[key] = new Chart(ctx, {
            type: 'bar',
            data: { labels: labels, datasets: [{ label: 'Egresos', data: arr, backgroundColor: '#ef4444', borderRadius: 6, maxBarThickness: 28 }] },
            options: {
                ...BASE, indexAxis: 'y',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => money(ctx.parsed.x) } } },
                scales: { x: { beginAtZero: true, ticks: { callback: v => axisMoney(v), color: TICK_COLOR, font: TICK_FONT }, grid: { color: GRID }, border: { display: false } }, y: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { display: false }, border: { display: false } } }
            }
        });
    }

    // 5. Ingresos de la semana (line con spanGaps)
    function renderCajaIngresosSemana(id, key, labels, data, opts) {
        destroyChart(key);
        const arr = Array.isArray(data) ? data : [];
        const has = !!(labels && labels.length) && arr.some(v => v !== null && v !== undefined && Number(v) > 0);
        const ctx = getCtxWithEmpty(id, null, has);
        if (!ctx) return;
        window[key] = new Chart(ctx, {
            type: 'line',
            data: { labels: labels, datasets: [{ label: 'Ingresos', data: arr, borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.12)', borderWidth: 2.5, fill: true, tension: 0.4, spanGaps: false, pointRadius: 4, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderColor: '#0ea5e9', pointBorderWidth: 2 }] },
            options: {
                ...BASE,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false }, tooltip: { callbacks: { title: items => { const l = items[0]?.label; return Array.isArray(l) ? l.join(' ') : String(l ?? ''); }, label: ctx => ctx.parsed.y === null ? 'Día aún no ocurrido' : money(ctx.parsed.y) } } },
                scales: { x: { grid: { display: false, drawBorder: false }, ticks: { color: TICK_COLOR, font: TICK_FONT, maxRotation: 45, autoSkip: true }, border: { display: false } }, y: { beginAtZero: true, ticks: { callback: v => axisMoney(v), color: TICK_COLOR, font: TICK_FONT }, grid: { color: GRID }, border: { display: false } } }
            }
        });
    }

    function readDataset(canvas, key) {
        try { return JSON.parse(canvas.dataset[key] || '[]'); } catch (e) { return []; }
    }

    function anyPositive(arrays) {
        return arrays.some(function (arr) {
            return Array.isArray(arr) && arr.some(function (v) { return Number(v) > 0; });
        });
    }

    function renderCitas() {
        const canvas = document.getElementById('chartCitas');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = readDataset(canvas, 'labels');
        const aceptadas = readDataset(canvas, 'aceptadas');
        const noAceptadas = readDataset(canvas, 'noaceptadas');
        const conversion = readDataset(canvas, 'conversion');

        const ctx = getCtxWithEmpty('chartCitas', 'empty-chartCitas', anyPositive([aceptadas, noAceptadas, conversion]));
        if (!ctx) return;
        destroyChart('chartCitasInstance');

        window.chartCitasInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Aceptadas',
                        data: aceptadas,
                        borderColor: colors.green,
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.green,
                        pointBorderWidth: 2
                    },
                    {
                        label: 'No aceptadas',
                        data: noAceptadas,
                        borderColor: colors.red,
                        backgroundColor: 'rgba(239, 68, 68, 0.12)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.red,
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Con OS (conversión)',
                        data: conversion,
                        borderColor: colors.indigo,
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.indigo,
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                ...BASE,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: (c) => c.dataset.label + ': ' + c.parsed.y }
                    }
                },
                scales: {
                    x: {
                        ticks: { maxRotation: 45, autoSkip: true, font: TICK_FONT, color: TICK_COLOR },
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, color: TICK_COLOR, font: TICK_FONT },
                        grid: { color: GRID },
                        border: { display: false }
                    }
                }
            }
        });
    }

    function renderRatioCitas(instanceKey, canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = readDataset(canvas, 'labels');
        const valores = readDataset(canvas, 'valores');
        const color = canvas.dataset.color;
        const fill = canvas.dataset.fillcolor;
        const emptyId = 'empty-' + canvasId;

        const ctx = getCtxWithEmpty(canvasId, emptyId, anyPositive([valores]));
        if (!ctx) return;
        destroyChart(instanceKey);

        window[instanceKey] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    borderColor: color,
                    backgroundColor: fill,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    spanGaps: true,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: color,
                    pointBorderWidth: 2
                }]
            },
            options: {
                ...BASE,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: (c) => c.parsed.y + '%' }
                    }
                },
                scales: {
                    x: {
                        ticks: { maxRotation: 45, autoSkip: true, font: TICK_FONT, color: TICK_COLOR },
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        ticks: { stepSize: 25, callback: (v) => v + '%', color: TICK_COLOR, font: TICK_FONT },
                        grid: { color: GRID },
                        border: { display: false }
                    }
                }
            }
        });
    }

    function renderCitasAsesores(onAsesorClick) {
        const canvas = document.getElementById('chartAsesores');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = readDataset(canvas, 'labels');
        const claves = readDataset(canvas, 'claves');
        const seleccionado = canvas.dataset.seleccionado || 'todos';
        const filtro = seleccionado !== 'todos';

        const alpha = (hex, a) => hex + Math.round(a * 255).toString(16).padStart(2, '0');
        const colorBar = (hex) => labels.map((_, i) => {
            if (!filtro || claves[i] === seleccionado) return hex;
            return alpha(hex, 0.35);
        });

        const ctx = getCtxWithEmpty('chartAsesores', 'empty-chartAsesores', anyPositive([
            readDataset(canvas, 'aceptadas'),
            readDataset(canvas, 'pendientes'),
            readDataset(canvas, 'rechazadas'),
            readDataset(canvas, 'canceladas')
        ]));
        if (!ctx) return;
        destroyChart('chartAsesoresInstance');

        window.chartAsesoresInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Aceptadas', data: readDataset(canvas, 'aceptadas'), backgroundColor: colorBar(colors.green), borderWidth: 0, borderRadius: { topLeft: 4, topRight: 4 } },
                    { label: 'Pendientes', data: readDataset(canvas, 'pendientes'), backgroundColor: colorBar(colors.amber), borderWidth: 0 },
                    { label: 'Rechazadas', data: readDataset(canvas, 'rechazadas'), backgroundColor: colorBar(colors.red), borderWidth: 0 },
                    { label: 'Canceladas', data: readDataset(canvas, 'canceladas'), backgroundColor: colorBar(colors.gray), borderWidth: 0, borderRadius: { bottomLeft: 4, bottomRight: 4 } }
                ]
            },
            options: {
                ...BASE,
                onClick: (evt, elements) => {
                    if (!elements.length || !onAsesorClick) return;
                    const clave = claves[elements[0].index];
                    if (clave) onAsesorClick(clave, seleccionado);
                },
                onHover: (evt, elements) => {
                    const target = evt.native && evt.native.target;
                    if (target) target.style.cursor = elements.length ? 'pointer' : 'default';
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { boxWidth: 12, boxHeight: 12, usePointStyle: true, pointStyle: 'rectRounded', font: { size: 11, weight: 'bold' }, color: '#6b7280', padding: 16 }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: (c) => c.dataset.label + ': ' + c.parsed.y,
                            footer: (items) => 'Total: ' + items.reduce((sum, it) => sum + (it.parsed.y || 0), 0)
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        ticks: { autoSkip: false, font: TICK_FONT, color: TICK_COLOR, maxRotation: 45, minRotation: 0 },
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, color: TICK_COLOR, font: TICK_FONT },
                        grid: { color: GRID },
                        border: { display: false }
                    }
                }
            }
        });
    }

    function renderFisePagosEstados() {
        const canvas = document.getElementById('chartPagosEstados');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = readDataset(canvas, 'labels');
        const pagados = readDataset(canvas, 'pagados');
        const parciales = readDataset(canvas, 'parciales');
        const pendientes = readDataset(canvas, 'pendientes');
        const tasa = readDataset(canvas, 'tasa');

        destroyChart('chartPagosEstadosInstance');
        window.chartPagosEstadosInstance = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Pagados', data: pagados, backgroundColor: colors.green, borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                    { label: 'Parciales', data: parciales, backgroundColor: '#6366f1', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                    { label: 'Pendientes', data: pendientes, backgroundColor: colors.amber, borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.8 },
                    { label: 'Tasa de pago', data: tasa, type: 'line', yAxisID: 'yTasa', borderColor: '#8b5cf6', backgroundColor: 'transparent', borderWidth: 2, tension: 0.3, pointRadius: 2, pointBackgroundColor: '#8b5cf6', fill: false }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)', titleColor: '#0f172a', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1, padding: 12, boxPadding: 6, usePointStyle: true,
                        callbacks: { label: (c) => c.dataset.yAxisID === 'yTasa' ? `${c.dataset.label}: ${c.parsed.y}%` : `${c.dataset.label}: ${c.parsed.y}` }
                    }
                },
                scales: {
                    x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { color: colors.slate } },
                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1, color: TICK_COLOR }, grid: { color: GRID, drawBorder: false, borderDash: [5, 5] } },
                    yTasa: { position: 'right', min: 0, max: 100, grid: { display: false, drawBorder: false }, ticks: { color: '#a78bfa', maxTicksLimit: 6, callback: (v) => v + '%' } }
                }
            }
        });
    }

    function renderFisePagosMonto() {
        const canvas = document.getElementById('chartPagosFise');
        if (!canvas || typeof Chart === 'undefined') return;

        renderConvLine('chartPagosFise', 'chartPagosFiseInstance',
            readDataset(canvas, 'labels'),
            [
                { label: 'Monto total', data: readDataset(canvas, 'montoTotal'), borderColor: colors.indigo, backgroundColor: 'rgba(79, 70, 229, 0.1)', fill: true, tension: 0.3, pointRadius: 3, pointBackgroundColor: colors.indigo, pointBorderColor: '#fff', pointBorderWidth: 2 },
                { label: 'Monto pagado', data: readDataset(canvas, 'montoPagado'), borderColor: colors.green, backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.3, pointRadius: 3, pointBackgroundColor: colors.green, pointBorderColor: '#fff', pointBorderWidth: 2 },
                { label: 'Saldo pendiente', data: readDataset(canvas, 'saldoPendiente'), borderColor: colors.amber, backgroundColor: 'rgba(245, 158, 11, 0.1)', fill: true, tension: 0.3, pointRadius: 3, pointBackgroundColor: colors.amber, pointBorderColor: '#fff', pointBorderWidth: 2 }
            ],
            {
                options: {
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)', titleColor: '#0f172a', bodyColor: '#475569',
                            borderColor: '#e2e8f0', borderWidth: 1, padding: 12, boxPadding: 6, usePointStyle: true,
                            callbacks: { label: (c) => `${c.dataset.label}: ${money(c.parsed.y)}` }
                        }
                    },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { color: colors.slate } },
                        y: { beginAtZero: true, ticks: { color: TICK_COLOR, callback: axisMoney }, grid: { color: GRID, drawBorder: false, borderDash: [5, 5] } }
                    }
                }
            }
        );
    }

    function inventarioGraficoBarras() {
        // La instancia va FUERA del objeto de Alpine para que no la vuelva Proxy.
        let chart = null;
        let observer = null;

        const sellados = [6, 182, 212];
        const completados = [16, 185, 129];
        const consumidos = [220, 38, 38];
        const rgba = (rgb, a) => `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${a})`;

        const construirDatasets = (g) => {
            const labels = g.labels || [];
            const armar = (label, data, color) => ({
                label,
                data: data || [],
                backgroundColor: labels.map(() => rgba(color, 0.85)),
                borderColor: labels.map(() => rgba(color, 1)),
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 48
            });
            return [
                armar('Sellados', g.sellados, sellados),
                armar('Completados', g.completados, completados),
                armar('Consumidos', g.consumidos, consumidos)
            ];
        };

        // Plugin: muestra el valor exacto encima de cada barra.
        const etiquetasValor = {
            id: 'etiquetasValor',
            afterDatasetsDraw(c) {
                const ctx = c.ctx;
                ctx.save();
                ctx.font = '600 11px Inter, system-ui, sans-serif';
                ctx.fillStyle = '#334155';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                c.data.datasets.forEach((ds, i) => {
                    c.getDatasetMeta(i).data.forEach((bar, idx) => {
                        ctx.fillText(ds.data[idx] ?? 0, bar.x, bar.y - 4);
                    });
                });
                ctx.restore();
            }
        };

        const leerDatos = (el) => {
            try {
                return JSON.parse(el.dataset.grafico);
            } catch (e) {
                console.error('[charts] Gráfico de inventario: datos inválidos', e);
                return { labels: [], sellados: [], completados: [], consumidos: [] };
            }
        };

        return {
            init() {
                const canvas = this.$refs.canvas;
                const datos = leerDatos(this.$el);

                const previo = typeof Chart !== 'undefined' && Chart.getChart ? Chart.getChart(canvas) : null;
                if (previo) previo.destroy();
                if (typeof Chart === 'undefined') return;

                chart = new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: datos.labels,
                        datasets: construirDatasets(datos)
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { top: 20 } },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleFont: { size: 13 },
                                bodyFont: { size: 12 },
                                padding: 12,
                                callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.y} kits` }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: colors.slate, font: { size: 12 } } },
                            y: { beginAtZero: true, grace: '10%', grid: { color: GRID }, ticks: { color: colors.slate, font: { size: 11 }, precision: 0 } }
                        },
                        animation: { duration: 500, easing: 'easeOutQuart' }
                    },
                    plugins: [etiquetasValor]
                });

                observer = new MutationObserver(() => {
                    if (!chart) return;
                    const nuevo = leerDatos(this.$el);
                    chart.data.labels = nuevo.labels;
                    chart.data.datasets = construirDatasets(nuevo);
                    chart.update();
                });
                observer.observe(this.$el, { attributes: true, attributeFilter: ['data-grafico'] });
            },

            destroy() {
                if (observer) observer.disconnect();
                if (chart) chart.destroy();
                chart = null;
            }
        };
    }

    // ====== REGISTRO GLOBAL ======
    window.inventarioGraficoBarras = inventarioGraficoBarras;
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
        renderCitas: renderCitas,
        renderRatioCitas: renderRatioCitas,
        renderCitasAsesores: renderCitasAsesores,
        renderFisePagosEstados: renderFisePagosEstados,
        renderFisePagosMonto: renderFisePagosMonto,
        // Utilidades
        money: money,
        axisMoney: axisMoney,
        destroyChart: destroyChart,
        getCtxWithEmpty: getCtxWithEmpty
    };
})();