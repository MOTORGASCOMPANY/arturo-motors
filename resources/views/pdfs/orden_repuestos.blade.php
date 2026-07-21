<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Repuestos - Conversión {{ $conversion->id }}</title>
    <style>
        /* Estilos básicos para el PDF */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .table-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-container th, .table-container td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table-container th {
            background-color: #f2f2f2;
            color: #555;
            font-size: 11px;
        }
        .table-container td {
            font-size: 10px;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Orden de Repuestos y Accesorios</h1>
        <p>Conversión #{{ $conversion->id }}</p>
    </div>
    
    <div class="info-box">
        <p><strong>Fecha:</strong> {{ $fechaActual }}</p>
        <p><strong>Cliente:</strong> {{ $conversion->expediente->cliente->nombre ?? 'N/A' }} {{ $conversion->expediente->cliente->apellido ?? '' }}</p>
        <p><strong>Vehículo:</strong> {{ $conversion->expediente->vehiculo->placa ?? '' }} - {{ $conversion->expediente->vehiculo->marca ?? '' }} {{ $conversion->expediente->vehiculo->modelo ?? '' }}</p>
        <p><strong>Tecnico:</strong> {{ $conversion->tecnico->name ?? 'N/A' }}</p>
        <p><strong>Expediente:</strong> # {{ $conversion->expediente->id ?? 'N/A' }}</p>
    </div>

    <h2 style="font-size: 14px; color: #333;">Detalle de Repuestos</h2>

    <table class="table-container">
        <thead>
            <tr>
                <th>#</th>
                <th>Repuesto / Accesorio</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $i = 1;
                $granTotal = 0;
            @endphp
            @foreach ($conversion->conversionDetalles as $detalle)
            @php
                // Calcular el total por fila
                $subtotal = ($detalle->cantidad_utilizada ?? 0) * ($detalle->repuesto->precio ?? 0);
                // Sumar al gran total
                $granTotal += $subtotal;
            @endphp
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $detalle->repuesto->nombre ?? 'Repuesto Eliminado' }}</td>
                <td>{{ $detalle->cantidad_utilizada }}</td>
                <td>S/ {{ number_format($detalle->repuesto->precio ?? 0, 2) }}</td>
                <td>S/ {{ number_format($subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align:right; font-size:12px;"><strong>TOTAL:</strong></td>
                <td style="font-size:12px;"><strong>S/ {{ number_format($granTotal, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Generado por Sistema de Gestión de Conversiones</p>
    </div>

</body>
</html>
