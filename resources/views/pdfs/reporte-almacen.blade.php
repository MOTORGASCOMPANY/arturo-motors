<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Almacén — Arturo Motors</title>
    @include('pdfs.partials.base-styles')
    <style>
        .badge-red { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-amber { background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-green { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-blue { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

        .bar-track { background: #e2e8f0; height: 8px; border-radius: 4px; width: 100%; }
        .bar-fill { background: #2563eb; height: 8px; border-radius: 4px; }
        .bar-fill.cuello { background: #dc2626; }

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
                        <div class="report-title">REPORTE DE ALMACÉN</div>
                        <div class="report-subtitle">Documento generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} hrs.  ·  Sede: {{ $sedeLabel }}{{ $filtroStock !== 'todos' ? '  ·  Filtro: ' . ucfirst(str_replace('_', ' ', $filtroStock)) : '' }}</div>
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
                    <span class="meta-label">Sede</span>
                    <span class="meta-value">{{ $sedeLabel }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="meta-label">Emitido por</span>
                    <span class="meta-value">Sistema Arturo Motors</span>
                </td>
            </tr>
        </table>

        <div class="spacer-lg">&nbsp;</div>

        {{-- KPIs: 4 cajas (Kits armables, Tasa consumo %, Trazabilidad %, Alertas stock) --}}
        <table class="w-100" style="margin-bottom: 8px;">
            <tr>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-blue">
                        <div class="value">{{ $capacidad['kitsArmables'] ?? 0 }}</div>
                        <div class="label">Kits armables</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-green">
                        <div class="value">{{ $tasa['porcentaje'] ?? 0 }}%</div>
                        <div class="label">Tasa de consumo</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box kpi-cyan">
                        <div class="value">{{ $trazabilidad['porcentaje'] ?? 0 }}%</div>
                        <div class="label">Trazabilidad serie</div>
                    </div>
                </td>
                <td style="width: 25%; padding: 0 4px;">
                    <div class="kpi-box {{ ($totalSinStock ?? 0) > 0 ? 'kpi-red' : (($totalStockBajo ?? 0) > 0 ? 'kpi-amber' : 'kpi-green') }}">
                        <div class="value">{{ ($totalAlertas ?? ($totalSinStock + $totalStockBajo)) }}</div>
                        <div class="label">Alertas de stock</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="spacer-md">&nbsp;</div>

        {{-- Detalle: Kits armables (cuello de botella) --}}
        @if(($capacidad['kitsArmables'] ?? 0) === 0 && ($capacidad['cuello'] ?? '') !== 'Sin receta' && ($capacidad['cuello'] ?? ''))
        <div class="section-title">Capacidad de armado — Cuello de botella</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 60%;">Componente</th>
                    <th style="width: 20%; text-align: right;">Stock suelto</th>
                    <th style="width: 20%; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($capacidad['barras'] ?? [] as $barra)
                <tr>
                    <td class="cell-strong">{{ $barra['nombre'] }}</td>
                    <td class="cell-right">{{ $barra['cantidad'] }}</td>
                    <td class="cell-center">
                        @if($barra['esCuello'])
                            <span class="badge badge-red">Límite</span>
                        @else
                            <span class="badge badge-green">OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Stock por sede --}}
        @if($stockPorSede && $stockPorSede->count())
        <div class="section-title">Stock por sede</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 60%;">Sede</th>
                    <th style="width: 40%; text-align: right;">Unidades totales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockPorSede as $nombre => $total)
                <tr>
                    <td class="cell-strong">{{ $nombre }}</td>
                    <td class="cell-right cell-strong">{{ number_format($total) }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #eef2f8; font-weight: 700;">
                    <td class="cell-strong">Total</td>
                    <td class="cell-right cell-strong">{{ number_format($stockPorSede->sum()) }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- Stock por categoría --}}
        @if($stockPorCategoria && $stockPorCategoria->count())
        <div class="section-title">Stock por categoría</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 60%;">Categoría</th>
                    <th style="width: 40%; text-align: right;">Unidades</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockPorCategoria as $nombre => $total)
                <tr>
                    <td class="cell-strong">{{ $nombre }}</td>
                    <td class="cell-right cell-strong">{{ number_format($total) }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #eef2f8; font-weight: 700;">
                    <td class="cell-strong">{{ $productosConStock ?? 0 }} productos</td>
                    <td class="cell-right cell-strong">{{ number_format($totalItems ?? 0) }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- Distribución (tabla principal) --}}
        @if($distribucion && $distribucion->count())
        <div class="section-title">Distribución de stock por producto</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 35%;">Producto</th>
                    <th style="width: 15%;">Categoría</th>
                    @foreach ($sedes as $s)
                        @if (!$filtroSede || $s->id === $filtroSede)
                            <th style="width: 15%; text-align: right;">{{ $s->nombre }}</th>
                        @endif
                    @endforeach
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($distribucion as $row)
                <tr>
                    <td class="cell-strong">{{ $row['producto']->nombre }}</td>
                    <td class="cell-muted">{{ $row['producto']->categoria->nombre }}</td>
                    @foreach ($sedes as $s)
                        @if (!$filtroSede || $s->id === $filtroSede)
                            <td class="cell-right {{ $row['por_sede'][$s->id] > 0 ? 'cell-strong' : 'cell-muted' }}">{{ $row['por_sede'][$s->id] }}</td>
                        @endif
                    @endforeach
                    <td class="cell-right cell-strong">{{ $row['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state" style="margin-bottom: 14px;">
            <p>Sin stock registrado para los filtros seleccionados.</p>
        </div>
        @endif

        {{-- Sin stock --}}
        @if(($alertas['sinStock'] ?? collect())->count())
        <div class="section-title">Productos sin stock</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 70%;">Producto</th>
                    <th style="width: 30%; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alertas['sinStock'] as $item)
                <tr>
                    <td class="cell-strong">{{ $item['nombre'] }}</td>
                    <td class="cell-center"><span class="badge badge-red">SIN STOCK</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Stock bajo --}}
        @if(($stockBajo ?? collect())->count())
        <div class="section-title">Stock bajo — Sede: {{ $sedeLabel }}</div>
        <table class="data-table w-100" style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th style="width: 50%;">Producto</th>
                    <th style="width: 20%; text-align: right;">Disponible</th>
                    <th style="width: 15%; text-align: right;">Mínimo</th>
                    <th style="width: 15%; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockBajo as $p)
                <tr>
                    <td class="cell-strong">{{ $p->nombre }}</td>
                    <td class="cell-right cell-strong text-red"> {{ $p->stockSueltoEnSede( $filtroSede ?? null ) }} </td>
                    <td class="cell-right cell-muted">{{ $p->stock_minimo }}</td>
                    <td class="cell-center"><span class="badge badge-amber">STOCK BAJO</span></td>
                </tr>
                @endforeach
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