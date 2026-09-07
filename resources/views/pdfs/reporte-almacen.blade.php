<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Almacén - Arturo Motors</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; color: #2d3748; margin-bottom: 5px; }
        .header p { color: #4a5568; font-size: 14px; }
        .section { margin-bottom: 30px; }
        .kpi { text-align: center; padding: 15px; background: #f7fafc; border-radius: 8px; margin: 10px 0; }
        .kpi .value { font-size: 24px; font-weight: bold; }
        .kpi .label { font-size: 12px; color: #718096; text-transform: uppercase; margin-top: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: left; }
        .table th { background: #edf2f7; font-weight: bold; font-size: 12px; }
        .table tr:nth-child(even) { background: #fafafa; }
        .chart { text-align: center; margin: 20px 0; }
        .stock-bajo { color: #dc2626; }
        .sin-precio { color: #d97706; }
    </style>
</head>
<body>

<div class="header">
    <h1>Reporte de Almacén</h1>
    <p>Arturo Motors - Callao</p>
</div>

<div class="kpi">
    <div class="value" style="color: #38a169;">S/ {{ number_format($valorTotal, 2) }}</div>
    <div class="label">Valor Total del Inventario</div>
</div>

<h3>Stock Bajo (<span class="stock-bajo">{{ count($stockBajo) }}</span>)</h3>
<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Disponible</th>
            <th>Mínimo</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($stockBajo as $p)
        <tr class="{{ $p->stock_disponible <= $p->stock_minimo ? 'bg-red-50' : '' }}">
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->categoria->nombre }}</td>
            <td class="text-red-600 font-semibold">{{ $p->stock_disponible }}</td>
            <td class="text-gray-500">{{ $p->stock_minimo }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center">Ningún producto está bajo su mínimo configurado.</td></tr>
    @endforelse
    </tbody>
</table>

<h3>Stock pero sin precio referencial (<span class="sin-precio">{{ count($sinPrecio) }}</span>)</h3>
<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($sinPrecio as $p)
        <tr class="bg-amber-50">
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->categoria->nombre }}</td>
            <td class="text-amber-600">{{ $p->stock_disponible }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-center">Ningún producto tiene stock sin precio referencial.</td></tr>
    @endforelse
    </tbody>
</table>

<h3>Productos con stock por debajo del mínimo</h3>
<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Disponible</th>
            <th>Mínimo</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($productosBajoMinimo as $p)
        <tr class="bg-red-50 text-red-600">
            <td>{{ $p->nombre }}</td>
            <td>{{ $p->categoria->nombre }}</td>
            <td class="text-red-600">{{ $p->stock_disponible }}</td>
            <td class="text-red-600">{{ $p->stock_minimo }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center">Ningún producto requiere reabastecimiento urgente.</td></tr>
    @endforelse
    </tbody>
</table>

<h3>Valor de inventario por categoría</h3>
<table class="table">
    <thead>
        <tr>
            <th>Categoría</th>
            <th>Valor (S/)</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($valorPorCategoria as $categoria => $valor)
        <tr>
            <td>{{ $categoria }}</td>
            <td>S/ {{ number_format($valor, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="2" class="text-center">No hay categorías con valor asignado.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer" style="margin-top: 50px; font-size: 12px; color: #718096; text-align: center;">
    Documento generado el {{ now()->format('d/m/Y H:i') }} — Arturo Motors
</div>

</body>
</html>