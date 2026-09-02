<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 45px 60px 60px 60px; }

    * { box-sizing: border-box; }

    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #4a4a4a; line-height: 1.65; margin: 0; padding: 0; }

    /* ---------- Marca de agua ---------- */
    table.watermark { width: 100%; height: 100%; position: fixed; top: 0; left: 0; z-index: -1; }
    table.watermark img { width: 380px; opacity: 0.06; }

    /* ---------- Encabezado ---------- */
    table.header { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    table.header td { vertical-align: middle; padding: 0; }
    .logo-cell { width: 15%; }
    .logo-cell img { width: 62px; height: 62px; }
    .brand-cell { width: 55%; padding-left: 8px; }
    .brand-title { font-size: 25px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1.5px; }
    .brand-subtitle { font-size: 9px; color: #9a9a9a; margin: 2px 0 0 0; letter-spacing: 3px; text-transform: uppercase; }
    .ruc-cell { text-align: right; width: 30%; }
    .ruc-label { font-size: 9px; color: #9a9a9a; letter-spacing: 1px; text-transform: uppercase; }
    .ruc-value { font-size: 12px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 8.5px; color: #aaaaaa; margin-top: 1px; }

    .header-divider { border: none; border-top: 2.5px solid #1565c0; margin: 8px 0 2px 0; }
    .header-divider-thin { border: none; border-top: 0.75px solid #cfcfcf; margin: 0 0 18px 0; }

    /* ---------- Título del documento ---------- */
    table.doc-title-box { width: 100%; border-collapse: collapse; background: #1565c0; margin-bottom: 18px; }
    table.doc-title-box td { padding: 11px 16px; }
    .doc-title-main { color: #ffffff; font-size: 14.5px; font-weight: bold; letter-spacing: 0.6px; margin: 0; }
    .doc-title-sub { color: #d6e6fb; font-size: 8.5px; letter-spacing: 1.5px; text-transform: uppercase; margin: 3px 0 0 0; }

    .fecha { text-align: right; margin: 0 0 16px 0; color: #8a8a8a; font-size: 10px; }
    p.destinatario { margin: 0; font-weight: bold; color: #46586b; text-transform: uppercase; letter-spacing: 0.3px; font-size: 10.5px; }
    p.presente { margin: 2px 0 16px 0; color: #46586b; font-size: 10.5px; }
    p.parrafo { text-align: justify; margin: 0 0 13px 0; }
    strong { color: #1565c0; }

    /* ---------- Firmas ---------- */
    table.firmas { width: 100%; border-collapse: collapse; margin-top: 45px; }
    table.firmas td { width: 50%; text-align: center; vertical-align: top; font-size: 9.5px; color: #46586b; padding-top: 65px; }
    .firma-linea { border-top: 1px solid #9aa8b8; width: 85%; margin: 0 auto 6px auto; }
    table.firmas td strong { display: block; color: #46586b; font-size: 9.5px; margin-bottom: 2px; }
    table.firmas td span { display: block; color: #9aa8b8; font-size: 8.5px; }

    /* ---------- Pie de página ---------- */
    .footer-note { margin-top: 28px; text-align: center; font-size: 7.5px; color: #b3b3b3; letter-spacing: 0.5px; border-top: 0.75px solid #e2e6ea; padding-top: 8px; }
</style>
</head>
<body>
    <table class="watermark">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/icon.png') }}"></td></tr>
    </table>

    <!-- ENCABEZADO -->
    <table class="header">
        <tr>
            <td class="logo-cell"><img src="{{ public_path('images/icon.png') }}"></td>
            <td class="brand-cell">
                <p class="brand-title">ARTURO MOTORS</p>
                <p class="brand-subtitle">Tecnolog&iacute;a Automotriz</p>
            </td>
            <td class="ruc-cell">
                <div class="ruc-label">R.U.C.</div>
                <div class="ruc-value">{{ $ruc ?? '20610295321' }}</div>
                <div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0224-2025-MTC/17.03' }}</div>
            </td>
        </tr>
    </table>
    <hr class="header-divider">
    <hr class="header-divider-thin">

    <!-- TÍTULO DEL DOCUMENTO -->
    <table class="doc-title-box">
        <tr>
            <td>
                <p class="doc-title-main">CONSTANCIA DE ENTREGA</p>
                <p class="doc-title-sub">Conformidad de recepci&oacute;n del veh&iacute;culo</p>
            </td>
        </tr>
    </table>

    <p class="fecha">{{ $ciudad_fecha }}</p>
    <p class="destinatario">Se&ntilde;ores:</p>
    <p class="destinatario">ARTURO MOTORS S.A.C.</p>
    <p class="presente">Presente.-</p>

    <p class="parrafo">Por medio del presente documento, yo, <strong>{{ $nombre_cliente }}</strong>, identificado con D.N.I. n&uacute;mero <strong>{{ $dni_cliente }}</strong>, en mi calidad de propietario y/o conductor autorizado del veh&iacute;culo marca <strong>{{ $marca_vehiculo }}</strong>, modelo <strong>{{ $modelo_vehiculo }}</strong> y con placa de rodaje n&uacute;mero <strong>{{ $placa }}</strong>, declaro lo siguiente:</p>

    <p class="parrafo">Que, recibo a mi entera conformidad y satisfacci&oacute;n el veh&iacute;culo anteriormente descrito por parte de la empresa <strong>ARTURO MOTORS S.A.C.</strong>, tras haberse culminado de manera satisfactoria el servicio de instalaci&oacute;n y conversi&oacute;n al sistema de combusti&oacute;n de Gas Natural Vehicular (GNV).</p>

    <p class="parrafo">Asimismo, dejo constancia de que he procedido a realizar la inspecci&oacute;n correspondiente del veh&iacute;culo, constatando que el sistema instalado funciona de manera &oacute;ptima y que la unidad es devuelta en perfectas condiciones mec&aacute;nicas, operativas y est&eacute;ticas, sin presentar da&ntilde;os ni observaciones al momento de esta entrega.</p>

    <p class="parrafo">De igual manera, declaro haber recibido la inducci&oacute;n b&aacute;sica sobre el uso del sistema de gas, as&iacute; como los componentes f&iacute;sicos y la documentaci&oacute;n t&eacute;cnica correspondiente de acuerdo al servicio contratado.</p>

    <p class="parrafo">En se&ntilde;al de mutuo acuerdo, conformidad y aceptaci&oacute;n con los t&eacute;rminos descritos en este documento, ambas partes procedemos a firmar la presente constancia.</p>

    <p class="parrafo">Atentamente,</p>

    <table class="firmas">
        <tr>
            <td>
                <div class="firma-linea"></div>
                <strong>Firma del Cliente / Propietario</strong>
                <span>Nombre: {{ $nombre_cliente }}</span>
                <span>D.N.I.: {{ $dni_cliente }}</span>
            </td>
            <td>
                <div class="firma-linea"></div>
                <strong>Por: ARTURO MOTORS S.A.C.</strong>
                <span>&Aacute;rea de Entrega y Control de Calidad</span>
            </td>
        </tr>
    </table>

    <p class="footer-note">DOCUMENTO GENERADO POR EL SISTEMA DE GESTI&Oacute;N DE ARTURO MOTORS</p>
</body>
</html>