<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>
    body { font-family: sans-serif; font-size: 13px; }
    .header { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 6px 0; }
</style></head>
<body>
    <div class="header">
        <h2>Comprobante {{ $orden->comprobante->folio }}</h2>
    </div>
    <table>
        <tr><td><strong>Cliente:</strong></td><td>{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td></tr>
        <tr><td><strong>Vehículo:</strong></td><td>{{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</td></tr>
        <tr><td><strong>Servicio:</strong></td><td>{{ $orden->service->nombre }}</td></tr>
        <tr><td><strong>Monto:</strong></td><td>S/ {{ number_format($orden->comprobante->monto, 2) }}</td></tr>
        <tr><td><strong>Método de pago:</strong></td><td>{{ ucfirst($orden->comprobante->metodo_pago) }}</td></tr>
        <tr><td><strong>Fecha:</strong></td><td>{{ $orden->comprobante->created_at->format('d/m/Y H:i') }}</td></tr>
    </table>
</body>
</html>