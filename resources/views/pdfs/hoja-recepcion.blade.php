<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 25px 30px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #555; line-height: 1.4; margin: 0; padding: 0; }
    table.header { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .brand-title { font-size: 20px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
    .brand-subtitle { font-size: 8px; color: #999; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
    .ruc-cell { text-align: right; }
    .ruc-label { font-size: 8px; color: #999; }
    .ruc-value { font-size: 9px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 7px; color: #999; }
    h1.titulo { text-align: center; font-size: 13px; font-weight: bold; color: #1a1a1a; margin: 6px 0 4px 0; text-decoration: underline; }
    p.preinsp { text-align: center; font-weight: bold; margin: 0 0 6px 0; color: #555; font-size: 9px; }
    table.fechas { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    table.fechas td { padding: 2px 0; color: #555; font-size: 9px; }
    .section-title { background: #e0e0e0; font-weight: bold; padding: 3px 5px; margin-top: 6px; color: #333; font-size: 9px; }
    table.datos { width: 100%; border-collapse: collapse; border: 1.5px solid #999; border-top: none; margin-bottom: 6px; }
    table.datos td { padding: 3px 5px; border-bottom: 1px solid #ccc; color: #555; font-size: 9px; }
    table.datos td.label { font-weight: bold; width: 25%; color: #333; }
    table.recepcion { width: 100%; border-collapse: collapse; }
    table.recepcion td.esquema { width: 45%; border: 1.5px solid #999; vertical-align: top; padding: 4px; }
    table.recepcion td.accesorios { width: 55%; vertical-align: top; padding: 0; }
    table.acc-table { width: 100%; border-collapse: collapse; border: 1.5px solid #999; }
    table.acc-table th { background: #e0e0e0; border: 1px solid #999; padding: 2px 3px; font-size: 8px; color: #333; }
    table.acc-table td { border: 1px solid #999; padding: 2px 3px; color: #555; font-size: 8px; }
    table.acc-table td.chk { text-align: center; width: 18px; }
    .obs-box { border: 1.5px solid #999; margin-top: 6px; padding: 4px; min-height: 40px; color: #555; font-size: 9px; }
    table.firmas { width: 100%; margin-top: 25px; }
    table.firmas td { width: 50%; text-align: center; color: #555; font-size: 9px; }
    .firma-linea { border-top: 1px solid #999; width: 80%; margin: 0 auto 3px auto; }
    .footer-note { margin-top: 10px; text-align: center; font-size: 7px; color: #aaa; }
</style>
</head>
<body>
    <table width="100%" height="100%" style="position: fixed; top: 0; left: 0; z-index: -1;">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/icon.png') }}" style="width: 400px; opacity: 0.12;"></td></tr>
    </table>

    <table class="header">
        <tr>
            <td style="width: 15%;"><img src="{{ public_path('images/icon.png') }}" style="width: 50px; height: 50px;"></td>
            <td style="width: 55%;"><p class="brand-title">ARTURO MOTORS</p><p class="brand-subtitle">TECNOLOG&Iacute;A AUTOMOTRIZ</p></td>
            <td class="ruc-cell" style="width: 30%;"><div class="ruc-label">R.U.C.</div><div class="ruc-value">{{ $ruc ?? '20610295321' }}</div><div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0224-2025-MTC/17.03' }}</div></td>
        </tr>
    </table>

    <h1 class="titulo">HOJA DE RECEPCI&Oacute;N</h1>
    <p class="preinsp">{!! $tipo_inspeccion ?? 'PRE INSPECCI&Oacute;N' !!}</p>

    <table class="fechas">
        <tr><td style="width:50%;"><strong>Fecha de ingreso al taller:</strong> {{ $fecha_ingreso }}</td><td style="width:50%;"><strong>Fecha de salida al taller:</strong> {{ $fecha_salida }}</td></tr>
    </table>

    <div class="section-title">DATOS DEL DUE&Ntilde;O</div>
    <table class="datos">
        <tr><td class="label">Nombre:</td><td colspan="3">{{ $nombre_dueno }}</td></tr>
        <tr><td class="label">DNI:</td><td style="width:25%;">{{ $dni }}</td><td class="label" style="width:25%;">Tel&eacute;fono:</td><td style="width:25%;">{{ $telefono }}</td></tr>
    </table>

    <div class="section-title">DATOS Y CARACTER&Iacute;STICAS DEL VEH&Iacute;CULO</div>
    <table class="datos">
        <tr><td class="label">Placa Actual:</td><td>{{ $placa_actual }}</td><td class="label">Marca:</td><td>{{ $marca }}</td></tr>
        <tr><td class="label">Placa Anterior:</td><td>{{ $placa_anterior ?? 'NE' }}</td><td class="label">Modelo:</td><td>{{ $modelo }}</td></tr>
        <tr><td class="label">N&deg; Motor:</td><td>{{ $motor_num }}</td><td class="label">Color:</td><td>{{ $color }}</td></tr>
        <tr><td class="label">A&ntilde;o:</td><td>{{ $anio }}</td><td class="label">Combustible:</td><td>{{ $combustible ?? 'BI-COMBUSTIBLE GNV' }}</td></tr>
        <tr><td class="label">Kilometraje:</td><td colspan="3">{{ $kilometraje ?? 'NE' }}</td></tr>
    </table>

    <div class="section-title">RECEPCI&Oacute;N DEL VEH&Iacute;CULO</div>
    <table class="recepcion">
        <tr>
            <td class="esquema">
                <strong>Esquema de Da&ntilde;os</strong>
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

    <div class="section-title">OBSERVACIONES</div>
    <div class="obs-box">
        {!! nl2br(e($observaciones ?? '')) !!}
        <br><br><em>Con la presente yo y/o en representaci&oacute;n autorizo el trabajo a realizarse en mi veh&iacute;culo.</em>
    </div>

    <table class="firmas">
        <tr><td><div class="firma-linea"></div>Firma del Cliente</td><td><div class="firma-linea"></div>Firma Representante del Taller</td></tr>
    </table>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n de Conversiones &ndash; ARTURO MOTORS</p>
</body>
</html>
