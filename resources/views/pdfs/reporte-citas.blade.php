<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Citas — Arturo Motors</title>
    <style>
        @page {
            margin: 25px 30px;
            orientation: landscape;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.3;
        }
        table { width: 100%; border-collapse: collapse; }
        .w-100 { width: 100%; }

        /* HEADER */
        .header-table td { vertical-align: middle; }
        .logo { max-height: 50px; width: auto; }
        .company-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 9px;
            color: #1e3a8a;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .company-info {
            font-size: 8px;
            color: #64748b;
            line-height: 1.4;
            margin-top: 4px;
        }
        .report-title-box {
            background: linear-gradient(135deg, #1e293b, #312e81, #1e3a8a);
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-align: center;
        }
        .report-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .report-subtitle {
            font-size: 9px;
            opacity: 0.85;
            margin-top: 3px;
        }

        /* FILTROS */
        .filters-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 14px;
            margin: 12px 0;
        }
        .filters-label {
            font-size: 8px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .filters-value {
            font-size: 9px;
            color: #0f172a;
            font-weight: 600;
        }

        /* TABLA */
        .data-table {
            border: 1px solid #cbd5e1;
            margin-top: 10px;
        }
        .data-table thead th {
            background: #1e293b;
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            padding: 7px 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid #334155;
            text-align: left;
        }
        .data-table thead th:last-child { border-right: none; }
        .data-table tbody td {
            padding: 6px 10px;
            font-size: 9px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tbody td:last-child { border-right: none; }
        .data-table tbody tr:nth-child(even) { background: #f8fafc; }

        /* ESTADOS */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-pendiente { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .badge-aceptada { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-rechazada { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-cancelada { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* FOOTER */
        .footer {
            margin-top: 15px;
            font-size: 8px;
            color: #94a3b8;
        }
        .footer-line {
            border-top: 2px solid #1e293b;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- CABECERA -->
    <table class="header-table w-100">
        <tr>
            <td style="width: 45%; padding-right: 15px;">
                <img src="{{ public_path('images/icon.png') }}" class="logo">
                <div class="company-title" style="margin-top: 5px;">ARTURO MOTORS</div>
                <div class="company-subtitle">ASESOR AUTOMOTRIZ — CENTRO DE INSPECCION TECNICA VEHICULAR</div>
                <div class="company-info">
                    Av. Peru N° 5176 - Callao, Peru<br>
                    TEL: 987 288 504 / 943 694 464 · contacto@empresa.com.pe
                </div>
            </td>
            <td style="width: 55%; text-align: right;">
                <div class="report-title-box">
                    <div class="report-title">REPORTE DE CITAS</div>
                    <div class="report-subtitle">Generado: {{ now()->format('d/m/Y H:i') }} · Total: {{ $citas->count() }} cita(s)</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- FILTROS APLICADOS -->
    @if($search || $estado !== 'todos' || $fechaInicio || $fechaFin)
    <div class="filters-box">
        <span class="filters-label">Filtros aplicados:</span>
        @if($search)
            <span class="filters-value">Busqueda: "{{ $search }}"</span> ·
        @endif
        @if($estado !== 'todos')
            <span class="filters-value">Estado: {{ ucfirst($estado) }}</span> ·
        @endif
        @if($fechaInicio)
            <span class="filters-value">Desde: {{ $fechaInicio }}</span> ·
        @endif
        @if($fechaFin)
            <span class="filters-value">Hasta: {{ $fechaFin }}</span>
        @endif
    </div>
    @endif

    <!-- TABLA DE CITAS -->
    @if($citas->count())
    <table class="data-table w-100">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 18%;">Cliente</th>
                <th style="width: 9%;">Documento</th>
                <th style="width: 9%;">Placa</th>
                <th style="width: 14%;">Asesor</th>
                <th style="width: 16%;">Servicio</th>
                <th style="width: 17%;">Motivo</th>
                <th style="width: 9%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($citas as $cita)
            <tr>
                <td>{{ $cita->id }}</td>
                <td>{{ $cita->fecha_cita->format('d/m/Y H:i') }}</td>
                <td><strong>{{ $cita->cliente->nombre ?? 'N/A' }} {{ $cita->cliente->apellido ?? '' }}</strong></td>
                <td>{{ $cita->cliente->documento ?? '—' }}</td>
                <td>{{ $cita->vehiculo->placa ?? 'N/A' }}</td>
                <td>{{ $cita->asesor->name ?? 'N/A' }}</td>
                <td>{{ $cita->serviceOrder->service->nombre ?? '—' }}</td>
                <td>{{ $cita->motivo ?? '—' }}</td>
                <td>
                    @php
                        $badgeClass = match($cita->estado) {
                            'pendiente' => 'badge-pendiente',
                            'aceptada' => 'badge-aceptada',
                            'rechazada' => 'badge-rechazada',
                            'cancelada' => 'badge-cancelada',
                            default => 'badge-cancelada',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($cita->estado) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; color: #94a3b8;">
        <p style="font-size: 12px;">No se encontraron citas con los filtros seleccionados.</p>
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-line" style="display: flex; justify-content: space-between;">
            <span>Arturo Motors — Reporte generado automaticamente</span>
            <span>Pagina 1 de 1</span>
        </div>
    </div>

</body>
</html>
