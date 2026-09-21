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
        .kpi-table td { width: 33%; text-align: center; padding: 15px; background: #ffffff; border: 1px solid #e2e8f0; }
        .kpi-value { font-size: 28px; font-weight: bold; color: #1e293b; }
        .kpi-value-blue { color: #2563eb; }
        .kpi-value-red { color: #dc2626; }
        .kpi-label { font-size: 10px; color: #64748b; text-transform: uppercase; margin-top: 5px; }
        
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
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>Reporte de Almacén</h1>
        <p>Arturo Motors — Stock actual en tiempo real</p>
    </div>

    {{-- KPIs --}}
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-value kpi-value-blue">{{ number_format($totalItems) }}</div>
                <div class="kpi-label">Total de items</div>
            </td>
            <td>
                <div class="kpi-value">{{ $productosConStock }}</div>
                <div class="kpi-label">Productos con stock</div>
            </td>
            <td>
                <div class="kpi-value {{ $stockBajo->count() > 0 ? 'kpi-value-red' : '' }}">{{ $stockBajo->count() }}</div>
                <div class="kpi-label">Stock bajo</div>
            </td>
        </tr>
    </table>

    {{-- Tabla de Distribución --}}
    <div class="section-title">Distribución de Stock por Sede</div>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                @foreach ($sedes as $s)
                    <th style="text-align: right;">{{ $s->nombre }}</th>
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
                    <td class="text-right {{ $row['por_sede'][$s->id] > 0 ? 'font-bold' : 'text-gray' }}">
                        {{ $row['por_sede'][$s->id] }}
                    </td>
                @endforeach
                <td class="text-right font-bold">{{ $row['total'] }}</td>
            </tr>
        @empty
            <tr><td colspan="{{ $sedes->count() + 3 }}" class="text-center text-gray">Sin stock registrado.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Stock Bajo --}}
    @if ($stockBajo->count())
        <div class="section-title">Stock Bajo en Callao</div>
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
                    <td class="text-right text-red font-bold">{{ $p->stockEnSede(1) }}</td>
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
