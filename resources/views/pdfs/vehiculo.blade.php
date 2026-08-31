<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CARTA DE GARANTIA</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; line-height: 1.5; color: #555; }
        .contenido { padding: 50mm 20mm 25mm 20mm; }
        .fecha { text-align: right; margin-bottom: 8mm; font-size: 11pt; color: #777; }
        h3 { text-align: center; text-decoration: underline; margin: 0 0 8mm 0; font-size: 13pt; color: #1a1a1a; }
        h4 { margin: 0 0 4mm 0; font-size: 12pt; color: #555; }
        p { text-align: justify; margin: 0 0 4mm 0; }
        strong { color: #333; }
        .firma { margin-top: 30mm; text-align: center; }
        .firma .linea { margin-top: 18mm; border-top: 1px solid #999; width: 40%; margin-left: auto; margin-right: auto; }
        .firma p { text-align: center; margin-top: 4mm; color: #555; }
        .footer-note { margin-top: 20mm; text-align: center; font-size: 8px; color: #aaa; }
    </style>
</head>
<body background="{{ str_replace('\\', '/', public_path('images/hoja_membretada_small.png')) }}">
    <table width="100%" height="100%" style="position: fixed; top: 0; left: 0; z-index: -1;">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/icon.png') }}" style="width: 400px; opacity: 0.12;"></td></tr>
    </table>

    <main class="contenido">
        <p class="fecha">Lima, {{ $fecha }}</p>
        <h3>CARTA DE GARANTIA</h3>
        <h4>SE&Ntilde;OR:</h4>
        <p>POR MEDIO DE LA PRESENTE ME DIRIJO A USTED PARA HACERLE LLEGAR LA CARTA DE GARANT&Iacute;A DE 02 A&Ntilde;OS DE LA INSTALACI&Oacute;N DEL EQUIPO DE GNV A SU AUTO, PLACA <strong>{{ $vehiculo->placa }}</strong>, EQUIPO DE LA MARCA <strong>IGT MOTORS</strong>, DE <strong>5TA GENERACI&Oacute;N,</strong> EN EL CUAL EL AUTO HA SALIDO EN PERFECTAS CONDICIONES.</p>
        <p>SE LE MENCIONA QUE DOS REVISIONES ANUALES GNV PUEDE REALIZARLA EN EL TALLER <strong>ARTURO MOTORS</strong> SIN NING&Uacute;N COSTO.</p>
        <p>LA GARANT&Iacute;A SE HAR&Aacute; EFECTIVA SI EL CLIENTE CUMPLE CON SU MANTENIMIENTO PREVENTIVO/CORRECTIVO, <strong>A LOS 7 MESES, TENIENDO COSTO DE S/.0.00. DOS MANTENIMIENTOS DE GAS PASANDO LOS 15,000 KM DE RECORRIDO SIN COSTO ADICIONAL.</strong> SI NO SE REALIZARA EL MANTENIMIENTO INDICADO EL EQUIPO SE DETERIORA DE UNA MANERA R&Aacute;PIDA Y LA EMPRESA NO SE HAR&Aacute; RESPONSABLE, POR ELLO SE LE RECOMIENDA CUMPLIR CON LO INDICADO PARA UN MEJOR CUIDADO DEL MOTOR DE LA UNIDAD.</p>
        <p>SIN OTRO PARTICULAR, ME DESPIDO.</p>
        <div class="firma"><div class="linea"></div><p><strong>Firma y Sello</strong></p></div>
        <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n Automotriz/Arturo Motors</p>
    </main>
</body>
</html>
