<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte FISE — Arturo Motors</title>
    @include('pdfs.partials.base-styles')
    <style>
        .badge-aprobado { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-rechazado { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-pendiente { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-pagado { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-parcial { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-pendiente-pago { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

        .section-title { font-size: 8px; font-weight: 700; color: #0d1b30; text-transform: uppercase; letter-spacing: 0.5px; margin: 12px 0 6px 0; padding-bottom: 4px; border-bottom: 2px solid #2563eb; }
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
                        <div class="report-title">REPORTE FISE</div>
                        <div class="report-subtitle">Solicitudes, pagos y rendimiento del programa  ·  Del {{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</div>
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
                    <span class="meta-label">Fecha de emisión</span>
                    <span class="meta-value">{{ now()->format('d/m/Y') }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="meta-label">Hora de emisión</span>
                    <span class="meta-value">{{ now()->format('H:i') }} hrs.</span>
                </td>
                <td style="width: 25%;">
                    <span class="meta-label">Período</span>
                    <span class="meta-value">{{ $desde->format('d/m/Y') }} al {{ $hasta->format('d/m/Y') }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="meta-label">Emitido por</span>
                    <span class="meta-value">Sistema Arturo Motors</span>
                </td>
            </tr>
        </table>

        <div class="spacer-lg">&nbsp;</div>

        {{-- KPIs: 4 cajas principales --}}
        <table class="w-100" style="margin-bottom: 8px;">
            <tr>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-blue">
                        <div class="value">{{ number_format($totalSolicitudes) }}</div>
                        <div class="label">Solicitudes totales</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-green">
                        <div class="value">{{ $tasaAprobacion }}%</div>
                        <div class="label">Tasa aprobación</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-cyan">
                        <div class="value">S/ {{ number_format($montoTotalFise, 2) }}</div>
                        <div class="label">Monto total FISE</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-amber">
                        <div class="value">S/ {{ number_format($saldoPendiente, 2) }}</div>
                        <div class="label">Saldo pendiente</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>

        {{-- KPIs secundarios: pagos e ingresos caja --}}
        <table class="w-100" style="margin-bottom: 8px;">
            <tr>
                <td style="width: 20%; padding: 0 4px;">
                    <div class="kpi-box kpi-navy">
                        <div class="value">{{ number_format($totalPagos) }}</div>
                        <div class="label">Pagos registrados</div>
                    </div>
                </td>
                <td style="width: 20%; padding: 0 4px;">
                    <div class="kpi-box kpi-green">
                        <div class="value">{{ number_format($pagosPagados) }}</div>
                        <div class="label">Pagados</div>
                    </div>
                </td>
                <td style="width: 20%; padding: 0 4px;">
                    <div class="kpi-box kpi-amber">
                        <div class="value">{{ number_format($pagosParciales) }}</div>
                        <div class="label">Parciales</div>
                    </div>
                </td>
                <td style="width: 20%; padding: 0 4px;">
                    <div class="kpi-box kpi-blue">
                        <div class="value">{{ number_format($pagosPendientes) }}</div>
                        <div class="label">Pendientes</div>
                    </div>
                </td>
                <td style="width: 20%; padding: 0 4px;">
                    <div class="kpi-box kpi-cyan">
                        <div class="value">S/ {{ number_format($ingresosCajaFise, 2) }}</div>
                        <div class="label">Ingresos en caja</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>

        {{-- Solicitudes del período --}}
        @if($solicitudes->isEmpty())
        <div class="empty-state" style="margin-bottom: 14px;">
            <p>Sin solicitudes en el rango seleccionado.</p>
        </div>
        @else
        <div class="section-title">Solicitudes del período</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 12%;">Fecha</th>
                    <th style="width: 28%;">Cliente</th>
                    <th style="width: 20%;">Vehículo</th>
                    <th style="width: 20%; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudes as $s)
                <tr>
                    <td>{{ $s->created_at->format('d/m/Y') }}</td>
                    <td class="cell-strong">{{ $s->cliente ? ($s->cliente->nombre_completo ?? trim(($s->cliente->nombre ?? '') . ' ' . ($s->cliente->apellido ?? ''))) : '—' }}</td>
                    <td>{{ $s->vehiculo?->placa ?? '—' }}</td>
                    <td class="cell-center">
                        @php
                            $estado = $s->estado ?? '';
                            $badgeClass = match($estado) {
                                'aprobado' => 'badge-aprobado',
                                'rechazado' => 'badge-rechazado',
                                'pendiente' => 'badge-pendiente',
                                default => 'badge-pendiente',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ strtoupper($estado) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Pagos del período --}}
        @if($pagos->isEmpty())
        <div class="empty-state" style="margin-bottom: 14px;">
            <p>Sin pagos en el rango seleccionado.</p>
        </div>
        @else
        <div class="section-title">Pagos del período</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 10%;">Fecha</th>
                    <th style="width: 8%;">Orden</th>
                    <th style="width: 25%;">Cliente</th>
                    <th style="width: 15%;">Técnico</th>
                    <th style="width: 12%; text-align: right;">Total (S/)</th>
                    <th style="width: 12%; text-align: right;">Pagado (S/)</th>
                    <th style="width: 18%; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $p)
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td class="cell-strong">#{{ $p->service_order_id }}</td>
                    <td>{{ $p->serviceOrder?->cliente ? ($p->serviceOrder->cliente->nombre_completo ?? trim(($p->serviceOrder->cliente->nombre ?? '') . ' ' . ($p->serviceOrder->cliente->apellido ?? ''))) : '—' }}</td>
                    <td>{{ $p->pagadoPor?->name ?? '—' }}</td>
                    <td class="cell-right">S/ {{ number_format((float) $p->monto_total, 2) }}</td>
                    <td class="cell-right">S/ {{ number_format((float) $p->monto_pagado, 2) }}</td>
                    <td class="cell-center">
                        @php
                            $estado = $p->estado ?? '';
                            $badgeClass = match($estado) {
                                'pagado' => 'badge-pagado',
                                'parcial' => 'badge-parcial',
                                'pendiente' => 'badge-pendiente-pago',
                                default => 'badge-pendiente-pago',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ strtoupper($estado) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Pagos por técnico --}}
        @if($pagosPorTecnico->isEmpty())
        <div class="empty-state" style="margin-bottom: 14px;">
            <p>Sin pagos con técnico asignado en el rango.</p>
        </div>
        @else
        <div class="section-title">Pagos por técnico</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 40%;">Técnico</th>
                    <th style="width: 20%; text-align: right;">Operaciones</th>
                    <th style="width: 20%; text-align: right;">Monto total (S/)</th>
                    <th style="width: 20%; text-align: right;">Monto pagado (S/)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagosPorTecnico as $tecnico => $row)
                <tr>
                    <td class="cell-strong">{{ $tecnico }}</td>
                    <td class="cell-right">{{ number_format($row['cantidad']) }}</td>
                    <td class="cell-right">S/ {{ number_format((float) $row['monto_total'], 2) }}</td>
                    <td class="cell-right">S/ {{ number_format((float) $row['monto_pagado'], 2) }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #eef2f8; font-weight: 700;">
                    <td class="cell-strong">Total</td>
                    <td class="cell-right cell-strong">{{ number_format($pagosPorTecnico->sum('cantidad')) }}</td>
                    <td class="cell-right cell-strong">S/ {{ number_format((float) $pagosPorTecnico->sum('monto_total'), 2) }}</td>
                    <td class="cell-right cell-strong">S/ {{ number_format((float) $pagosPorTecnico->sum('monto_pagado'), 2) }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <div class="spacer-lg">&nbsp;</div>
        <div class="closing-rule">&nbsp;</div>
        <div class="spacer-sm">&nbsp;</div>
        <div class="closing-text">Fin del reporte &mdash; Arturo Motors · Documento de uso interno</div>

    </div>

</body>
</html>