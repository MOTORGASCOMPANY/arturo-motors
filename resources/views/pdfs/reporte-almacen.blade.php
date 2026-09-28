<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Almacén - Arturo Motors</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 30px; color: #1e293b; font-size: 12px; }
        
        /* Header */
        .header { text-align: center; margin-bottom: 25px; padding: 20px; background: #1e40af; color: #ffffff; border-radius: 8px; }
        .header h1 { font-size: 22px; margin-bottom: 5px; color: #ffffff; }
        .header p { font-size: 12px; color: #ffffff; }
        
        /* KPIs - tabla para compatibilidad con DomPDF */
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .kpi-table td { width: 25%; text-align: center; padding: 15px; background: #ffffff; border: 1px solid #e2e8f0; vertical-align: top; }
        .kpi-value { font-size: 28px; font-weight: bold; color: #1e293b; }
        .kpi-value-blue { color: #2563eb; }
        .kpi-value-red { color: #dc2626; }
        .kpi-value-emerald { color: #10b981; }
        .kpi-value-amber { color: #f59e0b; }
        .kpi-value-cyan { color: #06b6d4; }
        .kpi-label { font-size: 10px; color: #64748b; text-transform: uppercase; margin-top: 5px; }
        
        /* KPI detail rows */
        .kpi-detail { font-size: 10px; color: #64748b; margin-top: 2px; }
        .kpi-detail strong { color: #1e293b; }
        
        /* Tabla principal */
        .section-title { font-size: 14px; font-weight: bold; color: #1e293b; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f1f5f9; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: bold; color: #475569; text-transform: uppercase; border-bottom: 2px solid #cbd5e1; }
        td { padding: 8px 10px; font-size: 11px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        .text-gray { color: #94a3b8; }
        
        /* Footer */
        .footer { text-align: center; margin-top: 30px; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        
        /* Capacidad barras */
        .capacidad-barras { margin-top: 10px; }
        .capacidad-barra { margin-bottom: 5px; font-size: 10px; }
        .capacidad-barra-label { display: inline-block; width: 80px; font-weight: bold; }
        .capacidad-barra-bar { display: inline-block; height: 8px; background: #e2e8f0; border-radius: 4px; width: 100px; position: relative; }
        .capacidad-barra-fill { height: 100%; border-radius: 4px; background: #4f46e5; }
        .capacidad-barra-fill.cuello { background: #dc2626; }
        .capacidad-barra-value { display: inline-block; width: 30px; text-align: right; margin-left: 5px; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>Reporte de Almacén</h1>
        <p>Arturo Motors — Stock actual en tiempo real · {{ $sedeLabel }}</p>
    </div>

    {{-- KPIs Dashboard --}}
    <table class="kpi-table">
        <tr>
            {{-- 1. Capacidad de armado --}}
            <td>
                <div class="kpi-value kpi-value-blue">{{ $capacidad['kitsArmables'] }}</div>
                <div class="kpi-label">Kits Armables</div>
                @if ($capacidad['kitsArmables'] === 0 && $capacidad['cuello'] !== 'Sin receta')
                    <div class="kpi-detail text-red">Frena: <strong>{{ $capacidad['cuello'] }}</strong></div>
                @elseif ($capacidad['kitsArmables'] > 0)
                    <div class="kpi-detail text-emerald">Stock suficiente</div>
                @endif
            </td>
            
            {{-- 2. Tasa de consumo --}}
            <td>
                <div class="kpi-value kpi-value-emerald">{{ $tasa['porcentaje'] }}%</div>
                <div class="kpi-label">Tasa Consumo</div>
                <div class="kpi-detail"><strong>{{ $tasa['sellados'] }}</strong> sellados · <strong>{{ $tasa['consumidos'] }}</strong> consumidos</div>
            </td>
            
            {{-- 3. Trazabilidad --}}
            <td>
                <div class="kpi-value kpi-value-cyan">{{ $trazabilidad['porcentaje'] }}%</div>
                <div class="kpi-label">Trazabilidad Serie</div>
                <div class="kpi-detail"><strong>{{ $trazabilidad['conProduce'] }}</strong> / <strong>{{ $trazabilidad['total'] }}</strong> con lote</div>
            </td>
            
            {{-- 4. Alertas --}}
            <td>
                <div class="kpi-value {{ $alertas['sinStock']->count() > 0 ? 'kpi-value-red' : ($alertas['stockBajo']->count() > 0 ? 'kpi-value-amber' : 'kpi-value-emerald') }}">
                    {{ $alertas['sinStock']->count() + $alertas['stockBajo']->count() }}
                </div>
                <div class="kpi-label">Alertas Stock</div>
                <div class="kpi-detail">
                    <span class="text-red">{{ $alertas['sinStock']->count() }}</span> sin stock · 
                    <span class="text-amber">{{ $alertas['stockBajo']->count() }}</span> bajo
                </div>
            </td>
        </tr>
    </table>

    {{-- Detalle Capacidad de Armado --}}
    @if ($capacidad['barras'] && count($capacidad['barras']) > 0)
        <div class="section-title">Capacidad de Armado por Componente</div>
        <div class="capacidad-barras">
            @php
                $maxCap = max(array_column($capacidad['barras'], 'cantidad')) ?: 1;
            @endphp
            @foreach ($capacidad['barras'] as $barra)
                @php
                    $pct = $maxCap > 0 ? min(100, ($barra['cantidad'] / $maxCap) * 100) : 0;
                @endphp
                <div class="capacidad-barra">
                    <span class="capacidad-barra-label">{{ $barra['nombre'] }}</span>
                    <div class="capacidad-barra-bar">
                        <div class="capacidad-barra-fill {{ $barra['esCuello'] ? 'cuello' : '' }}" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="capacidad-barra-value">{{ $barra['cantidad'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Tabla de Distribución --}}
    <div class="section-title">Distribución de Stock por Sede</div>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                @foreach ($sedes as $s)
                    @if (!$filtroSede || $s->id === $filtroSede)
                        <th style="text-align: right;">{{ $s->nombre }}</th>
                    @endif
                @endforeach
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($distribucion as $row)
            <tr>
                <td class="font-bold">{{ $row['producto']->nombre }}</td>
                <td class="text-gray">{{ $row['producto']->categoria->nombre }}</td>
                @foreach ($sedes as $s)
                    @if (!$filtroSede || $s->id === $filtroSede)
                        <td class="text-right {{ $row['por_sede'][$s->id] > 0 ? 'font-bold' : 'text-gray' }}">
                            {{ $row['por_sede'][$s->id] }}
                        </td>
                    @endif
                @endforeach
                <td class="text-right font-bold">{{ $row['total'] }}</td>
            </tr>
        @empty
            <tr><td colspan="{{ ($filtroSede ? 1 : $sedes->count()) + 3 }}" class="text-center text-gray">Sin stock registrado.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Stock Bajo --}}
    @if ($stockBajo->count())
        <div class="section-title">Stock Bajo (Sede: {{ $filtroSede ? $sedes->firstWhere('id', $filtroSede)?->nombre : 'Todas' }})</div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="text-align: right;">Disponible</th>
                    <th style="text-align: right;">Mínimo</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($stockBajo as $p)
                <tr>
                    <td class="font-bold">{{ $p->nombre }}</td>
                    <td class="text-right text-red font-bold">{{ $p->stockSueltoEnSede($filtroSede ?? 1) }}</td>
                    <td class="text-right text-gray">{{ $p->stock_minimo }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }} — Arturo Motors
    </div>

</body>
</html>