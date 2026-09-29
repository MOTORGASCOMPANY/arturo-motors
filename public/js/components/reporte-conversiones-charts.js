(function () {
    // Contrastes altos: nada de dos azules juntos (mareaba).
    const palette = ['#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16', '#f97316'];
    const kitColors = ['#4f46e5', '#8b5cf6', '#10b981', '#f59e0b']; // sellados, completados, asignados, en armado
    const despachadoColors = ['#10b981', '#f59e0b']; // serie = verde, cantidad = ámbar
    const estadoColors = ['#06b6d4', '#10b981', '#f59e0b', '#4f46e5', '#ec4899', '#ef4444'];

    function destroyChart(key) {
        if (window[key]) {
            try { window[key].destroy(); } catch (e) { /* noop */ }
            window[key] = null;
        }
    }

    function getCtx(id) {
        const el = document.getElementById(id);
        if (!el) return null;
        // Chart.js needs a fresh canvas node if prior instance died with morph
        return el;
    }

    function safe(fn) {
        try { fn(); } catch (e) { console.warn('[conv-charts]', e); }
    }

    function readPayloadText(raw) {
        if (!raw) return null;
        raw = raw.trim();
        if (!raw) return null;
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
        var el = document.getElementById('reporteConvPayload');
        if (!el) return;
        var text = el.textContent || '';
        if (!text.trim()) return;
        try {
            window.reporteConvData = readPayloadText(text);
        } catch (e) {
            console.warn('[conv-charts] payload parse', e, text.slice(0, 80));
        }
    }

    function renderBar(id, key, labels, datasets, opts) {
        const ctx = getCtx(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx, {
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

    function renderDoughnut(id, key, labels, data, colors) {
        const ctx = getCtx(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        const bg = (colors && colors.length) ? colors : palette;
        window[key] = new Chart(ctx, {
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

    function renderHBar(id, key, labels, data, color) {
        const ctx = getCtx(id);
        if (!ctx || !labels || !labels.length) return;
        destroyChart(key);
        window[key] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'Veces', data: data, backgroundColor: color || '#4f46e5', borderRadius: 6, maxBarThickness: 40 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    function renderCharts() {
        syncPayloadFromDom();
        const d = window.reporteConvData;
        if (!d) return;

        // 1. Conversiones por mes
        safe(function () {
            renderBar('chartConvMes', 'chartConvMesInst', d.labelsMes, [
                { label: 'Completadas', data: d.dataMesCompletadas, backgroundColor: '#10b981', borderRadius: 6, barPercentage: 0.6, categoryPercentage: 0.8 },
                { label: 'En proceso', data: d.dataMesProceso, backgroundColor: '#f59e0b', borderRadius: 6, barPercentage: 0.6, categoryPercentage: 0.8 },
                { label: 'Otras', data: d.dataMesOtras, backgroundColor: '#94a3b8', borderRadius: 6, barPercentage: 0.6, categoryPercentage: 0.8 }
            ], { stacked: true });
        });

        // 2. Distribución por estado de conversión
        safe(function () {
            renderDoughnut('chartConvEstado', 'chartConvEstadoInst', d.labelsEstado, d.dataEstado, d.coloresEstado || estadoColors);
        });

        // 3. Kits: sellados / completados / asignados a clientes
        safe(function () {
            renderDoughnut('chartKitsUsados', 'chartKitsUsadosInst', d.labelsKits, d.dataKits, kitColors);
        });

        // 4. Componentes más instalados
        safe(function () {
            renderHBar('chartComponentes', 'chartComponentesInst', d.labelsComponentes, d.dataComponentes, '#06b6d4');
        });

        // 5. Balance de almacén por sede
        safe(function () {
            renderBar('chartBalanceSedes', 'chartBalanceSedesInst', d.labelsStockSedes, [
                { label: 'Kits disponibles', data: d.dataStockKits, backgroundColor: '#4f46e5', borderRadius: 6 },
                { label: 'Sueltos con serie', data: d.dataStockSerie, backgroundColor: '#10b981', borderRadius: 6 },
                { label: 'Sueltos por cantidad', data: d.dataStockCantidad, backgroundColor: '#f59e0b', borderRadius: 6 }
            ], { stacked: false });
        });

        // 6. Despachados: con serie vs por cantidad (verde vs ámbar — no dos azules)
        safe(function () {
            renderDoughnut('chartDespachados', 'chartDespachadosInst', d.labelsDespachados, d.dataDespachados, despachadoColors);
        });
    }

    window.renderReporteConvCharts = function () {
        syncPayloadFromDom();
        renderCharts();
    };

    function scheduleRender() {
        requestAnimationFrame(function () {
            requestAnimationFrame(renderCharts);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleRender);
    } else {
        scheduleRender();
    }

    var hooksTries = 0;
    function registerHooks() {
        if (window.Livewire && typeof Livewire.hook === 'function') {
            try {
                Livewire.hook('morphed', scheduleRender);
                Livewire.hook('message.processed', scheduleRender);
            } catch (e) { console.warn('[conv-charts] hooks', e); }
            return;
        }
        if (++hooksTries < 100) setTimeout(registerHooks, 50);
    }
    registerHooks();

    document.addEventListener('livewire:navigated', scheduleRender);
    document.addEventListener('livewire:init', registerHooks);

    window.exportarPDF = function () {
        // Componente reutilizable de carga (ver js/components/carga-swal.js)
        CargaSwal.exportar({
            url: window.reporteConvData && window.reporteConvData.exportPdfUrl,
            titulo: 'Exportando PDF',
            texto: 'Generando el reporte...'
        });
    };

    window.exportarExcel = function () {
        CargaSwal.exportar({
            url: window.reporteConvData && window.reporteConvData.exportExcelUrl,
            titulo: 'Exportando Excel',
            texto: 'Generando el reporte...'
        });
    };
})();
