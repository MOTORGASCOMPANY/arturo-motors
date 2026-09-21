<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Conversiones GNV - Arturo Motors</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 30px; color: #1e293b; font-size: 12px; }
        .header { text-align: center; margin-bottom: 25px; padding: 20px; background: #1e40af; color: #ffffff; border-radius: 8px; }
        .header h1 { font-size: 22px; margin-bottom: 5px; color: #ffffff; }
        .header p { font-size: 12px; color: #ffffff; }
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .kpi-table td { width: 25%; text-align: center; padding: 12px; background: #ffffff; border: 1px solid #e2e8f0; }
        .kpi-value { font-size: 24px; font-weight: bold; color: #1e293b; }
        .kpi-value-blue { color: #2563eb; }
        .kpi-value-green { color: #16a34a; }
        .kpi-value-amber { color: #d97706; }
        .kpi-label { font-size: 10px; color: #64748b; text-transform: uppercase; margin-top: 5px; }
        .section-title { font-size: 14px; font-weight: bold; color: #1e293b; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f1f5f9; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: bold; color: #475569; text-transform: uppercase; border-bottom: 2px solid #cbd5e1; }
        td { padding: 8px 10px; font-size: 11px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-green { color: #16a34a; }
        .text-amber { color: #d97706; }
        .text-gray { color: #94a3b8; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-amber { background: #fef3c7; color: #d97706; }
        .footer { text-align: center; margin-top: 30px; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte de Conversiones GNV</h1>
        <p>Arturo Motors — Kits instalados, componentes y balance de almacén</p>
    </div>

    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-value kpi-value-blue">{{ number_format($totalConversiones) }}</div>
                <div class="kpi-label">Total Conversiones</div>
            </td>
            <td>
                <div class="kpi-value kpi-value-green">{{ number_format($completadas) }}</div>
                <div class="kpi-label">Completadas</div>
            </td>
            <td>
                <div class="kpi-value kpi-value-amber">{{ number_format($enProceso) }}</div>
                <div class="kpi-label">En Proceso</div>
            </td>
            <td>
                <div class="kpi-value">{{ number_format($itemsInstalados) }}</div>
                <div class="kpi-label">Piezas Instaladas</div>
            </td>
        </tr>
    </table>

    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-value">{{ number_format($kitsEnStock) }}</div>
                <div class="kpi-label">Kits en Almacén</div>
            </td>
            <td>
                <div class="kpi-value">{{ number_format($stockPiezasSueltas) }}</div>
                <div class="kpi-label">Piezas Sueltas</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Detalle de Conversiones</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Placa</th>
                <th>Vehículo</th>
                <th>Técnico</th>
                <th>Kit</th>
                <th>Items Serializados</th>
                <th style="text-align: center;">Gen.</th>
                <th style="text-align: center;">Comp.</th>
                <th style="text-align: center;">Inst.</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th style="text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($detalleOrdenes as $d)
            <tr>
                <td class="font-bold">{{ $d['orden']->id }}</td>
                <td>{{ $d['cliente'] }}</td>
                <td class="font-bold">{{ $d['placa'] }}</td>
                <td>{{ $d['vehiculo'] }}</td>
                <td>{{ $d['tecnico'] }}</td>
                <td>{{ $d['kit'] }}</td>
                <td>
                    @forelse ($d['items_serializados'] ?? [] as $item)
                        {{ $item['nombre'] }}: {{ $item['serie'] }}@if(!$loop->last)<br>@endif
                    @empty
                        —
                    @endforelse
                </td>
                <td class="text-center">{{ $d['generacion'] }}</td>
                <td class="text-center">{{ $d['total_componentes'] }}</td>
                <td class="text-center">{{ $d['instalados'] }}</td>
                <td>{{ $d['fecha_inicio'] ?? '—' }}</td>
                <td>{{ $d['fecha_fin'] ?? '—' }}</td>
                <td class="text-center">
                    @if($d['orden']->estado === 'conversion_completada')
                        <span class="badge badge-green">Completada</span>
                    @else
                        <span class="badge badge-amber">En proceso</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="12" class="text-center text-gray">No hay conversiones registradas para los filtros seleccionados.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }} — Arturo Motors
    </div>

</body>
</html>
