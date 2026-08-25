<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 50px 60px 60px 60px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #555; line-height: 1.6; margin: 0; padding: 0; }
    table.header { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table.header td { vertical-align: middle; }
    .brand-title { font-size: 26px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
    .brand-subtitle { font-size: 10px; color: #999; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
    .ruc-cell { text-align: right; }
    .ruc-label { font-size: 10px; color: #999; }
    .ruc-value { font-size: 11px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 9px; color: #999; }
    h1.titulo { text-align: center; font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 20px 0 25px 0; padding: 8px 0; text-decoration: underline; }
    .fecha { text-align: right; margin: 0 0 12px 0; color: #777; }
    p.destinatario { margin: 0; font-weight: bold; color: #555; }
    p.presente { margin: 0 0 18px 0; color: #555; }
    p.parrafo { text-align: justify; margin: 0 0 12px 0; }
    strong { color: #333; }
    table.firmas { width: 100%; margin-top: 60px; }
    table.firmas td { width: 50%; text-align: center; vertical-align: top; font-size: 10px; color: #555; }
    .firma-linea { border-top: 1px solid #999; width: 85%; margin: 0 auto 5px auto; }
    .footer-note { margin-top: 40px; text-align: center; font-size: 8px; color: #aaa; }
</style>
</head>
<body>
    <table width="100%" height="100%" style="position: fixed; top: 0; left: 0; z-index: -1;">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/icon.png') }}" style="width: 400px; opacity: 0.12;"></td></tr>
    </table>

    <table class="header">
        <tr>
            <td style="width: 15%;"><img src="{{ public_path('images/icon.png') }}" style="width: 65px; height: 65px;"></td>
            <td style="width: 55%;"><p class="brand-title">ARTURO MOTORS</p><p class="brand-subtitle">TECNOLOG&Iacute;A AUTOMOTRIZ</p></td>
            <td class="ruc-cell" style="width: 30%;"><div class="ruc-label">R.U.C.</div><div class="ruc-value">{{ $ruc ?? '20610295321' }}</div><div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0224-2025-MTC/17.03' }}</div></td>
        </tr>
    </table>

    <h1 class="titulo">CONSTANCIA DE ENTREGA</h1>
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
            <td><div class="firma-linea"></div>Firma del Cliente / Propietario<br>Nombre: {{ $nombre_cliente }}<br>D.N.I.: {{ $dni_cliente }}</td>
            <td><div class="firma-linea"></div>Por: ARTURO MOTORS S.A.C.<br>&Aacute;rea de Entrega y Control de Calidad</td>
        </tr>
    </table>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n de Conversiones &ndash; ARTURO MOTORS</p>
</body>
</html>
