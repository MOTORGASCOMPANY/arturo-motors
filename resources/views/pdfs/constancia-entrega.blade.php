<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Constancia de Entrega</title>
@include('pdfs._marca')
<style>
    @page { margin: 30px 60px 50px 60px; }

    body { font-size: 10px; line-height: 1.55; }

    .fecha { text-align: right; margin: 0 0 14px 0; color: #8a8a8a; font-size: 9.5px; }
    p.destinatario { margin: 0; font-weight: bold; color: #46586b; text-transform: uppercase; letter-spacing: 0.3px; font-size: 10px; }
    p.presente { margin: 2px 0 14px 0; color: #46586b; font-size: 10px; }
    p.parrafo { margin: 0 0 11px 0; text-align: justify; }
    p.parrafo strong { color: #1565c0; }

    table.firmas { margin-top: 20px; }
    table.firmas td { padding-top: 45px; }
</style>
</head>
<body>

    <table class="watermark">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/LOGOFINAL.jpg') }}"></td></tr>
    </table>

    <!-- ENCABEZADO -->
    <table class="header">
        <tr>
            <td class="logo-cell"><img src="{{ public_path('images/LOGOFINAL.jpg') }}"></td>
            <td class="brand-cell">
                <p class="brand-title">ARTURO MOTORS</p>
                <p class="brand-subtitle">Tecnología Automotriz</p>
            </td>
            <td class="ruc-cell">
                <div class="ruc-label">R.U.C.</div>
                <div class="ruc-value">{{ $ruc ?? '20610295321' }}</div>
                <div class="rd-value">R.D. N° {{ $rd_numero ?? '0224-2025-MTC/17.03' }}</div>
            </td>
        </tr>
    </table>
    <hr class="header-divider">

    <!-- TÍTULO -->
    <table class="doc-title-box">
        <tr>
            <td>
                <p class="doc-title-main">Constancia de Entrega</p>
                <p class="doc-title-sub">Conformidad de recepción del vehículo</p>
            </td>
        </tr>
    </table>

    <p class="fecha">{{ $ciudad_fecha }}</p>
    <p class="destinatario">Señores:</p>
    <p class="destinatario">ARTURO MOTORS S.A.C.</p>
    <p class="presente">Presente.-</p>

    <!-- RESUMEN DEL VEHÍCULO -->
    <table class="info" style="margin-bottom: 14px; border-top: 0.75px solid #c7d7ee;">
        <tr>
            <th>Cliente</th><td>{{ $nombre_cliente }}</td>
            <th>D.N.I.</th><td>{{ $dni_cliente }}</td>
        </tr>
        <tr>
            <th>Marca</th><td>{{ $marca_vehiculo }}</td>
            <th>Modelo</th><td>{{ $modelo_vehiculo }}</td>
        </tr>
        <tr>
            <th>Placa</th><td colspan="3"><strong>{{ $placa }}</strong></td>
        </tr>
    </table>

    <p class="parrafo">Por medio del presente documento, yo, <strong>{{ $nombre_cliente }}</strong>, identificado con D.N.I. número <strong>{{ $dni_cliente }}</strong>, en mi calidad de propietario y/o conductor autorizado del vehículo marca <strong>{{ $marca_vehiculo }}</strong>, modelo <strong>{{ $modelo_vehiculo }}</strong> y con placa de rodaje número <strong>{{ $placa }}</strong>, declaro lo siguiente:</p>

    <p class="parrafo">Que recibo a mi entera conformidad y satisfacción el vehículo anteriormente descrito por parte de la empresa <strong>ARTURO MOTORS S.A.C.</strong>, tras haberse culminado de manera satisfactoria el servicio de instalación y conversión al sistema de combustión de Gas Natural Vehicular (GNV).</p>

    <p class="parrafo">Asimismo, dejo constancia de que he procedido a realizar la inspección correspondiente del vehículo, constatando que el sistema instalado funciona de manera óptima y que la unidad es devuelta en perfectas condiciones mecánicas, operativas y estéticas, sin presentar daños ni observaciones al momento de esta entrega.</p>

    <p class="parrafo">De igual manera, declaro haber recibido la inducción básica sobre el uso del sistema de gas, así como los componentes físicos y la documentación técnica correspondiente de acuerdo al servicio contratado.</p>

    <p class="parrafo">En señal de mutuo acuerdo, conformidad y aceptación con los términos descritos en este documento, ambas partes procedemos a firmar la presente constancia.</p>

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
                <span>Área de Entrega y Control de Calidad</span>
            </td>
        </tr>
    </table>

    <p class="footer-note">DOCUMENTO GENERADO POR EL SISTEMA DE GESTIÓN DE ARTURO MOTORS</p>

</body>
</html>