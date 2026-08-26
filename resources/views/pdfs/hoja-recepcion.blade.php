<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 22px 28px 35px 28px; }

    * { box-sizing: border-box; }

    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #4a4a4a; line-height: 1.4; margin: 0; padding: 0; }

    /* ---------- Marca de agua ---------- */
    table.watermark { width: 100%; height: 100%; position: fixed; top: 0; left: 0; z-index: -1; }
    table.watermark img { width: 360px; opacity: 0.06; }

    /* ---------- Encabezado ---------- */
    table.header { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
    table.header td { vertical-align: middle; padding: 0; }
    .logo-cell { width: 15%; }
    .logo-cell img { width: 48px; height: 48px; }
    .brand-cell { width: 55%; padding-left: 6px; }
    .brand-title { font-size: 19px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
    .brand-subtitle { font-size: 7.5px; color: #9a9a9a; margin: 2px 0 0 0; letter-spacing: 2.5px; text-transform: uppercase; }
    .ruc-cell { text-align: right; width: 30%; }
    .ruc-label { font-size: 7.5px; color: #9a9a9a; letter-spacing: 1px; text-transform: uppercase; }
    .ruc-value { font-size: 10px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 7px; color: #aaaaaa; margin-top: 1px; }

    .header-divider { border: none; border-top: 2px solid #1565c0; margin: 6px 0 2px 0; }
    .header-divider-thin { border: none; border-top: 0.75px solid #cfcfcf; margin: 0 0 8px 0; }

    /* ---------- Título del documento ---------- */
    table.doc-title-box { width: 100%; border-collapse: collapse; background: #1565c0; margin-bottom: 10px; }
    table.doc-title-box td { padding: 7px 12px; vertical-align: middle; }
    .doc-title-main { color: #ffffff; font-size: 12.5px; font-weight: bold; letter-spacing: 0.5px; margin: 0; }
    .doc-title-sub { color: #d6e6fb; font-size: 8px; letter-spacing: 1.2px; text-transform: uppercase; margin: 2px 0 0 0; }
    .doc-title-tag { color: #ffffff; text-align: right; font-size: 9px; font-weight: bold; }

    /* ---------- Fechas ---------- */
    table.fechas { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    table.fechas td { padding: 5px 10px; color: #46586b; font-size: 8.5px; background: #f4f8fd; border: 1px solid #c7d7ee; }
    table.fechas td strong { color: #1565c0; }

    /* ---------- Secciones ---------- */
    table.section-title { width: 100%; border-collapse: collapse; margin: 10px 0 4px 0; }
    table.section-title td { padding: 0; }
    .section-bar { width: 5px; background: #1565c0; }
    .section-label { background: #eef2f7; color: #2c3e50; padding: 4px 8px; font-size: 9px; font-weight: bold; letter-spacing: 0.4px; text-transform: uppercase; }

    /* ---------- Tablas de datos ---------- */
    table.datos { width: 100%; border-collapse: collapse; border: 1px solid #d6dde5; margin-bottom: 4px; }
    table.datos td { padding: 4px 8px; border-bottom: 1px solid #e2e6ea; color: #333333; font-size: 9px; }
    table.datos tr:last-child td { border-bottom: none; }
    table.datos td.label { font-weight: bold; width: 25%; color: #46586b; text-transform: uppercase; font-size: 8px; letter-spacing: 0.3px; background: #f7f9fb; border-right: 1px solid #e2e6ea; }

    /* ---------- Recepción: esquema + accesorios ---------- */
    table.recepcion { width: 100%; border-collapse: collapse; }
    table.recepcion td.esquema { width: 45%; border: 1px solid #d6dde5; vertical-align: top; padding: 6px; background: #fbfcfd; }
    table.recepcion td.accesorios { width: 55%; vertical-align: top; padding: 0 0 0 8px; }
    .esquema-titulo { font-weight: bold; color: #46586b; font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.3px; }

    table.acc-table { width: 100%; border-collapse: collapse; border: 1px solid #d6dde5; }
    table.acc-table th { background: #eef2f7; border: 1px solid #d6dde5; padding: 3px 4px; font-size: 7.5px; color: #46586b; text-transform: uppercase; }
    table.acc-table td { border: 1px solid #d6dde5; padding: 3px 4px; color: #4a4a4a; font-size: 8px; }
    table.acc-table td.chk { text-align: center; width: 16px; color: #1565c0; font-weight: bold; }

    /* ---------- Observaciones ---------- */
    .obs-box { border: 1px solid #d6dde5; margin-top: 6px; padding: 8px; min-height: 46px; color: #4a4a4a; font-size: 9px; background: #fbfcfd; }
    .obs-box em { color: #6f88a8; }

    /* ---------- Firmas ---------- */
    table.firmas { width: 100%; border-collapse: collapse; margin-top: 45px; }
    table.firmas td { width: 50%; text-align: center; color: #46586b; font-size: 9px; padding-top: 65px; font-weight: bold; }
    .firma-linea { border-top: 1px solid #9aa8b8; width: 78%; margin: 0 auto 6px auto; }
    .firma-cell span { display: block; font-weight: normal; font-size: 7.5px; color: #9aa8b8; margin-top: 2px; }

    /* ---------- Pie de página ---------- */
    .footer-note { margin-top: 14px; text-align: center; font-size: 7px; color: #b3b3b3; letter-spacing: 0.4px; border-top: 0.75px solid #e2e6ea; padding-top: 6px; }
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
            <td style="width: 70%;">
                <p class="doc-title-main">HOJA DE RECEPCI&Oacute;N DEL VEH&Iacute;CULO</p>
                <p class="doc-title-sub">Registro de ingreso, estado y accesorios</p>
            </td>
            <td class="doc-title-tag" style="width: 30%;">{!! $tipo_inspeccion ?? 'PRE INSPECCI&Oacute;N' !!}</td>
        </tr>
    </table>

    <!-- FECHAS -->
    <table class="fechas">
        <tr>
            <td style="width:50%; border-right: none;"><strong>Fecha de ingreso al taller:</strong> {{ $fecha_ingreso }}</td>
            <td style="width:50%;"><strong>Fecha de salida del taller:</strong> {{ $fecha_salida }}</td>
        </tr>
    </table>

    <!-- DATOS DEL DUEÑO -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">Datos del Due&ntilde;o</td>
        </tr>
    </table>
    <table class="datos">
        <tr><td class="label">Nombre</td><td colspan="3">{{ $nombre_dueno }}</td></tr>
        <tr><td class="label">DNI</td><td style="width:25%;">{{ $dni }}</td><td class="label" style="width:25%;">Tel&eacute;fono</td><td style="width:25%;">{{ $telefono }}</td></tr>
    </table>

    <!-- DATOS DEL VEHÍCULO -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">Datos y Caracter&iacute;sticas del Veh&iacute;culo</td>
        </tr>
    </table>
    <table class="datos">
        <tr><td class="label">Placa Actual</td><td>{{ $placa_actual }}</td><td class="label">Marca</td><td>{{ $marca }}</td></tr>
        <tr><td class="label">Placa Anterior</td><td>{{ $placa_anterior ?? 'NE' }}</td><td class="label">Modelo</td><td>{{ $modelo }}</td></tr>
        <tr><td class="label">N&deg; Motor</td><td>{{ $motor_num }}</td><td class="label">Color</td><td>{{ $color }}</td></tr>
        <tr><td class="label">A&ntilde;o</td><td>{{ $anio }}</td><td class="label">Combustible</td><td>{{ $combustible ?? 'BI-COMBUSTIBLE GNV' }}</td></tr>
        <tr><td class="label">Kilometraje</td><td colspan="3">{{ $kilometraje ?? 'NE' }}</td></tr>
    </table>

    <!-- RECEPCIÓN DEL VEHÍCULO -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">Recepci&oacute;n del Veh&iacute;culo</td>
        </tr>
    </table>
    <table class="recepcion">
        <tr>
            <td class="esquema">
                <span class="esquema-titulo">Esquema de Da&ntilde;os</span>
                <div style="text-align: center; padding: 3px; margin-top: 3px;">
                    <img src="{{ public_path('images/Diagrama-vechiculos.png') }}" style="width: 100%; max-width: 260px; height: auto;">
                </div>
            </td>
            <td class="accesorios">
                <table class="acc-table">
                    <tr><th style="width:44%; text-align:left;">Accesorios</th><th class="chk">SI</th><th class="chk">NO</th><th style="width:44%; text-align:left;">Accesorios</th><th class="chk">SI</th><th class="chk">NO</th></tr>
                    @php $izq = $accesorios_izq ?? []; $der = $accesorios_der ?? []; $maxRows = max(count($izq), count($der)); @endphp
                    @for ($i = 0; $i < $maxRows; $i++)
                    <tr>
                        @php $itemIzq = $izq[$i] ?? null; @endphp
                        <td>{{ $itemIzq['nombre'] ?? '' }}</td>
                        <td class="chk">{!! ($itemIzq && ($itemIzq['si'] ?? false)) ? '&#10003;' : '' !!}</td>
                        <td class="chk">{!! ($itemIzq && !($itemIzq['si'] ?? true)) ? '&#10003;' : '' !!}</td>
                        @php $itemDer = $der[$i] ?? null; @endphp
                        <td>{{ $itemDer['nombre'] ?? '' }}</td>
                        <td class="chk">{!! ($itemDer && ($itemDer['si'] ?? false)) ? '&#10003;' : '' !!}</td>
                        <td class="chk">{!! ($itemDer && !($itemDer['si'] ?? true)) ? '&#10003;' : '' !!}</td>
                    </tr>
                    @endfor
                </table>
            </td>
        </tr>
    </table>

    <!-- OBSERVACIONES -->
    <table class="section-title">
        <tr>
            <td class="section-bar"></td>
            <td class="section-label">Observaciones</td>
        </tr>
    </table>
    <div class="obs-box">
        {!! nl2br(e($observaciones ?? '')) !!}
        <br><br><em>Con la presente yo y/o en representaci&oacute;n autorizo el trabajo a realizarse en mi veh&iacute;culo.</em>
    </div>

    <!-- FIRMAS -->
    <table class="firmas">
        <tr>
            <td class="firma-cell">
                <div class="firma-linea"></div>
                Firma del Cliente
                <span>Conformidad de Recepci&oacute;n</span>
            </td>
            <td class="firma-cell">
                <div class="firma-linea"></div>
                Firma Representante del Taller
                <span>ARTURO MOTORS</span>
            </td>
        </tr>
    </table>

    </body>
</html>