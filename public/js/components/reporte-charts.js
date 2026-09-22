/**
 * reporte-charts — Chart.js rendering for the almacén reporte view.
 *
 * Depends on: Chart.js, SweetAlert2 (for exports)
 *
 * Data must be set before this script loads:
 *   window.reporteData = { sedes, labelsSedes, dataSedes, labelsCategorias, dataCategorias, exportPdfUrl, exportExcelUrl }
 */
(function () {
    const sedeColors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
    const categoriaColors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ec4899', '#64748b'];

    function renderCharts() {
        const d = window.reporteData;
        if (!d) return;

        // Bar chart: Stock por sede
        const ctxSedes = document.getElementById('chartStockSedes');
        const emptySedes = document.getElementById('emptySedes');
        if (ctxSedes) {
            if (window.chartSedes) window.chartSedes.destroy();
            const hasData = d.dataSedes.some(v => v > 0);
            emptySedes.classList.toggle('hidden', hasData);
            ctxSedes.classList.toggle('hidden', !hasData);
            if (hasData) {
                window.chartSedes = new Chart(ctxSedes, {
                    type: 'bar',
                    data: {
                        labels: d.labelsSedes,
                        datasets: [{
                            label: 'Items',
                            data: d.dataSedes,
                            backgroundColor: sedeColors,
                            borderRadius: 6,
                            maxBarThickness: 48
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.formattedValue} unidades`
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }

        // Doughnut chart: Stock por categoría
        const ctxCategorias = document.getElementById('chartStockCategorias');
        const emptyCategorias = document.getElementById('emptyCategorias');
        if (ctxCategorias) {
            if (window.chartCategorias) window.chartCategorias.destroy();
            const hasData = d.dataCategorias.some(v => v > 0);
            emptyCategorias.classList.toggle('hidden', hasData);
            ctxCategorias.classList.toggle('hidden', !hasData);
            if (hasData) {
                window.chartCategorias = new Chart(ctxCategorias, {
                    type: 'doughnut',
                    data: {
                        labels: d.labelsCategorias,
                        datasets: [{
                            data: d.dataCategorias,
                            backgroundColor: categoriaColors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, padding: 12, font: { size: 11, family: "'Inter', sans-serif" } }
                            }
                        }
                    }
                });
            }
        }
    }

    // Export functions
    window.exportarPDF = function () {
        const url = window.reporteData?.exportPdfUrl;
        if (!url) return;
        Swal.fire({
            title: 'Exportando PDF',
            text: 'Generando el reporte...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                window.location.href = url;
                setTimeout(() => { Swal.close(); }, 3000);
            }
        });
    };

    window.exportarExcel = function () {
        const url = window.reporteData?.exportExcelUrl;
        if (!url) return;
        Swal.fire({
            title: 'Exportando Excel',
            text: 'Generando el reporte...',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                window.location.href = url;
                setTimeout(() => { Swal.close(); }, 3000);
            }
        });
    };

    // Bind to Livewire lifecycle
    document.addEventListener('livewire:navigated', renderCharts);
    document.addEventListener('livewire:updated', renderCharts);
})();
