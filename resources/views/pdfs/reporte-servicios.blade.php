<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Servicios — Arturo Motors</title>
    <style>
        @page { margin: 10px; }
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-font-smoothing: antialiased; }
        html, body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px; color: #1c2d42; line-height: 1.35;
        }
        .page-wrap { padding: 26px 34px 40px 34px; }
        table { width: 100%; border-collapse: collapse; }
        .w-100 { width: 100%; }
        .spacer-sm { height: 6px; line-height: 6px; font-size: 1px; }
        .spacer-md { height: 10px; line-height: 10px; font-size: 1px; }
        .spacer-lg { height: 16px; line-height: 16px; font-size: 1px; }

        .header-table td { vertical-align: middle; }
        .logo-cell { width: 46%; padding-right: 16px; }
        .title-cell { width: 54%; }
        .logo { max-height: 36px; width: auto; display: block; }
        .company-title { font-family: 'Georgia', serif; font-size: 13px; font-weight: 700; color: #0d1b30; letter-spacing: 0.4px; margin-top: 5px; }
        .company-subtitle { font-size: 7.3px; color: #2e5286; font-weight: 700; letter-spacing: 0.6px; margin-top: 2px; }
        .company-info { font-size: 7px; color: #64748b; line-height: 1.5; margin-top: 4px; }
        .report-title-box { background-color: #14233f; color: #ffffff; padding: 10px 16px; text-align: center; }
        .report-title { font-family: 'Georgia', serif; font-size: 11.5px; font-weight: 700; letter-spacing: 1.2px; }
        .report-subtitle { font-size: 7px; color: #c7d2e0; margin-top: 3px; }
        .header-rule { border-top: 2px solid #14233f; font-size: 1px; line-height: 1px; }

        .meta-table td { font-size: 7.3px; padding: 7px 11px; background-color: #f7f9fc; border: 1px solid #e2e8f0; }
        .meta-label { display: block; font-size: 6.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px; }
        .meta-value { font-size: 8px; color: #0d1b30; font-weight: 700; }

        .kpi-box { text-align: center; padding: 10px; background-color: #f7f9fc; border: 1px solid #e2e8f0; border-radius: 4px; }
        .kpi-box .value { font-size: 16px; font-weight: 700; }
        .kpi-box .label { font-size: 6.5px; color: #64748b; text-transform: uppercase; margin-top: 3px; letter-spacing: 0.3px; }
        .kpi-green .value { color: #059669; }
        .kpi-blue .value { color: #2563eb; }
        .kpi-amber .value { color: #d97706; }

        .data-table { border: 1px solid #94a3b8; }
        .data-table thead th {
            background-color: #14233f; color: #ffffff; font-size: 7px; font-weight: 700;
            padding: 7px 8px; text-transform: uppercase; letter-spacing: 0.3px;
            border-right: 1px solid #2c3e5c; text-align: left;
        }
        .data-table thead th:last-child { border-right: none; }
        .data-table tbody td {
            padding: 6px 8px; font-size: 7.5px; border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0; vertical-align: middle; color: #1c2d42;
        }
        .data-table tbody td:last-child { border-right: none; }
        .data-table tbody tr:nth-child(even) { background-color: #f7f9fc; }
        .cell-strong { font-weight: 700; color: #0d1b30; }
        .cell-muted { color: #64748b; }
        .cell-right { text-align: right; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 8px; font-size: 6.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
        .badge-simple { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .badge-conversion { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

        .empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; border: 1px dashed #cbd5e1; background-color: #f7f9fc; }
        .empty-state p { font-size: 10px; font-weight: 600; }

        .closing-rule { border-top: 1px solid #cbd5e1; font-size: 1px; line-height: 1px; }
        .closing-text { text-align: center; font-size: 6.5px; color: #94a3b8; letter-spacing: 0.4px; text-transform: uppercase; }

        .page-footer { position: fixed; bottom: -32px; left: 34px; right: 34px; }
        .page-footer table td { border-top: 1px solid #cbd5e1; padding-top: 5px; font-size: 6.5px; color: #94a3b8; }
        .fright { text-align: right; }
    </style>
</head>
<body>

    <div class="page-footer">
        <table class="w-100">
            <tr>
                <td>Arturo Motors &mdash; Documento generado automáticamente por el sistema</td>
                <td class="fright">Página <script type="text/php">
                    if (isset($pdf)) {
                        $text = "{PAGE_NUM} de {PAGE_COUNT}";
                        $font = $fontMetrics->get_font("Helvetica", "normal");
                        $pdf->page_text(520, 800, $text, $font, 6.5, array(0.58, 0.64, 0.72));
                    }
                </script></td>
            </tr>
        </table>
    </div>

    <div class="page-wrap">

        <table class="header-table w-100">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/icon.png') }}" class="logo">
                    <div class="company-title">ARTURO MOTORS</div>
                    <div class="company-subtitle">ASESOR AUTOMOTRIZ — CENTRO DE INSPECCIÓN TÉCNICA VEHICULAR</div>
                    <div class="company-info">
                        Av. Perú N.° 5176, Callao, Perú<br>
                        Tel: 987 288 504 / 943 694 464&nbsp;&nbsp;·&nbsp;&nbsp;contacto@empresa.com.pe
                    </div>
                </td>
                <td class="title-cell">
                    <div class="report-title-box">
                        <div class="report-title">REPORTE DE SERVICIOS</div>
                        <div class="report-subtitle">Documento generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} hrs.{{ $tipoServicio !== 'todos' ? '  ·  Tipo: ' . ucfirst($tipoServicio) : '' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>
        <div class="header-rule">&nbsp;</div>
        <div class="spacer-md">&nbsp;</div>

        <table class="meta-table w-100">
            <tr>
                <td style="width: 25%;">
                    <span class="meta-label">Período</span>
                    <span class="meta-value">{{ $desde }} al {{ $hasta }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="meta-label">Fecha de emisión</span>
                    <span class="meta-value">{{ now()->format('d/m/Y') }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="meta-label">Hora de emisión</span>
                    <span class="meta-value">{{ now()->format('H:i') }} hrs.</span>
                </td>
                <td style="width: 25%;">
                    <span class="meta-label">Emitido por</span>
                    <span class="meta-value">Sistema Arturo Motors</span>
                </td>
            </tr>
        </table>

        <div class="spacer-lg">&nbsp;</div>

        <table class="w-100" style="margin-bottom: 8px;">
            <tr>
                <td style="width: 33%; padding: 0 4px;">
                    <div class="kpi-box kpi-green">
                        <div class="value">S/ {{ number_format($totalVentas, 2) }}</div>
                        <div class="label">Total vendido</div>
                    </div>
                </td>
                <td style="width: 33%; padding: 0 4px;">
                    <div class="kpi-box kpi-blue">
                        <div class="value">{{ number_format($totalOrdenes) }}</div>
                        <div class="label">Órdenes cobradas</div>
                    </div>
                </td>
                <td style="width: 33%; padding: 0 4px;">
                    <div class="kpi-box kpi-amber">
                        <div class="value">S/ {{ number_format($ticketPromedio, 2) }}</div>
                        <div class="label">Ticket promedio</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>

        @if($ventasPorServicio->count())
        <div class="cell-strong" style="font-size: 8px; margin-bottom: 6px;">VENTAS POR SERVICIO</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 50%;">Servicio</th>
                    <th style="width: 25%; text-align: right;">Cantidad</th>
                    <th style="width: 25%; text-align: right;">Total (S/)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventasPorServicio as $nombre => $info)
                <tr>
                    <td class="cell-strong">{{ $nombre }}</td>
                    <td class="cell-right">{{ $info['cantidad'] }}</td>
                    <td class="cell-right">S/ {{ number_format($info['total'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #eef2f8; font-weight: 700;">
                    <td class="cell-strong">TOTAL</td>
                    <td class="cell-right cell-strong">{{ $totalOrdenes }}</td>
                    <td class="cell-right cell-strong">S/ {{ number_format($totalVentas, 2) }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <div class="empty-state" style="margin-bottom: 14px;">
            <p>No hay ventas por servicio en este período.</p>
        </div>
        @endif

        @if($ventasPorTecnico->count())
        <div class="cell-strong" style="font-size: 8px; margin-bottom: 6px;">CONVERSIONES POR TÉCNICO</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 50%;">Técnico</th>
                    <th style="width: 25%; text-align: right;">Cantidad</th>
                    <th style="width: 25%; text-align: right;">Total (S/)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventasPorTecnico as $nombre => $info)
                <tr>
                    <td class="cell-strong">{{ $nombre }}</td>
                    <td class="cell-right">{{ $info['cantidad'] }}</td>
                    <td class="cell-right">S/ {{ number_format($info['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="spacer-lg">&nbsp;</div>
        <div class="closing-rule">&nbsp;</div>
        <div class="spacer-sm">&nbsp;</div>
        <div class="closing-text">Fin del reporte &mdash; {{ $totalOrdenes }} orden(es) cobrada(s)</div>

    </div>

</body>
</html>
