<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Servicios — Arturo Motors</title>
    @include('pdfs.partials.base-styles')
    <style>
        .badge-simple { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .badge-conversion { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
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
                    <img src="{{ public_path('images/LOGOFINAL.jpg') }}" class="logo">
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
