<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Mantenimiento y Conversión</title>
    <style>
        @page { margin: 45px 55px 65px 55px; }

        * { box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #4a4a4a;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

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

        /* ---------- Título del documento (estilo portada de manual) ---------- */
        table.doc-title-box {
            width: 100%;
            border-collapse: collapse;
            background: #1565c0;
            margin-bottom: 22px;
        }
        table.doc-title-box td { padding: 12px 16px; }
        .doc-title-main { color: #ffffff; font-size: 15px; font-weight: bold; letter-spacing: 0.5px; margin: 0; }
        .doc-title-sub { color: #d6e6fb; font-size: 9px; letter-spacing: 1.5px; text-transform: uppercase; margin: 3px 0 0 0; }
        .doc-title-code { color: #ffffff; text-align: right; font-size: 9px; }
        .doc-title-code span { display: block; color: #d6e6fb; font-size: 8px; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 2px; }

        /* ---------- Resumen del vehículo (ficha rápida tipo carátula) ---------- */
        table.summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            border: 1px solid #c7d7ee;
            background: #f4f8fd;
        }
        table.summary-box td {
            padding: 10px 14px;
            text-align: center;
            border-right: 1px solid #c7d7ee;
        }
        table.summary-box td:last-child { border-right: none; }
        .summary-label { font-size: 8px; color: #6f88a8; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 3px 0; }
        .summary-value { font-size: 13px; font-weight: bold; color: #1565c0; margin: 0; }

        /* ---------- Secciones ---------- */
        table.section-title {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 8px 0;
        }
        table.section-title td { padding: 0; }
        .section-bar {
            width: 6px;
            background: #1565c0;
        }
        .section-label {
            background: #eef2f7;
            color: #2c3e50;
            padding: 6px 10px;
            font-size: 11.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .section-index {
            background: #eef2f7;
            color: #9aa8b8;
            font-size: 9px;
            font-weight: bold;
            padding-right: 10px;
            text-align: right;
        }

        /* ---------- Tablas de información ---------- */
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            border: 1px solid #d6dde5;
        }
        .info-table th {
            text-align: left;
            width: 38%;
            padding: 6px 10px;
            font-weight: bold;
            background: #f7f9fb;
            border-bottom: 1px solid #e2e6ea;
            border-right: 1px solid #e2e6ea;
            font-size: 9.5px;
            color: #46586b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e6ea;
            font-size: 10.5px;
            color: #333333;
        }
        .info-table tr:last-child th,
        .info-table tr:last-child td { border-bottom: none; }
        .info-table tr:nth-child(even) td,
        .info-table tr:nth-child(even) th { background-color: #fbfcfd; }

        /* ---------- Notas / historial de mantenimiento ---------- */
        table.notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            border: 1px solid #d6dde5;
        }
        .notes-table th {
            background: #eef2f7;
            color: #46586b;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 6px 8px;
            border: 1px solid #d6dde5;
            text-align: left;
        }
        .notes-table td {
            padding: 14px 8px;
            border: 1px solid #d6dde5;
            font-size: 10px;
        }

        /* ---------- Firma ---------- */
        table.firma-box { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .firma-cell { width: 50%; text-align: center; padding-top: 70px; }
        .firma-linea { border-top: 1px solid #9aa8b8; width: 70%; margin: 0 auto 6px auto; }
        .firma-cell p { margin: 0; font-weight: bold; font-size: 9.5px; color: #46586b; letter-spacing: 0.3px; }
        .firma-cell span { font-size: 8.5px; color: #9aa8b8; }

        /* ---------- Pie de página ---------- */
        .footer-note {
            margin-top: 26px;
            text-align: center;
            font-size: 7.5px;
            color: #b3b3b3;
            letter-spacing: 0.5px;
            border-top: 0.75px solid #e2e6ea;
            padding-top: 8px;
        }
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
                <p class="brand-subtitle">Tecnología Automotriz</p>
            </td>
            <td class="ruc-cell">
                <div class="ruc-label">R.U.C.</div>
                <div class="ruc-value">{{ $ruc ?? '20610295321' }}</div>
                <div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0413-2023-MTC/17.03' }}</div>
            </td>
        </tr>
    </table>
    <hr class="header-divider">
    <hr class="header-divider-thin">

    <!-- TÍTULO DEL DOCUMENTO -->
    <table class="doc-title-box">
        <tr>
            <td style="width: 70%;">
                <p class="doc-title-main">MANUAL DEL VEH&Iacute;CULO &ndash; SERVICIOS Y MANTENIMIENTO</p>
                <p class="doc-title-sub">Ficha t&eacute;cnica y datos del propietario</p>
            </td>
            <td class="doc-title-code" style="width: 30%;">
                <span>Placa</span>
                {{ $vehiculo->placa }}
            </td>
        </tr>
    </table>

    <!-- FICHA RÁPIDA -->
    <table class="summary-box">
        <tr>
            <td><p class="summary-label">Marca / Modelo</p><p class="summary-value">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p></td>
            <td><p class="summary-label">A&ntilde;o</p><p class="summary-value">{{ $vehiculo->anio ?? '---' }}</p></td>
            <td><p class="summary-label">Placa</p><p class="summary-value">{{ $vehiculo->placa }}</p></td>
            <td><p class="summary-label">Kilometraje</p><p class="summary-value">{{ $vehiculo->kilometraje ?? '---' }}</p></td>
        </tr>
    </table>

    <!-- DATOS DEL CLIENTE -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">01 &nbsp;&middot;&nbsp; Datos del Cliente</td>
        </tr>
    </table>
    <table class="info-table">
        <tr><th>Apellidos / Nombres</th><td>{{ $vehiculo->clientes->first()?->nombre ?? '---' }} {{ $vehiculo->clientes->first()?->apellido ?? '' }}</td></tr>
        <tr><th>DNI / CE / RUC</th><td>{{ $vehiculo->clientes->first()?->documento ?? '---' }}</td></tr>
        <tr><th>Direcci&oacute;n</th><td>{{ $vehiculo->clientes->first()?->direccion ?? '---' }}</td></tr>
        <tr><th>Tel&eacute;fono</th><td>{{ $vehiculo->clientes->first()?->telefono ?? '---' }}</td></tr>
    </table>

    <!-- DATOS DEL VEHÍCULO -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">02 &nbsp;&middot;&nbsp; Datos del Veh&iacute;culo</td>
        </tr>
    </table>
    <table class="info-table">
        <tr><th>Marca</th><td>{{ $vehiculo->marca }}</td></tr>
        <tr><th>Modelo</th><td>{{ $vehiculo->modelo }}</td></tr>
        <tr><th>A&ntilde;o</th><td>{{ $vehiculo->anio ?? '---' }}</td></tr>
        <tr><th>Placa</th><td>{{ $vehiculo->placa }}</td></tr>
        <tr><th>Color</th><td>{{ $vehiculo->color ?? '---' }}</td></tr>
        <tr><th>Combustible</th><td>{{ $vehiculo->combustible ?? '---' }}</td></tr>
        <tr><th>N&deg; Serie (Motor)</th><td>{{ $vehiculo->serie ?? '---' }}</td></tr>
    </table>

    <!-- INFORMACIÓN DE INSTALACIÓN -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">03 &nbsp;&middot;&nbsp; Informaci&oacute;n de Instalaci&oacute;n</td>
        </tr>
    </table>
    <table class="info-table">
        <tr><th>Fecha de Instalaci&oacute;n</th><td>{{ $vehiculo->cita->last()?->fecha_cita?->format('d/m/Y') ?? '---' }}</td></tr>
        <tr><th>Kilometraje Actual</th><td>{{ $vehiculo->kilometraje ?? '---' }}</td></tr>
        <tr><th>Tipo de Equipo</th><td>{{ $vehiculo->equipo ?? '---' }}</td></tr>
        <tr><th>Marca / Modelo Equipo</th><td>{{ $vehiculo->Equipomarca ?? '---' }} / {{ $vehiculo->Equipomodelo ?? '---' }}</td></tr>
        <tr><th>N&deg; Serie Equipo</th><td>{{ $vehiculo->Equiposerie ?? '---' }}</td></tr>
        <tr><th>Central de Control</th><td>{{ $vehiculo->central ?? '---' }}</td></tr>
        <tr><th>Electr&oacute;nica Adicional</th><td>{{ $vehiculo->electronica ?? '---' }}</td></tr>
        <tr><th>Otros Dispositivos</th><td>{{ $vehiculo->otros ?? '---' }}</td></tr>
    </table>

    <!-- DATOS DEL TANQUE -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">04 &nbsp;&middot;&nbsp; Datos del Tanque</td>
        </tr>
    </table>
    <table class="info-table">
        <tr><th>Tipo de Tanque</th><td>{{ $vehiculo->tanque ?? '---' }}</td></tr>
        <tr><th>Marca / Modelo Tanque</th><td>{{ $vehiculo->Tanquemarca ?? '---' }} / {{ $vehiculo->Tanquemodelo ?? '---' }}</td></tr>
        <tr><th>N&deg; Serie Tanque</th><td>{{ $vehiculo->Tanqueserie ?? '---' }}</td></tr>
        <tr><th>Multiv&aacute;lvulas</th><td>{{ $vehiculo->multivalvulas ?? '---' }}</td></tr>
        <tr><th>Toma de Carga</th><td>{{ $vehiculo->toma ?? '---' }}</td></tr>
        <tr><th>N&deg; Constancia Aprobaci&oacute;n</th><td>{{ $vehiculo->Tanqueaprobacion ?? '---' }}</td></tr>
        <tr><th>N&deg; Expediente</th><td>{{ $vehiculo->Tanqueexpediente ?? '---' }}</td></tr>
    </table>

    <!-- HISTORIAL DE MANTENIMIENTO (formato manual del vehículo) -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">05 &nbsp;&middot;&nbsp; Historial de Mantenimiento</td>
        </tr>
    </table>
    <table class="notes-table">
        <tr>
            <th style="width: 18%;">Fecha</th>
            <th style="width: 18%;">Kilometraje</th>
            <th style="width: 44%;">Trabajo Realizado</th>
            <th style="width: 20%;">T&eacute;cnico</th>
        </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    </table>

    <!-- FIRMA -->
    <table class="firma-box">
        <tr>
            <td class="firma-cell">
                <div class="firma-linea"></div>
                <p>Sello y Firma del Taller</p>
                <span>ARTURO MOTORS</span>
            </td>
            <td class="firma-cell">
                <div class="firma-linea"></div>
                <p>Firma del Cliente</p>
                <span>Conformidad de Servicio</span>
            </td>
        </tr>
    </table>

    <p class="footer-note">DOCUMENTO GENERADO POR EL SISTEMA DE GESTI&Oacute;N DE ARTURO MOTORS</p>

</body>
</html>