<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Conversión GNV</title>
    <style>
        @page { margin: 35px 45px 50px 45px; }

        * { box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #3d3d3d;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* ---------- Marca de agua ---------- */
        table.watermark { width: 100%; height: 100%; position: fixed; top: 0; left: 0; z-index: -1; }
        table.watermark img { width: 340px; opacity: 0.05; }

        /* ---------- Encabezado ---------- */
        table.header { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.header td { vertical-align: middle; padding: 0; }
        .logo-cell { width: 14%; }
        .logo-cell img { width: 58px; height: 58px; }
        .brand-cell { width: 56%; padding-left: 8px; }
        .brand-title { font-size: 23px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1.2px; }
        .brand-subtitle { font-size: 8.5px; color: #9a9a9a; margin: 2px 0 0 0; letter-spacing: 2.5px; text-transform: uppercase; }
        .ruc-cell { text-align: right; width: 30%; }
        .ruc-label { font-size: 8.5px; color: #9a9a9a; letter-spacing: 1px; text-transform: uppercase; }
        .ruc-value { font-size: 11.5px; font-weight: bold; color: #1565c0; }
        .rd-value { font-size: 8px; color: #aaaaaa; margin-top: 1px; }

        .header-divider { border: none; border-top: 2.5px solid #1565c0; margin: 8px 0 2px 0; }
        .header-divider-thin { border: none; border-top: 0.75px solid #cfcfcf; margin: 0 0 16px 0; }

        /* ---------- Título del documento ---------- */
        table.doc-title-box {
            width: 100%;
            border-collapse: collapse;
            background: #1565c0;
            margin-bottom: 20px;
        }
        table.doc-title-box td { padding: 11px 14px; }
        .doc-title-main { color: #ffffff; font-size: 14.5px; font-weight: bold; letter-spacing: 0.5px; margin: 0; }
        .doc-title-sub { color: #d6e6fb; font-size: 8.5px; letter-spacing: 1.5px; text-transform: uppercase; margin: 3px 0 0 0; }
        .doc-title-code { color: #ffffff; text-align: right; font-size: 9px; }
        .doc-title-code span { display: block; color: #d6e6fb; font-size: 8px; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 2px; }

        /* ---------- Ficha rápida ---------- */
        table.summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            border: 1px solid #c7d7ee;
            background: #f4f8fd;
        }
        table.summary-box td {
            padding: 9px 12px;
            text-align: center;
            border-right: 1px solid #c7d7ee;
        }
        table.summary-box td:last-child { border-right: none; }
        .summary-label { font-size: 7.5px; color: #6f88a8; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 2px 0; }
        .summary-value { font-size: 12.5px; font-weight: bold; color: #1565c0; margin: 0; }

        /* ---------- Secciones ---------- */
        table.section-title {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 7px 0;
        }
        table.section-title td { padding: 0; }
        .section-bar { width: 5px; background: #1565c0; }
        .section-label {
            background: #eef2f7;
            color: #2c3e50;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .section-index {
            background: #eef2f7;
            color: #9aa8b8;
            font-size: 8.5px;
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
            width: 40%;
            padding: 5.5px 9px;
            font-weight: bold;
            background: #f7f9fb;
            border-bottom: 1px solid #e2e6ea;
            border-right: 1px solid #e2e6ea;
            font-size: 9px;
            color: #46586b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-table td {
            padding: 5.5px 9px;
            border-bottom: 1px solid #e2e6ea;
            font-size: 10px;
            color: #333333;
        }
        .info-table tr:last-child th,
        .info-table tr:last-child td { border-bottom: none; }
        .info-table tr:nth-child(even) td,
        .info-table tr:nth-child(even) th { background-color: #fbfcfd; }

        /* ---------- Tabla de historial ---------- */
        table.history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            border: 1px solid #d6dde5;
            font-size: 9.5px;
        }
        .history-table th {
            background: #eef2f7;
            color: #46586b;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 7px;
            border: 1px solid #d6dde5;
            text-align: left;
        }
        .history-table td {
            padding: 5.5px 7px;
            border: 1px solid #d6dde5;
        }
        .history-table tr:nth-child(even) td { background-color: #fbfcfd; }

        /* ---------- Firma ---------- */
        table.firma-box { width: 100%; border-collapse: collapse; margin-top: 38px; }
        .firma-cell { width: 50%; text-align: center; padding-top: 65px; }
        .firma-linea { border-top: 1px solid #9aa8b8; width: 65%; margin: 0 auto 5px auto; }
        .firma-cell p { margin: 0; font-weight: bold; font-size: 9px; color: #46586b; letter-spacing: 0.3px; }
        .firma-cell span { font-size: 8px; color: #9aa8b8; }

        /* ---------- Pie de página ---------- */
        .footer-note {
            margin-top: 24px;
            text-align: center;
            font-size: 7px;
            color: #b3b3b3;
            letter-spacing: 0.5px;
            border-top: 0.75px solid #e2e6ea;
            padding-top: 7px;
        }

        /* Helpers */
        .empty { color: #aaa; font-style: italic; }
    </style>
</head>
<body>

    <!-- Marca de agua -->
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
                <p class="doc-title-main">MANUAL DE CONVERSIÓN A GNV</p>
                <p class="doc-title-sub">Ficha técnica del vehículo y propietario</p>
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
            <td><p class="summary-label">Año</p><p class="summary-value">{{ $vehiculo->anio ?? '---' }}</p></td>
            <td><p class="summary-label">Placa</p><p class="summary-value">{{ $vehiculo->placa }}</p></td>
            <td><p class="summary-label">Combustible</p><p class="summary-value">{{ $vehiculo->combustible ?? '---' }}</p></td>
        </tr>
    </table>

    <!-- 01 · DATOS DEL PROPIETARIO -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">01 &nbsp;&middot;&nbsp; Datos del Propietario</td>
        </tr>
    </table>
    <table class="info-table">
        <tr><th>Apellidos / Nombres</th><td>{{ $propietario->nombre ?? '---' }} {{ $propietario->apellido ?? '' }}</td></tr>
        <tr><th>Tipo Documento</th><td>{{ $propietario->tipo_documento ?? '---' }}</td></tr>
        <tr><th>N° Documento</th><td>{{ $propietario->documento ?? '---' }}</td></tr>
        <tr><th>Dirección</th><td>{{ $propietario->direccion ?? '---' }}</td></tr>
        <tr><th>Teléfono</th><td>{{ $propietario->telefono ?? '---' }}</td></tr>
        <tr><th>Email</th><td>{{ $propietario->email ?? '---' }}</td></tr>
    </table>

    <!-- 02 · DATOS DEL VEHÍCULO -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">02 &nbsp;&middot;&nbsp; Datos del Vehículo</td>
        </tr>
    </table>
    <table class="info-table">
        <tr><th>Marca</th><td>{{ $vehiculo->marca }}</td></tr>
        <tr><th>Modelo</th><td>{{ $vehiculo->modelo }}</td></tr>
        <tr><th>Año</th><td>{{ $vehiculo->anio ?? '---' }}</td></tr>
        <tr><th>Placa</th><td>{{ $vehiculo->placa }}</td></tr>
        <tr><th>Color</th><td>{{ $vehiculo->color ?? '---' }}</td></tr>
        <tr><th>Combustible</th><td>{{ $vehiculo->combustible ?? '---' }}</td></tr>
        <tr><th>N° Serie / VIN</th><td>{{ $vehiculo->serie ?? '---' }}</td></tr>
        <tr><th>Color</th><td>{{ $vehiculo->color ?? '---' }}</td></tr>
    </table>

    <!-- 03 · HISTORIAL DE SERVICIOS -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">03 &nbsp;&middot;&nbsp; Historial de Servicios</td>
        </tr>
    </table>
    @if($servicios->count() > 0)
        <table class="history-table">
            <tr>
                <th style="width: 14%;">Fecha</th>
                <th style="width: 20%;">Servicio</th>
                <th style="width: 16%;">Estado</th>
                <th style="width: 14%;">Técnico</th>
                <th style="width: 14%;">Monto</th>
                <th style="width: 22%;">Observaciones</th>
            </tr>
            @foreach($servicios as $s)
                <tr>
                    <td>{{ $s->created_at->format('d/m/Y') }}</td>
                    <td>{{ $s->service?->nombre ?? 'Servicio' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $s->estado)) }}</td>
                    <td>{{ $s->tecnico?->name ?? '---' }}</td>
                    <td>S/ {{ number_format($s->precio_final ?? 0, 2) }}</td>
                    <td>{{ $s->evaluacion_observaciones ?? '---' }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="empty">No hay servicios registrados para este vehículo.</p>
    @endif

    <!-- 04 · PRÓXIMOS MANTENIMIENTOS SUGERIDOS -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">04 &nbsp;&middot;&nbsp; Próximos Mantenimientos Sugeridos</td>
        </tr>
    </table>
    <table class="info-table">
        <tr><th>Cada 6 meses</th><td>Revisión general del sistema GNV (fugas, conexiones, regulador)</td></tr>
        <tr><th>Cada 12 meses</th><td>Revisión de cilindro, válvulas y prueba de hermeticidad</td></tr>
        <tr><th>Cada 24 meses</th><td>Prueba hidrostática de cilindro (según normativa vigente)</td></tr>
        <tr><th>Próxima revisión</th><td>{{ $proximoMantenimiento ?? 'Según última cita: ' . ($ultimaCita?->fecha_cita?->format('d/m/Y') ?? 'Pendiente') }}</td></tr>
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

    <p class="footer-note">DOCUMENTO GENERADO POR EL SISTEMA DE GESTIÓN DE ARTURO MOTORS &mdash; {{ $fechaEmision }}</p>

</body>
</html>