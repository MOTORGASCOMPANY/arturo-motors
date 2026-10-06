<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carta de Garantía</title>
@include('pdfs._marca')
<style>
    @page { margin: 30px 60px 50px 60px; }

    body { font-size: 10px; line-height: 1.6; }

    .fecha { text-align: right; margin: 0 0 14px 0; color: #8a8a8a; font-size: 9.5px; }
    p.senor { font-weight: bold; margin: 0 0 14px 0; color: #46586b; text-transform: uppercase; letter-spacing: 0.3px; font-size: 10px; }
    p.parrafo { margin: 0 0 12px 0; text-align: justify; }
    p.parrafo strong { color: #1565c0; }

    ol.condiciones { margin: 6px 0 14px 0; padding-left: 18px; }
    ol.condiciones li { margin-bottom: 8px; text-align: justify; }

    .firma-wrap { margin-top: 60px; text-align: center; page-break-inside: avoid; }
    .firma-wrap .firma-linea { width: 40%; }
    .firma-wrap strong { display: block; font-size: 9.5px; color: #46586b; }
    .firma-wrap span { display: block; font-size: 8px; color: #8a8a8a; }
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
                <div class="rd-value">R.D. N° {{ $rd_numero ?? '0413-2023-MTC/17.03' }}</div>
            </td>
        </tr>
    </table>
    <hr class="header-divider">

    <!-- TÍTULO -->
    <table class="doc-title-box">
        <tr>
            <td>
                <p class="doc-title-main">Carta de Garantía</p>
                <p class="doc-title-sub">Instalación de equipo GNV</p>
            </td>
        </tr>
    </table>

    <p class="fecha">{{ $ciudad_fecha }}</p>
    <p class="senor">Señor(a):</p>

    <p class="parrafo">
        Por medio de la presente nos dirigimos a usted para hacerle llegar la <strong>Carta de Garantía de {{ $anios_garantia }} años</strong>
        por la instalación del equipo de GNV en su vehículo, cuyos datos se detallan a continuación.
        El vehículo ha salido del taller en perfectas condiciones.
    </p>

    <!-- DATOS DE LA GARANTÍA -->
    <table class="info" style="margin-bottom: 14px; border-top: 0.75px solid #c7d7ee;">
        <tr>
            <th>Placa</th><td><strong>{{ $placa }}</strong></td>
            <th>Vigencia</th><td>{{ $anios_garantia }} años</td>
        </tr>
        <tr>
            <th>Marca del equipo</th><td>{{ $marca_equipo }}</td>
            <th>Generación</th><td>{{ $generacion }}</td>
        </tr>
    </table>

    <div class="section-title">Condiciones de la garantía</div>
    <ol class="condiciones">
        <li>
            Dos revisiones anuales del sistema GNV pueden realizarse en el taller <strong>ARTURO MOTORS</strong> sin ningún costo.
        </li>
        <li>
            La garantía se hará efectiva siempre que el cliente cumpla con su mantenimiento preventivo/correctivo a los
            <strong>{{ $meses_mant }} meses</strong>, con un costo de S/ 0.00.
        </li>
        <li>
            Se incluyen dos mantenimientos de gas al superar los <strong>{{ $km_mant }} km</strong> de recorrido, sin costo adicional.
        </li>
        <li>
            Si no se realiza el mantenimiento indicado, el equipo se deteriorará de manera más rápida y la empresa no se hará responsable.
            Por ello se recomienda cumplir con lo indicado, para un mejor cuidado del motor de la unidad.
        </li>
    </ol>

    <p class="parrafo">Sin otro particular, nos despedimos.</p>

    <div class="firma-wrap">
        <div class="firma-linea"></div>
        <strong>Firma y Sello</strong>
        <span>ARTURO MOTORS</span>
    </div>

    <p class="footer-note">Documento generado por el Sistema de Gestión Automotriz / Arturo Motors</p>

</body>
</html>