(function () {
    const palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'];

    function destroyChart(key) {
        try {
            if (window[key] && typeof window[key].destroy === 'function') {
                window[key].destroy();
            }
        } catch (e) { /* ignore */ }
        window[key] = null;
    }

    function getCtx(canvasId, emptyId, hasData) {
        const canvas = document.getElementById(canvasId);
        const empty = document.getElementById(emptyId);
        if (empty) empty.classList.toggle('hidden', !!hasData);
        if (canvas) canvas.classList.toggle('hidden', !hasData);
        if (!canvas || !hasData) return null;
        if (typeof Chart === 'undefined') return null;
        return canvas.getContext('2d');
    }

    function renderBar(id, emptyId, chartKey, labels, data, color, label) {
        destroyChart(chartKey);
        const arr = Array.isArray(data) ? data : [];
        const has = arr.some(function (v) { return Number(v) > 0; });
        const ctx = getCtx(id, emptyId, has);
        if (!ctx) return;
        window[chartKey] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Array.isArray(labels) ? labels : [],
                datasets: [{
                    label: label || 'Unidades',
                    data: arr,
                    backgroundColor: color || palette[0],
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
        const ctx = getCtx(id, emptyId, has);
        if (!ctx) return;
        window[chartKey] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Array.isArray(labels) ? labels : [],
                datasets: [{
                    data: arr,
                    backgroundColor: palette,
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
        const ctx = getCtx(id, emptyId, has);
        if (!ctx) return;
        window[chartKey] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Array.isArray(labels) ? labels : [],
                datasets: [{
                    label: 'Unidades',
                    data: arr,
                    backgroundColor: palette,
                    borderRadius: 4,
                    maxBarThickness: 18
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    function renderLine(id, emptyId, chartKey, labels, entradas, salidas) {
        destroyChart(chartKey);
        const e = Array.isArray(entradas) ? entradas : [];
        const s = Array.isArray(salidas) ? salidas : [];
        const has = e.some(function (v) { return Number(v) > 0; }) || s.some(function (v) { return Number(v) > 0; });
        const ctx = getCtx(id, emptyId, has);
        if (!ctx) return;
        window[chartKey] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array.isArray(labels) ? labels : [],
                datasets: [
                    {
                        label: 'Entradas',
                        data: e,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2
                    },
                    {
                        label: 'Salidas',
                        data: s,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239,68,68,0.10)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 10, font: { size: 11 } }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } }
                }
            }
        });
    }

    function safe(fn) {
        try { fn(); } catch (e) { console.warn('[reporte-charts]', e); }
    }

    function readPayloadText(raw) {
        if (!raw) return null;
        raw = raw.trim();
        if (!raw) return null;
        // Defensive: Js::from-style wrapper (should not appear with @json)
        var m = raw.match(/^JSON\.parse\((['"])([\s\S]*)\1\)$/);
        if (m) {
            try {
                var inner = m[2]
                    .replace(/\\"/g, '"')
                    .replace(/\\'/g, "'")
                    .replace(/\\\\/g, '\\');
                return JSON.parse(inner);
            } catch (e) { /* fall through */ }
        }
        return JSON.parse(raw);
    }

    function syncPayloadFromDom() {
        var el = document.getElementById('reportePayload');
        if (!el) return;
        var text = el.textContent || '';
        if (!text.trim()) return;
        try {
            window.reporteData = readPayloadText(text);
        } catch (e) {
            console.warn('[reporte-charts] payload parse', e, text.slice(0, 80));
        }
    }

    function renderCharts() {
        // Always re-read from DOM: @script does NOT re-run on LW3 updates,
        // but #reportePayload is morphed with fresh $charts on every filter change.
        syncPayloadFromDom();
        const d = window.reporteData;
        if (!d) return;

        safe(function () {
            renderBar('chartStockSedes', 'emptySedes', 'chartSedes',
                d.labelsSedes, d.dataSedes, palette, 'Unidades');
        });
        safe(function () {
            renderDoughnut('chartStockCategorias', 'emptyCategorias', 'chartCategorias',
                d.labelsCategorias, d.dataCategorias);
        });
        safe(function () {
            renderHorizontalBar('chartTopProductos', 'emptyTop', 'chartTop',
                d.labelsTop, d.dataTop);
        });
        safe(function () {
            renderDoughnut('chartNivelStock', 'emptyNivel', 'chartNivel',
                d.labelsNivel, d.dataNivel);
        });
        safe(function () {
            renderDoughnut('chartKitsEstado', 'emptyKitsEstado', 'chartKitsEstadoInst',
                d.labelsKitsEstado, d.dataKitsEstado);
        });
        safe(function () {
            renderLine('chartMovDias', 'emptyMovDias', 'chartMovDiasInst',
                d.labelsMovDias, d.dataEntradas, d.dataSalidas);
        });
    }

    window.renderReporteCharts = function () {
        syncPayloadFromDom();
        renderCharts();
    };

    window.exportarPDF = function () {
        const url = window.reporteData && window.reporteData.exportPdfUrl;
        if (!url) return;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Exportando PDF',
                text: 'Generando el reporte...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: function () {
                    Swal.showLoading();
                    window.location.href = url;
                    setTimeout(function () { Swal.close(); }, 3000);
                }
            });
        } else {
            window.location.href = url;
        }
    };

    window.exportarExcel = function () {
        const url = window.reporteData && window.reporteData.exportExcelUrl;
        if (!url) return;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Exportando Excel',
                text: 'Generando el reporte...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: function () {
                    Swal.showLoading();
                    window.location.href = url;
                    setTimeout(function () { Swal.close(); }, 3000);
                }
            });
        } else {
            window.location.href = url;
        }
    };

    function scheduleRender() {
        requestAnimationFrame(function () {
            requestAnimationFrame(renderCharts);
        });
    }

    // Initial paint
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleRender);
    } else {
        scheduleRender();
    }

    // Livewire hooks: register as soon as Livewire exists (livewire:init may already have fired
    // because this file loads after @livewireScripts).
    var hooksTries = 0;
    function registerHooks() {
        if (window.Livewire && typeof Livewire.hook === 'function') {
            try {
                Livewire.hook('morphed', scheduleRender);
                Livewire.hook('message.processed', scheduleRender);
            } catch (e) { console.warn('[reporte-charts] hooks', e); }
            return;
        }
        if (++hooksTries < 100) setTimeout(registerHooks, 50);
    }
    registerHooks();

    document.addEventListener('livewire:navigated', scheduleRender);
    document.addEventListener('livewire:init', registerHooks);
})();
