<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Almacén - Arturo Motors</title>
    <style>
        @page { margin: 28px 30px 50px 30px; }
        body { font-family: 'DejaVu Sans', sans-serif; margin: 0; color: #1e293b; font-size: 11px; }

        /* ── Header ─────────────────────────────── */
        .header { background: #1e40af; color: #ffffff; padding: 18px 24px; border-radius: 6px; margin-bottom: 18px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; padding: 0; background: transparent; color: #ffffff; vertical-align: middle; }
        .header h1 { font-size: 20px; margin: 0 0 4px 0; color: #ffffff; letter-spacing: 0.5px; }
        .header .sub { font-size: 11px; color: #bfdbfe; }
        .header .meta { text-align: right; font-size: 10px; color: #dbeafe; line-height: 1.6; }
        .header .meta strong { color: #ffffff; }

        /* ── KPIs ───────────────────────────────── */
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px 20px -8px; width: 103%; }
        .kpi-table td { width: 25%; text-align: center; padding: 14px 8px; background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #1e40af; vertical-align: top; }
        .kpi-table td.k-blue    { border-top-color: #2563eb; }
        .kpi-table td.k-emerald { border-top-color: #10b981; }
        .kpi-table td.k-cyan    { border-top-color: #06b6d4; }
        .kpi-table td.k-red     { border-top-color: #dc2626; }
        .kpi-table td.k-amber   { border-top-color: #f59e0b; }
        .kpi-value { font-size: 26px; font-weight: bold; color: #1e293b; }
        .kpi-value-blue { color: #2563eb; }
        .kpi-value-red { color: #dc2626; }
        .kpi-value-emerald { color: #10b981; }
        .kpi-value-amber { color: #f59e0b; }
        .kpi-value-cyan { color: #06b6d4; }
        .kpi-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-top: 4px; }
        .kpi-detail { font-size: 9px; color: #64748b; margin-top: 4px; }
        .kpi-detail strong { color: #1e293b; }

        /* ── Secciones y tablas ─────────────────── */
        .section-title { font-size: 12px; font-weight: bold; color: #1e40af; text-transform: uppercase; letter-spacing: 0.6px;
                         margin: 4px 0 8px 0; padding-bottom: 5px; border-bottom: 2px solid #1e40af; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data thead { display: table-header-group; }
        table.data tr { page-break-inside: avoid; }
        table.data th { background: #f1f5f9; padding: 7px 10px; text-align: left; font-size: 9px; font-weight: bold; color: #475569;
                        text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 2px solid #cbd5e1; }
        table.data td { padding: 7px 10px; font-size: 10.5px; border-bottom: 1px solid #e2e8f0; }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tfoot td { background: #eff6ff; font-weight: bold; color: #1e40af; border-top: 2px solid #1e40af; border-bottom: none; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        .text-amber { color: #f59e0b; }
        .text-emerald { color: #10b981; }
        .text-gray { color: #94a3b8; }

        /* ── Resumen (2 columnas) ───────────────── */
        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .two-col > tbody > tr > td { width: 50%; vertical-align: top; padding: 0; border: none; }
        .two-col .left  { padding-right: 10px; }
        .two-col .right { padding-left: 10px; }

        /* ── Capacidad de armado (barras) ───────── */
        table.bars { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.bars td { padding: 4px 6px; font-size: 10px; border: none; background: transparent; vertical-align: middle; }
        .bar-track { background: #e2e8f0; height: 8px; border-radius: 4px; width: 100%; }
        .bar-fill { background: #2563eb; height: 8px; border-radius: 4px; }
        .bar-fill.cuello { background: #dc2626; }

        /* ── Badges ─────────────────────────────── */
        .badge { padding: 2px 7px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-amber { background: #fef3c7; color: #b45309; }

        /* ── Footer (se repite en cada página) ──── */
        .footer { position: fixed; bottom: -32px; left: 0; right: 0; text-align: center; color: #94a3b8; font-size: 9px;
                  border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>

    @php
        $filtroStockLabel = match ($filtroStock) {
            'con_stock'  => 'Con stock',
            'sin_stock'  => 'Sin stock',
            'stock_bajo' => 'Stock bajo',
            default      => 'Todos',
        };
        $totalSinStock = $alertas['totalSinStock'] ?? $alertas['sinStock']->count();
        $totalStockBajo = $alertas['totalStockBajo'] ?? $alertas['stockBajo']->count();
        $totalAlertas = $totalSinStock + $totalStockBajo;
    @endphp

    {{-- Header --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1>Reporte de Almacén</h1>
                    <div class="sub">Arturo Motors — Stock actual en tiempo real</div>
                </td>
                <td class="meta">
                    Sede: <strong>{{ $sedeLabel }}</strong><br>
                    Filtro de stock: <strong>{{ $filtroStockLabel }}</strong><br>
                    Emitido: <strong>{{ now()->format('d/m/Y H:i') }}</strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- KPIs --}}
    <table class="kpi-table">
        <tr>
            <td class="k-blue">
                <div class="kpi-value kpi-value-blue">{{ $capacidad['kitsArmables'] }}</div>
                <div class="kpi-label">Kits armables</div>
                @if ($capacidad['kitsArmables'] === 0 && $capacidad['cuello'] !== 'Sin receta')
                    <div class="kpi-detail text-red">Limita: <strong>{{ $capacidad['cuello'] }}</strong></div>
                @elseif ($capacidad['kitsArmables'] > 0)
                    <div class="kpi-detail text-emerald">Stock suficiente</div>
                @else
                    <div class="kpi-detail">Sin receta definida</div>
                @endif
            </td>

            <td class="k-emerald">
                <div class="kpi-value kpi-value-emerald">{{ $tasa['porcentaje'] }}%</div>
                <div class="kpi-label">Tasa de consumo</div>
                <div class="kpi-detail"><strong>{{ $tasa['sellados'] }}</strong> sellados · <strong>{{ $tasa['consumidos'] }}</strong> consumidos</div>
            </td>

            <td class="k-cyan">
                <div class="kpi-value kpi-value-cyan">{{ $trazabilidad['porcentaje'] }}%</div>
                <div class="kpi-label">Trazabilidad de serie</div>
                <div class="kpi-detail"><strong>{{ $trazabilidad['conProduce'] }}</strong> de <strong>{{ $trazabilidad['total'] }}</strong> con lote</div>
            </td>

            <td class="{{ $totalSinStock > 0 ? 'k-red' : ($totalStockBajo > 0 ? 'k-amber' : 'k-emerald') }}">
                <div class="kpi-value {{ $totalSinStock > 0 ? 'kpi-value-red' : ($totalStockBajo > 0 ? 'kpi-value-amber' : 'kpi-value-emerald') }}">{{ $totalAlertas }}</div>
                <div class="kpi-label">Alertas de stock</div>
                <div class="kpi-detail">
                    <span class="text-red font-bold">{{ $totalSinStock }}</span> sin stock ·
                    <span class="text-amber font-bold">{{ $totalStockBajo }}</span> bajo
                </div>
            </td>
        </tr>
    </table>

    {{-- Resumen por sede y categoría --}}
    <table class="two-col">
        <tr>
            <td class="left">
                <div class="section-title">Stock por sede</div>
                <table class="data">
                    <thead><tr><th>Sede</th><th class="text-right">Unidades</th></tr></thead>
                    <tbody>
                    @forelse ($stockPorSede as $nombre => $total)
                        <tr>
                            <td class="font-bold">{{ $nombre }}</td>
                            <td class="text-right {{ $total > 0 ? 'font-bold' : 'text-gray' }}">{{ $total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-gray">Sin datos.</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot><tr><td>Total</td><td class="text-right">{{ $stockPorSede->sum() }}</td></tr></tfoot>
                </table>
            </td>
            <td class="right">
                <div class="section-title">Stock por categoría</div>
                <table class="data">
                    <thead><tr><th>Categoría</th><th class="text-right">Unidades</th></tr></thead>
                    <tbody>
                    @forelse ($stockPorCategoria as $nombre => $total)
                        <tr>
                            <td class="font-bold">{{ $nombre }}</td>
                            <td class="text-right font-bold">{{ $total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-gray">Sin datos.</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot><tr><td>{{ $productosConStock }} productos</td><td class="text-right">{{ $totalItems }}</td></tr></tfoot>
                </table>
            </td>
        </tr>
    </table>

    {{-- Distribución --}}
    <div class="section-title">Distribución de stock por sede</div>
    <table class="data">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                @foreach ($sedes as $s)
                    @if (!$filtroSede || $s->id === $filtroSede)
                        <th class="text-right">{{ $s->nombre }}</th>
                    @endif
                @endforeach
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($distribucion as $row)
            <tr>
                <td class="font-bold">{{ $row['producto']->nombre }}</td>
                <td class="text-gray">{{ $row['producto']->categoria->nombre }}</td>
                @foreach ($sedes as $s)
                    @if (!$filtroSede || $s->id === $filtroSede)
                        <td class="text-right {{ $row['por_sede'][$s->id] > 0 ? 'font-bold' : 'text-gray' }}">{{ $row['por_sede'][$s->id] }}</td>
                    @endif
                @endforeach
                <td class="text-right font-bold">{{ $row['total'] }}</td>
            </tr>
        @empty
            <tr><td colspan="{{ ($filtroSede ? 1 : $sedes->count()) + 3 }}" class="text-center text-gray">Sin stock registrado.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Sin stock --}}
    @if ($alertas['sinStock']->count())
        <div class="section-title">Productos sin stock</div>
        <table class="data">
            <thead><tr><th>Producto</th><th class="text-right">Estado</th></tr></thead>
            <tbody>
            @foreach ($alertas['sinStock'] as $item)
                <tr>
                    <td class="font-bold">{{ $item['nombre'] }}</td>
                    <td class="text-right"><span class="badge badge-red">SIN STOCK</span></td>
                </tr>
            @endforeach
            @if ($totalSinStock > $alertas['sinStock']->count())
                <tr><td colspan="2" class="text-center text-gray">y {{ $totalSinStock - $alertas['sinStock']->count() }} más…</td></tr>
            @endif
            </tbody>
        </table>
    @endif

    {{-- Stock bajo --}}
    @if ($stockBajo->count())
        <div class="section-title">Stock bajo · Sede: {{ $sedeLabel }}</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Disponible</th>
                    <th class="text-right">Mínimo</th>
                    <th class="text-right">Estado</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($stockBajo as $p)
                <tr>
                    <td class="font-bold">{{ $p->nombre }}</td>
                    <td class="text-right text-red font-bold">{{ $p->stockSueltoEnSede($filtroSede) }}</td>
                    <td class="text-right text-gray">{{ $p->stock_minimo }}</td>
                    <td class="text-right"><span class="badge badge-amber">STOCK BAJO</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Arturo Motors · Reporte de Almacén · Generado el {{ now()->format('d/m/Y H:i') }} · Documento de uso interno
    </div>

</body>
</html>