<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 50px 60px 60px 60px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #555; line-height: 1.6; margin: 0; padding: 0; }
    table.header { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .brand-title { font-size: 26px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
    .brand-subtitle { font-size: 10px; color: #999; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
    .ruc-cell { text-align: right; }
    .ruc-label { font-size: 10px; color: #999; }
    .ruc-value { font-size: 11px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 9px; color: #999; }
    h1.titulo { text-align: center; font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 20px 0 25px 0; text-decoration: underline; }
    table.datos { width: 100%; margin-bottom: 15px; border: 1px solid #eee; }
    table.datos td { padding: 4px 8px; border-bottom: 1px solid #f5f5f5; color: #555; }
    table.datos td strong { color: #333; }
    h3.subtitulo { color: #333; font-size: 12px; margin: 10px 0; text-decoration: underline; }
    table.equipos { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #eee; }
    table.equipos th { background: #e0e0e0; color: #333; text-align: left; padding: 6px 8px; font-size: 11px; }
    table.equipos td { padding: 6px 8px; border-bottom: 1px solid #f5f5f5; font-size: 11px; color: #555; }
    .terminos { margin-top: 15px; background: #f9f9f9; padding: 12px; border: 1px solid #eee; }
    .terminos h3 { margin: 0 0 8px 0; font-size: 12px; color: #333; text-decoration: underline; }
    .terminos ul { padding-left: 18px; margin: 0; }
    .terminos li { color: #555; }
    table.firmas { width: 100%; margin-top: 50px; }
    table.firmas td { width: 50%; text-align: center; color: #555; }
    .firma-linea { border-top: 1px solid #999; width: 80%; margin: 0 auto 5px auto; }
    .footer-note { margin-top: 40px; text-align: center; font-size: 8px; color: #aaa; }
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

    <h1 class="titulo">CARTA DE GARANT&Iacute;A</h1>
    <p style="text-align: right; font-size: 10px; color: #999;">Orden #{{ $orden->id }} &mdash; {{ now()->format('d/m/Y') }}</p>

    <table class="datos">
        <tr><td width="15%"><strong>Cliente:</strong></td><td>{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} &mdash; {{ $orden->cliente->documento }}</td></tr>
        <tr><td><strong>Veh&iacute;culo:</strong></td><td>{{ $orden->vehiculo->placa }} &mdash; {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</td></tr>
        <tr><td><strong>Servicio:</strong></td><td>{{ $orden->service->nombre }}</td></tr>
    </table>

    <h3 class="subtitulo">Equipos garantizados</h3>
    <table class="equipos">
        <thead><tr><th>Producto</th><th>Marca</th><th>N.° de serie</th></tr></thead>
        <tbody>
            @foreach ($orden->items as $item)
                <tr><td>{{ $item->producto->nombre }}</td><td>{{ $item->producto->marca }}</td><td><strong>{{ $item->serie }}</strong></td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="terminos">
        <h3>T&eacute;rminos de la garant&iacute;a</h3>
        <ul>
            <li>El taller garantiza el correcto funcionamiento de los equipos e instalaci&oacute;n detallados arriba por un periodo de [ __ ] meses o [ __ ] kil&oacute;metros, lo que ocurra primero, contados desde la fecha de entrega del veh&iacute;culo.</li>
            <li>La garant&iacute;a cubre defectos de fabricaci&oacute;n de los equipos y errores de instalaci&oacute;n atribuibles al taller.</li>
            <li>La garant&iacute;a no cubre da&ntilde;os derivados de mal uso, manipulaci&oacute;n por terceros no autorizados, accidentes, o falta de mantenimiento del sistema de conversi&oacute;n.</li>
            <li>Para hacer v&aacute;lida la garant&iacute;a, el cliente debe presentar este documento junto con el comprobante de pago.</li>
        </ul>
    </div>

    <table class="firmas">
        <tr><td><div class="firma-linea"></div>Firma del taller</td><td><div class="firma-linea"></div>Firma del cliente</td></tr>
    </table>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n Automotriz/Arturo Motors</p>
</body>
</html>
