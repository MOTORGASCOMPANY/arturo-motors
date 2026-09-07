<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Caja — Arturo Motors</title>
    <style>
        @page { margin: 10px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #1c2d42;
            line-height: 1.3;
        }
        .page-wrap { padding: 22px 30px 36px 30px; }
        table { width: 100%; border-collapse: collapse; }
        .w-100 { width: 100%; }
        .spacer-sm { height: 5px; font-size: 1px; }
        .spacer-md { height: 8px; font-size: 1px; }
        .spacer-lg { height: 12px; font-size: 1px; }

        .header-table td { vertical-align: middle; }
        .logo { max-height: 32px; width: auto; display: block; }
        .company-title { font-family: 'Georgia', serif; font-size: 12px; font-weight: 700; color: #0d1b30; letter-spacing: 0.4px; margin-top: 4px; }
        .company-subtitle { font-size: 6.5px; color: #2e5286; font-weight: 700; letter-spacing: 0.5px; margin-top: 1px; }
        .report-title-box { background-color: #14233f; color: #fff; padding: 8px 14px; text-align: center; }
        .report-title { font-family: 'Georgia', serif; font-size: 11px; font-weight: 700; letter-spacing: 1px; }
        .report-subtitle { font-size: 6.5px; color: #c7d2e0; margin-top: 2px; }
        .header-rule { border-top: 2px solid #14233f; font-size: 1px; line-height: 1px; }

        .kpi-box { text-align: center; padding: 8px 4px; background: #f7f9fc; border: 1px solid #e2e8f0; border-radius: 3px; }
        .kpi-box .value { font-size: 13px; font-weight: 700; }
        .kpi-box .label { font-size: 6px; color: #64748b; text-transform: uppercase; margin-top: 2px; letter-spacing: 0.3px; }
        .kpi-green .value { color: #059669; }
        .kpi-red .value { color: #dc2626; }
        .kpi-blue .value { color: #2563eb; }
        .kpi-navy .value { color: #14233f; }

        .data-table { border: 1px solid #94a3b8; }
        .data-table thead th {
            background: #14233f; color: #fff; font-size: 6.5px; font-weight: 700;
            padding: 5px 6px; text-transform: uppercase; letter-spacing: 0.3px;
            border-right: 1px solid #2c3e5c; text-align: left;
        }
        .data-table thead th:last-child { border-right: none; }
        .data-table tbody td {
            padding: 4px 6px; font-size: 7px; border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0; vertical-align: middle;
        }
        .data-table tbody td:last-child { border-right: none; }
        .data-table tbody tr:nth-child(even) { background: #f7f9fc; }
        .cell-strong { font-weight: 700; color: #0d1b30; }
        .cell-right { text-align: right; }
        .cell-center { text-align: center; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 6px; font-size: 6px; font-weight: 700; text-transform: uppercase; }
        .badge-cerrada { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-dif-ok { color: #15803d; }
        .badge-dif-err { color: #dc2626; }

        .closing-text { text-align: center; font-size: 6px; color: #94a3b8; letter-spacing: 0.4px; text-transform: uppercase; margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 6px; }

        .page-footer { position: fixed; bottom: -28px; left: 30px; right: 30px; }
        .page-footer table td { border-top: 1px solid #cbd5e1; padding-top: 4px; font-size: 6px; color: #94a3b8; }
    </style>
</head>
<body>

    <div class="page-footer">
        <table class="w-100">
            <tr>
                <td>Arturo Motors — Documento generado automáticamente</td>
                <td style="text-align: right;">Página <script type="text/php">
                    if (isset($pdf)) {
                        $text = "{PAGE_NUM} de {PAGE_COUNT}";
                        $font = $fontMetrics->get_font("Helvetica", "normal");
                        $pdf->page_text(520, 790, $text, $font, 6, array(0.58, 0.64, 0.72));
                    }
                </script></td>
            </tr>
        </table>
    </div>

    <div class="page-wrap">

        <table class="header-table w-100">
            <tr>
                <td style="width: 46%; padding-right: 14px;">
                    <img src="{{ public_path('images/icon.png') }}" class="logo">
                    <div class="company-title">ARTURO MOTORS</div>
                    <div class="company-subtitle">CENTRO DE INSPECCIÓN TÉCNICA VEHICULAR</div>
                </td>
                <td style="width: 54%;">
                    <div class="report-title-box">
                        <div class="report-title">REPORTE DE CAJA</div>
                        <div class="report-subtitle">Período: {{ $desde }} al {{ $hasta }} · Generado {{ now()->format('d/m/Y H:i') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>
        <div class="header-rule">&nbsp;</div>
        <div class="spacer-md">&nbsp;</div>

        <table class="w-100" style="margin-bottom: 6px;">
            <tr>
                <td style="width: 25%; padding: 0 3px;">
                    <div class="kpi-box kpi-navy">
                        <div class="value">S/ {{ number_format($efectivoAnterior, 2) }}</div>
                        <div class="label">Efectivo anterior</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 3px;">
                    <div class="kpi-box kpi-green">
                        <div class="value">S/ {{ number_format($totalIngresos, 2) }}</div>
                        <div class="label">Ingresos</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 3px;">
                    <div class="kpi-box kpi-red">
                        <div class="value">S/ {{ number_format($totalEgresos, 2) }}</div>
                        <div class="label">Egresos</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 3px;">
                    <div class="kpi-box kpi-blue">
                        <div class="value">S/ {{ number_format($neto, 2) }}</div>
                        <div class="label">Neto</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-lg">&nbsp;</div>

        @if($sesiones->count())
        <table class="data-table w-100">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 16%;">Fecha</th>
                    <th style="width: 16%;">Cajero</th>
                    <th style="width: 14%; text-align: right;">Apertura</th>
                    <th style="width: 14%; text-align: right;">Esperado</th>
                    <th style="width: 14%; text-align: right;">Cierre</th>
                    <th style="width: 12%; text-align: right;">Diferencia</th>
                    <th style="width: 9%; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sesiones as $s)
                <tr>
                    <td style="color: #64748b;">{{ $s->id }}</td>
                    <td>{{ $s->abierta_en ? $s->abierta_en->format('d/m/Y H:i') : '—' }}</td>
                    <td class="cell-strong">{{ strtoupper($s->abiertaPor->name ?? 'N/A') }}</td>
                    <td class="cell-right">S/ {{ number_format($s->monto_apertura, 2) }}</td>
                    <td class="cell-right">{{ $s->monto_esperado !== null ? 'S/ ' . number_format($s->monto_esperado, 2) : '—' }}</td>
                    <td class="cell-right">{{ $s->monto_cierre !== null ? 'S/ ' . number_format($s->monto_cierre, 2) : '—' }}</td>
                    <td class="cell-right {{ $s->diferencia !== null && (float) $s->diferencia == 0 ? 'badge-dif-ok' : 'badge-dif-err' }}">
                        {{ $s->diferencia !== null ? 'S/ ' . number_format($s->diferencia, 2) : '—' }}
                    </td>
                    <td class="cell-center">
                        <span class="badge badge-cerrada">{{ ucfirst($s->estado) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($sesionesConDescuadre->count())
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-left: 3px solid #dc2626; padding: 6px 10px; margin-top: 8px; font-size: 7px;">
            <strong style="color: #991b1b; text-transform: uppercase; font-size: 6px; letter-spacing: 0.3px;">Descuadres ({{ $sesionesConDescuadre->count() }})</strong><br>
            @foreach($sesionesConDescuadre as $s)
                <span style="color: #64748b;">{{ $s->abierta_en->format('d/m') }} — {{ $s->abiertaPor->name ?? '' }}</span>
                <span style="color: #dc2626; font-weight: 700;">S/ {{ number_format($s->diferencia, 2) }}</span>{{ !$loop->last ? ' · ' : '' }}
            @endforeach
        </div>
        @endif

        <div class="closing-text">Fin del reporte — {{ $sesiones->count() }} sesión(es) listada(s)</div>
        @else
        <div style="text-align: center; padding: 30px; color: #94a3b8; border: 1px dashed #cbd5e1;">
            <p style="font-size: 9px; font-weight: 600;">No se encontraron sesiones en este período.</p>
        </div>
        @endif

    </div>

</body>
</html>
