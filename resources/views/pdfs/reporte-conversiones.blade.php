<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Conversiones GNV — Arturo Motors</title>
    @include('pdfs.partials.base-styles')
    <style>
        .badge-completada { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-enproceso { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-evaluacion { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .badge-aprobado { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .badge-entregado { background-color: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
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
                        <div class="report-title">REPORTE DE CONVERSIONES GNV</div>
                        <div class="report-subtitle">Documento generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} hrs.{{ $filtroEstado && $filtroEstado !== 'todos' ? '  ·  Estado: ' . ucfirst(str_replace('_', ' ', $filtroEstado)) : '' }}{{ $filtroSede ? '  ·  Sede: ' . ($sedeNombre ?? '') : '' }}</div>
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
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-blue">
                        <div class="value">{{ number_format($totalConversiones) }}</div>
                        <div class="label">Total Conversiones</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-green">
                        <div class="value">{{ number_format($completadas) }}</div>
                        <div class="label">Completadas</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-amber">
                        <div class="value">{{ number_format($enProceso) }}</div>
                        <div class="label">En Proceso</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-navy">
                        <div class="value">{{ number_format($itemsInstalados) }}</div>
                        <div class="label">Piezas Instaladas</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>

        @if($detalleOrdenes->count())
        <div class="cell-strong" style="font-size: 8px; margin-bottom: 6px;">DETALLE DE CONVERSIONES</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Cliente</th>
                    <th style="width: 10%;">Placa</th>
                    <th style="width: 12%;">Vehículo</th>
                    <th style="width: 10%;">Técnico</th>
                    <th style="width: 12%;">Kit</th>
                    <th style="width: 15%;">Items Serializados</th>
                    <th style="width: 5%; text-align: center;">Gen.</th>
                    <th style="width: 5%; text-align: center;">Comp.</th>
                    <th style="width: 5%; text-align: center;">Inst.</th>
                    <th style="width: 8%;">Inicio</th>
                    <th style="width: 8%;">Fin</th>
                    <th style="width: 5%; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalleOrdenes as $d)
                <tr>
                    <td class="cell-strong">{{ $d['orden']->id }}</td>
                    <td>{{ $d['cliente'] }}</td>
                    <td class="cell-strong">{{ $d['placa'] }}</td>
                    <td>{{ $d['vehiculo'] }}</td>
                    <td>{{ $d['tecnico'] }}</td>
                    <td>{{ $d['kit_nombre'] ?? $d['kit'] ?? 'N/A' }}</td>
                    <td>
                        @forelse ($d['items_serializados'] ?? [] as $item)
                            {{ $item['nombre'] }}: {{ $item['serie'] }}@if(!$loop->last)<br>@endif
                        @empty
                            —
                        @endforelse
                    </td>
                    <td class="cell-center">{{ $d['kit_generacion'] ?? $d['generacion'] ?? '' }}</td>
                    <td class="cell-center">{{ $d['total_componentes'] }}</td>
                    <td class="cell-center">{{ $d['instalados'] }}</td>
                    <td>{{ $d['fecha_inicio'] ?? '—' }}</td>
                    <td>{{ $d['fecha_fin'] ?? '—' }}</td>
                    <td class="cell-center">
                        @php
                            $estado = $d['orden']->estado ?? $d['estado'] ?? '';
                            $badgeClass = match($estado) {
                                'conversion_completada' => 'badge-completada',
                                'en_conversion' => 'badge-enproceso',
                                'en_evaluacion' => 'badge-evaluacion',
                                'aprobado_conversion' => 'badge-aprobado',
                                'entregado' => 'badge-entregado',
                                default => 'badge-enproceso',
                            };
                            $label = match($estado) {
                                'conversion_completada' => 'Completada',
                                'en_conversion' => 'En proceso',
                                'en_evaluacion' => 'En evaluación',
                                'aprobado_conversion' => 'Aprobado',
                                'entregado' => 'Entregado',
                                default => ucfirst(str_replace('_', ' ', $estado)),
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state" style="margin-bottom: 14px;">
            <p>No hay conversiones registradas para los filtros seleccionados.</p>
        </div>
        @endif

        <div class="spacer-lg">&nbsp;</div>
        <div class="closing-rule">&nbsp;</div>
        <div class="spacer-sm">&nbsp;</div>
        <div class="closing-text">Fin del reporte &mdash; {{ $totalConversiones }} conversión(es) en el período</div>

    </div>

</body>
</html>