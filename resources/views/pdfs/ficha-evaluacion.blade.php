<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Recepci&oacute;n - Ficha Evaluaci&oacute;n</title>
    <style>
        @page { margin: 30px 35px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #555; line-height: 1.5; margin: 0; padding: 0; }
        table.header { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .brand-title { font-size: 22px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
        .brand-subtitle { font-size: 9px; color: #999; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
        .ruc-cell { text-align: right; }
        .ruc-label { font-size: 9px; color: #999; }
        .ruc-value { font-size: 10px; font-weight: bold; color: #1565c0; }
        .rd-value { font-size: 8px; color: #999; }
        h2.titulo { text-align: center; font-size: 14px; font-weight: bold; color: #1a1a1a; margin: 8px 0 10px 0; text-decoration: underline; }
        .section-title { background: #e0e0e0; color: #333; padding: 4px 6px; font-weight: bold; margin-top: 10px; margin-bottom: 6px; font-size: 10px; }
        .field-label { font-weight: bold; color: #333; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 1px solid #eee; }
        .info-table td { padding: 4px; vertical-align: top; border: none; color: #555; }
        .accessories-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9px; border: 1px solid #eee; }
        .accessories-table td, .accessories-table th { border: 1px solid #eee; padding: 2px 4px; text-align: left; vertical-align: top; }
        .accessories-table th { background-color: #e0e0e0; text-align: center; font-weight: bold; color: #333; }
        .accessories-table td.center-text { text-align: center; }
        .carro-esquema { padding: 0; background-repeat: no-repeat; background-position: center; background-size: 80% 100%; height: 240px; }
        .check-icon { height: 12px; width: auto; }
        .footer-note { margin-top: 15px; text-align: center; font-size: 8px; color: #aaa; }
    </style>
</head>
<body>
    <table width="100%" height="100%" style="position: fixed; top: 0; left: 0; z-index: -1;">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/icon.png') }}" style="width: 400px; opacity: 0.12;"></td></tr>
    </table>

    <table class="header">
        <tr>
            <td style="width: 15%;"><img src="{{ public_path('images/icon.png') }}" style="width: 55px; height: 55px;"></td>
            <td style="width: 55%;"><p class="brand-title">ARTURO MOTORS</p><p class="brand-subtitle">TECNOLOG&Iacute;A AUTOMOTRIZ</p></td>
            <td class="ruc-cell" style="width: 30%;"><div class="ruc-label">R.U.C.</div><div class="ruc-value">{{ $ruc ?? '20610295321' }}</div><div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0413-2023-MTC/17.03' }}</div></td>
        </tr>
    </table>

    <h2 class="titulo">HOJA DE RECEPCI&Oacute;N</h2>

    <table class="info-table">
        <tr><td style="width: 50%;"><span class="field-label">Fecha de ingreso al taller:</span> {{ $fechaIngreso }}</td><td style="width: 50%;"><span class="field-label">Fecha de salida al taller:</span> {{ $fechaSalida }}</td></tr>
    </table>

    <div class="section-title">DATOS DEL DUE&Ntilde;O</div>
    <table class="info-table">
        <tr><td style="width: 48%;"><span class="field-label">Nombre:</span> {{ $nombreCliente }}</td><td style="width: 26%;"><span class="field-label">DNI:</span> {{ $dniCliente }}</td><td style="width: 26%;"><span class="field-label">Tel&eacute;fono:</span> {{ $telefonoCliente }}</td></tr>
    </table>

    <div class="section-title">DATOS Y CARACTER&Iacute;STICAS DEL VEH&Iacute;CULO</div>
    <table class="info-table">
        <tr><td style="width: 33.33%;"><span class="field-label">Placa Actual:</span> {{ $placaVehiculo }}</td><td style="width: 33.33%;"><span class="field-label">Placa Anterior:</span> {{ $placaAnteriorVehiculo }}</td><td style="width: 33.33%;"><span class="field-label">Marca:</span> {{ $marcaVehiculo }}</td></tr>
        <tr><td style="width: 33.33%;"><span class="field-label">Modelo:</span> {{ $modeloVehiculo }}</td><td style="width: 33.33%;"><span class="field-label">N&deg; Motor:</span> {{ $motorVehiculo }}</td><td style="width: 33.33%;"><span class="field-label">Color:</span> {{ $colorVehiculo }}</td></tr>
        <tr><td style="width: 33.33%;"><span class="field-label">A&ntilde;o:</span> {{ $anioVehiculo }}</td><td style="width: 33.33%;"><span class="field-label">Combustible:</span> {{ $combustibleVehiculo }}</td><td style="width: 33.33%;"><span class="field-label">Kilometraje:</span> {{ $kilometrajeVehiculo }}</td></tr>
    </table>

    <div class="section-title">RECEPCI&Oacute;N DEL VEH&Iacute;CULO</div>
    <table class="accessories-table">
        <thead><tr><th style="width: 49%;">Esquema de Da&ntilde;os</th><th style="width: 20%;">Accesorios</th><th class="center-text" style="width: 4%;">SI</th><th class="center-text" style="width: 4%;">NO</th><th style="width: 20%;">Accesorios</th><th class="center-text" style="width: 4%;">SI</th><th class="center-text" style="width: 4%;">NO</th></tr></thead>
        <tbody>
            @php
                $detalles = $detallesEvaluacion->getAttributes();
                unset($detalles['id'], $detalles['evaluacion_id'], $detalles['created_at'], $detalles['updated_at']);
                $column_count = 2;
                $items_per_column = ceil(count($detalles) / $column_count);
                $chunks = array_chunk($detalles, $items_per_column, true);
                $svg = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#333"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>');
            @endphp
            @for ($i = 0; $i < $items_per_column; $i++)
                <tr>
                    @if ($i === 0)
                        <td class="carro-esquema" rowspan="{{ $items_per_column }}" background="{{ str_replace('\\', '/', public_path('images/Diagrama-vechiculos.png')) }}"></td>
                    @endif
                    @foreach ($chunks as $column)
                        @php $key = array_keys($column)[$i] ?? null; $value = $key ? $column[$key] : null; $nombre_accesorio = ucwords(str_replace('_', ' ', $key)); @endphp
                        @if ($key)
                            <td>{{ $nombre_accesorio }}</td>
                            <td class="center-text">@if ($value === 1)<img src="{{ $svg }}" alt="S&iacute;" class="check-icon">@endif</td>
                            <td class="center-text">@if ($value === 0)<img src="{{ $svg }}" alt="No" class="check-icon">@endif</td>
                        @else
                            <td></td><td></td><td></td>
                        @endif
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="section-title">OBSERVACIONES</div>
    <table class="info-table"><tr><td style="width:100%;">{{ $observaciones }}</td></tr></table>

    <p style="color: #555;">Con la presente yo y/o en representaci&oacute;n autorizo el trabajo a realizarse en mi veh&iacute;culo.</p>

    <table class="info-table" style="margin-top:40px; text-align:center;">
        <tr><td style="width:50%; padding-top:40px;">___________________________<br>Firma del Cliente</td><td style="width:50%; padding-top:40px;">___________________________<br>Firma Representante del Taller</td></tr>
    </table>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n de Conversiones &ndash; ARTURO MOTORS</p>
</body>
</html>
