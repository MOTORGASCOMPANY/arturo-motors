<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Repuestos</title>
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
        h1.titulo { text-align: center; font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 20px 0 25px 0; text-decoration: underline; }
        .info-box { border: 1px solid #eee; padding: 10px; margin-bottom: 20px; }
        .info-box p { margin: 5px 0; color: #555; }
        .info-box strong { color: #333; }
        h3.subtitulo { color: #333; font-size: 12px; margin: 10px 0; text-decoration: underline; }
        table.tabla { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #eee; }
        table.tabla th { background: #e0e0e0; color: #333; font-size: 11px; padding: 8px; text-align: left; }
        table.tabla td { padding: 8px; font-size: 10px; border-bottom: 1px solid #f5f5f5; color: #555; }
        .total-row { font-weight: bold; }
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

    <h1 class="titulo">ORDEN DE REPUESTOS Y ACCESORIOS</h1>

    <div class="info-box">
        <p><strong>Fecha:</strong> {{ $fechaActual }}</p>
        <p><strong>Cliente:</strong> {{ $conversion->expediente->cliente->nombre ?? 'N/A' }} {{ $conversion->expediente->cliente->apellido ?? '' }}</p>
        <p><strong>Veh&iacute;culo:</strong> {{ $conversion->expediente->vehiculo->placa ?? '' }} - {{ $conversion->expediente->vehiculo->marca ?? '' }} {{ $conversion->expediente->vehiculo->modelo ?? '' }}</p>
        <p><strong>T&eacute;cnico:</strong> {{ $conversion->tecnico->name ?? 'N/A' }}</p>
        <p><strong>Expediente:</strong> # {{ $conversion->expediente->id ?? 'N/A' }}</p>
    </div>

    <h3 class="subtitulo">Detalle de Repuestos</h3>
    <table class="tabla">
        <thead><tr><th>#</th><th>Repuesto / Accesorio</th><th>Cantidad</th><th>Precio Unitario</th><th>Total</th></tr></thead>
        <tbody>
            @php $i = 1; $granTotal = 0; @endphp
            @foreach ($conversion->conversionDetalles as $detalle)
            @php $subtotal = ($detalle->cantidad_utilizada ?? 0) * ($detalle->repuesto->precio ?? 0); $granTotal += $subtotal; @endphp
            <tr><td>{{ $i++ }}</td><td>{{ $detalle->repuesto->nombre ?? 'Repuesto Eliminado' }}</td><td>{{ $detalle->cantidad_utilizada }}</td><td>S/ {{ number_format($detalle->repuesto->precio ?? 0, 2) }}</td><td>S/ {{ number_format($subtotal, 2) }}</td></tr>
            @endforeach
            <tr class="total-row"><td colspan="4" style="text-align:right; font-size:12px;"><strong>TOTAL:</strong></td><td style="font-size:12px;"><strong>S/ {{ number_format($granTotal, 2) }}</strong></td></tr>
        </tbody>
    </table>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n de Conversiones &ndash; ARTURO MOTORS</p>
</body>
</html>
