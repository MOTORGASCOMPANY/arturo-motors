<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 50px 70px 60px 70px; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #555;
        line-height: 1.6;
        margin: 0;
        padding: 0;
    }
    table.header { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table.header td { vertical-align: middle; }
    .brand-title {
        font-size: 28px;
        font-weight: bold;
        color: #1565c0;
        margin: 0;
        letter-spacing: 1px;
        text-align: center;
    }
    .brand-subtitle {
        font-size: 11px;
        color: #999;
        margin: 4px 0 0 0;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-align: center;
    }
    .ruc-cell { text-align: right; }
    .ruc-label { font-size: 10px; color: #999; }
    .ruc-value { font-size: 12px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 9px; color: #999; }
    h1.titulo {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        color: #1a1a1a;
        margin: 40px 0 50px 0;
        padding: 10px 0;
        text-decoration: underline;
        letter-spacing: 1px;
    }
    .fecha { text-align: center; margin: 0 0 35px 0; color: #777; }
    p.senor { font-weight: bold; margin: 0 0 25px 0; color: #555; text-align: center; }
    p.parrafo {
        text-align: justify;
        margin: 0 auto 32px auto;
        max-width: 620px;
    }
    strong { color: #333; }
    .firma-wrap { margin-top: 130px; text-align: center; }
    .firma-linea {
        width: 280px;
        border-top: 1px solid #999;
        margin: 0 auto 14px auto;
    }
    .firma-texto { font-size: 12px; }
    .footer-note { margin-top: 90px; text-align: center; font-size: 8px; color: #aaa; }

    .contenido {
        max-width: 640px;
        margin: 0 auto;
    }
</style>
</head>
<body>

    {{-- Marca de agua --}}
    <table width="100%" height="100%" style="position: fixed; top: 0; left: 0; z-index: -1;">
        <tr>
            <td align="center" valign="middle">
                <img src="{{ public_path('images/icon.png') }}" style="width: 450px; opacity: 0.12;">
            </td>
        </tr>
    </table>

    {{-- Header --}}
    <table class="header">
        <tr>
            <td style="width: 15%;">
                <img src="{{ public_path('images/icon.png') }}" style="width: 70px; height: 70px;">
            </td>
            <td style="width: 55%;">
                <p class="brand-title">ARTURO MOTORS</p>
                <p class="brand-subtitle">TECNOLOG&Iacute;A AUTOMOTRIZ</p>
            </td>
            <td class="ruc-cell" style="width: 30%;">
                <div class="ruc-label">R.U.C.</div>
                <div class="ruc-value">{{ $ruc ?? '20610295321' }}</div>
                <div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0413-2023-MTC/17.03' }}</div>
            </td>
        </tr>
    </table>

    <h1 class="titulo">CARTA DE GARANT&Iacute;A</h1>

    <div class="contenido">

        <p class="fecha">{{ $ciudad_fecha }}</p>

        <p class="senor">SE&Ntilde;OR:</p>

        <p class="parrafo">
            POR MEDIO DE LA PRESENTE ME DIRIJO A USTED PARA HACERLE LLEGAR LA CARTA DE
            GARANT&Iacute;A DE {{ $anios_garantia }} A&Ntilde;OS DE LA INSTALACI&Oacute;N DEL EQUIPO DE GNV A SU AUTO, PLACA
            <strong>{{ $placa }}</strong>,
            EQUIPO DE LA MARCA <strong>{{ $marca_equipo }}</strong>,
            DE <strong>{{ $generacion }}</strong>,
            EN EL CUAL EL AUTO HA SALIDO EN PERFECTAS CONDICIONES.
        </p>

        <p class="parrafo">
            SE LE MENCIONA QUE DOS REVISIONES ANUALES GNV PUEDE REALIZARLA EN EL
            TALLER <strong>ARTURO MOTORS</strong> SIN NING&Uacute;N COSTO.
        </p>

        <p class="parrafo">
            LA GARANT&Iacute;A SE HAR&Aacute; EFECTIVA SI EL CLIENTE CUMPLE CON SU MANTENIMIENTO
            PREVENTIVO/CORRECTIVO, A LOS {{ $meses_mant }} MESES, TENIENDO COSTO DE S/.0.00. DOS
            MANTENIMIENTOS DE GAS PASANDO LOS {{ $km_mant }} KM DE RECORRIDO SIN COSTO
            ADICIONAL. SI NO SE REALIZARA EL MANTENIMIENTO INDICADO EL EQUIPO SE
            DETERIORAR&Aacute; DE UNA MANERA R&Aacute;PIDA Y LA EMPRESA NO SE HAR&Aacute; RESPONSABLE, POR
            ELLO SE LE RECOMIENDA CUMPLIR CON LO INDICADO PARA UN MEJOR CUIDADO DEL
            MOTOR DE LA UNIDAD.
        </p>

        <p class="parrafo">SIN OTRO PARTICULAR, ME DESPIDO.</p>

        <div class="firma-wrap">
            <div class="firma-linea"></div>
            <p class="firma-texto"><strong>Firma y Sello</strong></p>
        </div>

    </div>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n de Conversiones &ndash; ARTURO MOTORS</p>

</body>
</html>