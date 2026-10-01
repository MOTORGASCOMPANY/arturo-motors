<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte FISE</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; margin: 0; padding: 16px; }

        table.header { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.header td { vertical-align: middle; }
        .header img { height: 46px; }
        .header h1 { font-size: 16px; color: #1565c0; margin: 0; }
        .header p { font-size: 8px; color: #666; margin: 2px 0 0 0; }
        .rango { text-align: right; font-size: 9px; color: #46586b; }
        .rango strong { color: #1565c0; }

        h2 { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #2c3e50; border-left: 3px solid #1565c0; padding-left: 6px; margin: 14px 0 5px; }

        table.datos { width: 100%; border-collapse: collapse; }
        table.datos th { background: #BDD6EE; color: #1f2937; font-size: 8px; text-transform: uppercase; padding: 4px 6px; border: 1px solid #c7d7ee; text-align: left; }
        table.datos td { padding: 3px 6px; border: 1px solid #dfe6ee; font-size: 9px; }
        table.datos tr { page-break-inside: avoid; }
        table.datos td.num, table.datos th.num { text-align: right; }

        .vacio { color: #999; font-style: italic; padding: 6px; }
        .est-aprobado, .est-pagado { color: #059669; font-weight: bold; }
        .est-rechazado { color: #dc2626; font-weight: bold; }
        .est-pendiente, .est-parcial { color: #d97706; font-weight: bold; }

        .footer { margin-top: 16px; text-align: center; font-size: 7px; color: #b3b3b3; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 18%;"><img src="{{ public_path('images/LOGOFINAL.jpg') }}"></td>
            <td>
                <h1>Reporte FISE</h1>
                <p>Solicitudes, pagos y rendimiento del programa</p>
            </td>
            <td class="rango">
                <strong>Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</strong><br>
                Generado: {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <h2>Indicadores</h2>
    <table class="datos">
        <tr><th style="width: 65%;">Indicador</th><th class="num">Valor</th></tr>
        <tr><td>Solicitudes totales (aprobadas / rechazadas / pendientes)</td><td class="num">{{ $totalSolicitudes }} ({{ $solicitudesAprobadas }} / {{ $solicitudesRechazadas }} / {{ $solicitudesPendientes }})</td></tr>
        <tr><td>Tasa de aprobación</td><td class="num">{{ $tasaAprobacion }}%</td></tr>
        <tr><td>Pagos registrados (pagados / parciales / pendientes)</td><td class="num">{{ $totalPagos }} ({{ $pagosPagados }} / {{ $pagosParciales }} / {{ $pagosPendientes }})</td></tr>
        <tr><td>Monto total FISE</td><td class="num">S/ {{ number_format((float) $montoTotalFise, 2) }}</td></tr>
        <tr><td>Monto pagado FISE</td><td class="num">S/ {{ number_format((float) $montoPagadoFise, 2) }}</td></tr>
        <tr><td>Saldo pendiente FISE</td><td class="num">S/ {{ number_format((float) $saldoPendiente, 2) }}</td></tr>
        <tr><td>Ingresos en caja (FISE)</td><td class="num">S/ {{ number_format((float) $ingresosCajaFise, 2) }}</td></tr>
    </table>

    <h2>Solicitudes del período</h2>
    @if($solicitudes->isEmpty())
        <p class="vacio">Sin solicitudes en el rango seleccionado.</p>
    @else
        <table class="datos">
            <tr><th>Fecha</th><th>Cliente</th><th>Vehículo</th><th>Estado</th></tr>
            @foreach($solicitudes as $s)
                <tr>
                    <td>{{ $s->created_at->format('d/m/Y') }}</td>
                    <td>{{ $s->cliente ? ($s->cliente->nombre_completo ?? trim(($s->cliente->nombre ?? '') . ' ' . ($s->cliente->apellido ?? ''))) : '—' }}</td>
                    <td>{{ $s->vehiculo?->placa ?? '—' }}</td>
                    <td class="est-{{ $s->estado ?? '' }}">{{ strtoupper($s->estado ?? '—') }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <h2>Pagos del período</h2>
    @if($pagos->isEmpty())
        <p class="vacio">Sin pagos en el rango seleccionado.</p>
    @else
        <table class="datos">
            <tr><th>Fecha</th><th>Orden</th><th>Cliente</th><th>Técnico</th><th class="num">Total</th><th class="num">Pagado</th><th>Estado</th></tr>
            @foreach($pagos as $p)
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td>#{{ $p->service_order_id }}</td>
                    <td>{{ $p->serviceOrder?->cliente ? ($p->serviceOrder->cliente->nombre_completo ?? trim(($p->serviceOrder->cliente->nombre ?? '') . ' ' . ($p->serviceOrder->cliente->apellido ?? ''))) : '—' }}</td>
                    <td>{{ $p->pagadoPor?->name ?? '—' }}</td>
                    <td class="num">S/ {{ number_format((float) $p->monto_total, 2) }}</td>
                    <td class="num">S/ {{ number_format((float) $p->monto_pagado, 2) }}</td>
                    <td class="est-{{ $p->estado ?? '' }}">{{ strtoupper($p->estado ?? '—') }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <h2>Pagos por técnico</h2>
    @if($pagosPorTecnico->isEmpty())
        <p class="vacio">Sin pagos con técnico asignado en el rango.</p>
    @else
        <table class="datos">
            <tr><th>Técnico</th><th class="num">Operaciones</th><th class="num">Monto total</th><th class="num">Monto pagado</th></tr>
            @foreach($pagosPorTecnico as $tecnico => $row)
                <tr>
                    <td>{{ $tecnico }}</td>
                    <td class="num">{{ $row['cantidad'] }}</td>
                    <td class="num">S/ {{ number_format((float) $row['monto_total'], 2) }}</td>
                    <td class="num">S/ {{ number_format((float) $row['monto_pagado'], 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="font-weight: bold;">Total</td>
                <td class="num" style="font-weight: bold;">{{ $pagosPorTecnico->sum('cantidad') }}</td>
                <td class="num" style="font-weight: bold;">S/ {{ number_format((float) $pagosPorTecnico->sum('monto_total'), 2) }}</td>
                <td class="num" style="font-weight: bold;">S/ {{ number_format((float) $pagosPorTecnico->sum('monto_pagado'), 2) }}</td>
            </tr>
        </table>
    @endif

    <div class="footer">Arturo Motors — Sistema de Control Interno · Reporte FISE · Generado {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
