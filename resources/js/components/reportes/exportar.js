(function () {
    // Exportación: spinner + toast vía AppSwal.exportar (layout)
    // Se usa desde botones de exportación PDF/Excel en reportes

    window.exportarPDF = function (url, opts) {
        if (!url) return;
        opts = opts || {};
        AppSwal.exportar(Object.assign({
            url: url,
            titulo: 'Exportando PDF',
            texto: 'Generando el reporte, por favor espera...',
            archivo: 'PDF',
            okTitulo: 'Descarga iniciada'
        }, opts));
    };

    window.exportarExcel = function (url, opts) {
        if (!url) return;
        opts = opts || {};
        AppSwal.exportar(Object.assign({
            url: url,
            titulo: 'Exportando Excel',
            texto: 'Generando el reporte, por favor espera...',
            archivo: 'Excel',
            okTitulo: 'Descarga iniciada'
        }, opts));
    };

    // Para compatibilidad con llamadas antiguas
    window.Exportar = {
        pdf: window.exportarPDF,
        excel: window.exportarExcel
    };
})();