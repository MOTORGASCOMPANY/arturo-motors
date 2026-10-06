<style>
    @page { margin: 30px 50px 50px 50px; }

    * { box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 9.5px;
        line-height: 1.4;
        color: #333333;
    }

    /* ---------- Marca de agua (muy tenue para ahorrar tinta) ---------- */
    table.watermark { position: fixed; top: 370px; left: 0; width: 100%; z-index: -1; }
    table.watermark img { width: 320px; opacity: 0.04; }

    /* ---------- Encabezado ---------- */
    table.header { width: 100%; border-collapse: collapse; }
    table.header td { vertical-align: middle; padding: 0; }
    .logo-cell { width: 11%; }
    .logo-cell img { width: 60px; height: 42px; }
    .brand-cell { width: 59%; padding-left: 6px; }
    .brand-title { font-size: 17px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
    .brand-subtitle { font-size: 7px; color: #777777; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
    .ruc-cell { width: 30%; text-align: right; }
    .ruc-label { font-size: 7px; color: #777777; letter-spacing: 1px; text-transform: uppercase; }
    .ruc-value { font-size: 10px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 7px; color: #777777; margin-top: 1px; }

    .header-divider { border: none; border-top: 1.5px solid #1565c0; margin: 6px 0 9px 0; }

    /* ---------- Título del documento ---------- */
    table.doc-title-box { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.doc-title-box td { padding: 0 0 5px 0; border-bottom: 0.75px solid #c7d7ee; vertical-align: bottom; }
    .doc-title-main { color: #1565c0; font-size: 13px; font-weight: bold; letter-spacing: 0.6px; margin: 0; text-transform: uppercase; }
    .doc-title-sub { color: #777777; font-size: 7.5px; letter-spacing: 1.2px; text-transform: uppercase; margin: 2px 0 0 0; }
    .doc-title-code { text-align: right; font-size: 11px; font-weight: bold; color: #1565c0; }
    .doc-title-code span { display: block; font-size: 7px; font-weight: normal; color: #777777; letter-spacing: 1px; text-transform: uppercase; }

    /* ---------- Secciones ---------- */
    .section-title {
        margin: 11px 0 4px 0;
        padding-bottom: 2px;
        border-bottom: 1px solid #1565c0;
        font-size: 9px;
        font-weight: bold;
        color: #1565c0;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }
    .section-title span { color: #777777; margin-right: 6px; }

    /* ---------- Tabla de datos ---------- */
    table.info { width: 100%; border-collapse: collapse; }
    table.info th {
        width: 20%;
        text-align: left;
        padding: 3.5px 5px;
        font-size: 7px;
        font-weight: bold;
        color: #46586b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 0.75px solid #e2e6ea;
    }
    table.info td {
        width: 30%;
        padding: 3.5px 5px;
        font-size: 9px;
        color: #222222;
        border-bottom: 0.75px solid #e2e6ea;
    }

    /* ---------- Tabla de listado ---------- */
    table.lista { width: 100%; border-collapse: collapse; }
    table.lista th {
        text-align: left;
        padding: 3.5px 5px;
        font-size: 7px;
        color: #46586b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border-bottom: 1px solid #1565c0;
    }
    table.lista td {
        padding: 3.5px 5px;
        font-size: 8.5px;
        border-bottom: 0.75px solid #e2e6ea;
        vertical-align: top;
    }

    /* ---------- Firmas ---------- */
    table.firmas { width: 100%; border-collapse: collapse; margin-top: 22px; page-break-inside: avoid; }
    table.firmas td { width: 50%; text-align: center; padding-top: 34px; vertical-align: top; }
    .firma-linea { border-top: 0.75px solid #46586b; width: 70%; margin: 0 auto 4px auto; }
    table.firmas strong { display: block; font-size: 8.5px; color: #46586b; }
    table.firmas span { display: block; font-size: 7.5px; color: #777777; font-weight: normal; }

    /* ---------- Pie de página fijo ---------- */
    .footer-note {
        position: fixed;
        bottom: -30px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 6.5px;
        color: #777777;
        letter-spacing: 0.4px;
        border-top: 0.75px solid #d6dde5;
        padding-top: 4px;
    }

    .empty { color: #777777; font-style: italic; }
</style>