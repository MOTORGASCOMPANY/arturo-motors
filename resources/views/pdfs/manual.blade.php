<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual y Mantenimiento</title>
    <style>
        @page { margin: 50px 60px 60px 60px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #555; line-height: 1.5; margin: 0; padding: 0; }
        table.header { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .brand-title { font-size: 26px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
        .brand-subtitle { font-size: 10px; color: #999; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
        .ruc-cell { text-align: right; }
        .ruc-label { font-size: 10px; color: #999; }
        .ruc-value { font-size: 11px; font-weight: bold; color: #1565c0; }
        .rd-value { font-size: 9px; color: #999; }
        .section-title { background: #e0e0e0; color: #333; padding: 5px 8px; font-size: 12px; font-weight: bold; margin: 15px 0 8px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border: 1.5px solid #999; }
        .info-table th { text-align: left; width: 40%; padding: 5px 8px; font-weight: bold; background: #f5f5f5; border-bottom: 1px solid #ccc; font-size: 10px; color: #333; border-right: 1px solid #ccc; }
        .info-table td { padding: 5px 8px; border-bottom: 1px solid #ccc; font-size: 10px; color: #555; }
        .firma { margin-top: 80px; text-align: center; }
        .firma .linea { border-top: 1px solid #999; width: 40%; margin: 0 auto 5px auto; }
        .firma p { margin-top: 6px; font-weight: bold; font-size: 10px; color: #555; }
        .footer-note { margin-top: 30px; text-align: center; font-size: 8px; color: #aaa; }
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
            <td class="ruc-cell" style="width: 30%;"><div class="ruc-label">R.U.C.</div><div class="ruc-value">{{ $ruc ?? '20610295321' }}</div><div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0413-2023-MTC/17.03' }}</div></td>
        </tr>
    </table>

    <div class="section-title">DATOS DEL CLIENTE</div>
    <table class="info-table">
        <tr><th>Apellidos / Nombres:</th><td>{{ $vehiculo->clientes->first()?->nombre ?? '---' }} {{ $vehiculo->clientes->first()?->apellido ?? '' }}</td></tr>
        <tr><th>DNI / CE / RUC:</th><td>{{ $vehiculo->clientes->first()?->documento ?? '---' }}</td></tr>
        <tr><th>Direcci&oacute;n:</th><td>{{ $vehiculo->clientes->first()?->direccion ?? '---' }}</td></tr>
        <tr><th>Tel&eacute;fono:</th><td>{{ $vehiculo->clientes->first()?->telefono ?? '---' }}</td></tr>
    </table>

    <div class="section-title">DATOS DEL VEH&Iacute;CULO</div>
    <table class="info-table">
        <tr><th>Marca:</th><td>{{ $vehiculo->marca }}</td></tr>
        <tr><th>Modelo:</th><td>{{ $vehiculo->modelo }}</td></tr>
        <tr><th>A&ntilde;o:</th><td>{{ $vehiculo->anio ?? '---' }}</td></tr>
        <tr><th>Placa:</th><td>{{ $vehiculo->placa }}</td></tr>
        <tr><th>Color:</th><td>{{ $vehiculo->color ?? '---' }}</td></tr>
        <tr><th>Combustible:</th><td>{{ $vehiculo->combustible ?? '---' }}</td></tr>
        <tr><th>N&deg; Serie (Motor):</th><td>{{ $vehiculo->serie ?? '---' }}</td></tr>
    </table>

    <div class="section-title">INFORMACI&Oacute;N DE INSTALACI&Oacute;N</div>
    <table class="info-table">
        <tr><th>Fecha de Instalaci&oacute;n:</th><td>{{ $vehiculo->cita->last()?->fecha_cita?->format('d/m/Y') ?? '---' }}</td></tr>
        <tr><th>Kilometraje Actual:</th><td>{{ $vehiculo->kilometraje ?? '---' }}</td></tr>
        <tr><th>Tipo de Equipo:</th><td>{{ $vehiculo->equipo ?? '---' }}</td></tr>
        <tr><th>Marca / Modelo Equipo:</th><td>{{ $vehiculo->Equipomarca ?? '---' }} / {{ $vehiculo->Equipomodelo ?? '---' }}</td></tr>
        <tr><th>N&deg; Serie Equipo:</th><td>{{ $vehiculo->Equiposerie ?? '---' }}</td></tr>
        <tr><th>Central de Control:</th><td>{{ $vehiculo->central ?? '---' }}</td></tr>
        <tr><th>Electr&oacute;nica Adicional:</th><td>{{ $vehiculo->electronica ?? '---' }}</td></tr>
        <tr><th>Otros Dispositivos:</th><td>{{ $vehiculo->otros ?? '---' }}</td></tr>
    </table>

    <div class="section-title">DATOS DEL TANQUE</div>
    <table class="info-table">
        <tr><th>Tipo de Tanque:</th><td>{{ $vehiculo->tanque ?? '---' }}</td></tr>
        <tr><th>Marca / Modelo Tanque:</th><td>{{ $vehiculo->Tanquemarca ?? '---' }} / {{ $vehiculo->Tanquemodelo ?? '---' }}</td></tr>
        <tr><th>N&deg; Serie Tanque:</th><td>{{ $vehiculo->Tanqueserie ?? '---' }}</td></tr>
        <tr><th>Multiv&aacute;lvulas:</th><td>{{ $vehiculo->multivalvulas ?? '---' }}</td></tr>
        <tr><th>Toma de Carga:</th><td>{{ $vehiculo->toma ?? '---' }}</td></tr>
        <tr><th>N&deg; Constancia Aprobaci&oacute;n:</th><td>{{ $vehiculo->Tanqueaprobacion ?? '---' }}</td></tr>
        <tr><th>N&deg; Expediente:</th><td>{{ $vehiculo->Tanqueexpediente ?? '---' }}</td></tr>
    </table>

    <div class="firma">
        <div class="linea"></div>
        <p>Sello y firma del taller</p>
    </div>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n de Conversiones &ndash; ARTURO MOTORS</p>
</body>
</html>
