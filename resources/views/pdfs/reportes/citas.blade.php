<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Citas — Arturo Motors</title>
    <style>
        @page {
            margin: 10px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }
        html, body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #1c2d42;
            line-height: 1.35;
        }

        /* ===== PALETA UNIFICADA (familia navy, un solo tono base) ===== */
        :root {
            --navy-900: #14233f;
            --navy-800: #1c3057;
            --blue-700: #2e5286;
            --steel-500: #64748b;
        }

        /* ===== CONTENEDOR CON MARGEN REAL ===== */
        .page-wrap {
            padding: 26px 34px 40px 34px;
        }

        table { width: 100%; border-collapse: collapse; }
        .w-100 { width: 100%; }
        .spacer-sm { height: 6px; line-height: 6px; font-size: 1px;}
        .spacer-md { height: 10px; line-height: 10px; font-size: 1px;}
        .spacer-lg { height: 16px; line-height: 16px; font-size: 1px;}

        /* ===== CABECERA ===== */
        .header-table td { vertical-align: middle; }
        .logo-cell { width: 46%; padding-right: 16px; }
        .title-cell { width: 54%; }

        .logo {
            max-height: 36px;
            width: auto;
            display: block;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }

        .company-title {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 13px;
            font-weight: 700;
            color: #0d1b30;
            letter-spacing: 0.4px;
            margin-top: 5px;
        }
        .company-subtitle {
            font-size: 7.3px;
            color: #2e5286;
            font-weight: 700;
            letter-spacing: 0.6px;
            margin-top: 2px;
        }
        .company-info {
            font-size: 7px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 4px;
        }

        .report-title-box {
            background-color: #14233f;
            color: #ffffff;
            padding: 10px 16px;
            text-align: center;
        }
        .report-title {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
        }
        .report-subtitle {
            font-size: 7px;
            color: #c7d2e0;
            margin-top: 3px;
        }

        .header-rule {
            border-top: 2px solid #14233f;
            font-size: 1px;
            line-height: 1px;
        }

        /* ===== RESUMEN ===== */
        .meta-table td {
            font-size: 7.3px;
            padding: 7px 11px;
            background-color: #f7f9fc;
            border: 1px solid #e2e8f0;
        }
        .meta-label {
            display: block;
            font-size: 6.5px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .meta-value {
            font-size: 8px;
            color: #0d1b30;
            font-weight: 700;
        }

        /* ===== FILTROS ===== */
        .filters-box {
            background-color: #eef2f8;
            border: 1px solid #c9d6e8;
            border-left: 3px solid #1c3057;
            padding: 7px 11px;
            font-size: 7.3px;
        }
        .filters-label {
            color: #1c3057;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-right: 6px;
        }
        .filters-value { color: #1c2d42; font-weight: 600; }
        .filters-sep { color: #9db3cf; margin: 0 5px; }

        /* ===== TABLA DE DATOS ===== */
        .data-table { border: 1px solid #94a3b8; }
        .data-table thead th {
            background-color: #14233f;
            color: #ffffff;
            font-size: 7px;
            font-weight: 700;
            padding: 7px 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-right: 1px solid #2c3e5c;
            text-align: left;
        }
        .data-table thead th:last-child { border-right: none; }
        .data-table tbody td {
            padding: 6px 8px;
            font-size: 7.5px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #1c2d42;
        }
        .data-table tbody td:last-child { border-right: none; }
        .data-table tbody tr:nth-child(even) { background-color: #f7f9fc; }
        .cell-id { color: #64748b; font-weight: 600; }
        .cell-strong { font-weight: 700; color: #0d1b30; }
        .cell-muted { color: #64748b; }

        /* ===== ESTADOS ===== */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .badge-pendiente { background-color: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .badge-aceptada  { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-rechazada { background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-cancelada { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* ===== VACÍO ===== */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
            background-color: #f7f9fc;
        }
        .empty-state p { font-size: 10px; font-weight: 600; }

        /* ===== CIERRE ===== */
        .closing-rule { border-top: 1px solid #cbd5e1; font-size: 1px; line-height: 1px; }
        .closing-text {
            text-align: center;
            font-size: 6.5px;
            color: #94a3b8;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        /* ===== FOOTER FIJO ===== */
        .page-footer {
            position: fixed;
            bottom: -32px;
            left: 34px;
            right: 34px;
        }
        .page-footer table td {
            border-top: 1px solid #cbd5e1;
            padding-top: 5px;
            font-size: 6.5px;
            color: #94a3b8;
        }
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

        <!-- CABECERA -->
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
                        <div class="report-title">REPORTE DE CITAS</div>
                        <div class="report-subtitle">Documento generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} hrs.</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>
        <div class="header-rule">&nbsp;</div>
        <div class="spacer-md">&nbsp;</div>

        <!-- RESUMEN -->
        <table class="meta-table w-100">
            <tr>
                <td style="width: 25%;">
                    <span class="meta-label">Total de registros</span>
                    <span class="meta-value">{{ $citas->count() }} cita(s)</span>
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

        @if($search || $estado !== 'todos' || $fechaInicio || $fechaFin)
            <div class="spacer-sm">&nbsp;</div>
            <div class="filters-box">
                <span class="filters-label">Filtros aplicados:</span>
                @if($search)
                    <span class="filters-value">Búsqueda "{{ $search }}"</span>
                @endif
                @if($estado !== 'todos')
                    <span class="filters-sep">|</span>
                    <span class="filters-value">Estado: {{ ucfirst($estado) }}</span>
                @endif
                @if($fechaInicio)
                    <span class="filters-sep">|</span>
                    <span class="filters-value">Desde: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</span>
                @endif
                @if($fechaFin)
                    <span class="filters-sep">|</span>
                    <span class="filters-value">Hasta: {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>
                @endif
            </div>
        @endif

        <div class="spacer-lg">&nbsp;</div>

        <!-- TABLA DE CITAS -->
        @if($citas->count())
        <table class="data-table w-100">
            <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">Fecha</th>
                <th style="width: 20%;">Cliente</th>
                <th style="width: 10%;">Documento</th>
                <th style="width: 10%;">Placa</th>
                <th style="width: 18%;">Asesor</th>
                <th style="width: 12%;">Estado</th>
            </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                <tr>
                    <td class="cell-id">{{ str_pad($cita->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $cita->fecha_cita->format('d/m/Y') }}<br><span class="cell-muted">{{ $cita->fecha_cita->format('H:i') }} hrs.</span></td>
                    <td class="cell-strong">{{ $cita->cliente->nombre ?? 'N/A' }} {{ $cita->cliente->apellido ?? '' }}</td>
                    <td class="cell-muted">{{ $cita->cliente->documento ?? '—' }}</td>
                    <td class="cell-muted">{{ $cita->vehiculo->placa ?? 'N/A' }}</td>
                    <td>{{ $cita->asesor->name ?? 'N/A' }}</td>
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

        <div class="spacer-lg">&nbsp;</div>

        <!-- CIERRE -->
        <div class="closing-rule">&nbsp;</div>
        <div class="spacer-sm">&nbsp;</div>
        <div class="closing-text">Fin del reporte &mdash; {{ $citas->count() }} registro(s) listado(s)</div>

        @else
        <div class="empty-state">
            <p>No se encontraron citas con los filtros seleccionados.</p>
        </div>
        @endif

    </div><!-- /.page-wrap -->

</body>
</html>