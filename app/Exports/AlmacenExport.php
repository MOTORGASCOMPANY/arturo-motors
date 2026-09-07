<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\Spreadsheet\Worksheet\Worksheet;

class AlmacenExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function collection()
    {
        $productos = Producto::with('categoria')->where('activo', true)->get();

        $stockBajo = $productos->filter(fn ($p) => $p->stock_bajo);

        $valorTotal = $productos->sum(fn ($p) => ($p->precio_referencial ?? 0) * $p->stock_disponible);

        $valorPorCategoria = $productos
            ->groupBy(fn ($p) => $p->categoria->nombre)
            ->map(fn ($grupo) => $grupo->sum(fn ($p) => ($p->precio_referencial ?? 0) * $p->stock_disponible))
            ->sortByDesc(fn ($v) => $v);

        $sinPrecio = $productos->filter(fn ($p) => is_null($p->precio_referencial) && $p->stock_disponible > 0);

        $productosBajoMinimo = $productos->filter(fn ($p) => $p->stock_disponible <= $p->stock_minimo);

        return collect([
            'productos' => $productos,
            'stockBajo' => $stockBajo,
            'valorTotal' => $valorTotal,
            'valorPorCategoria' => $valorPorCategoria,
            'sinPrecio' => $sinPrecio,
            'productosBajoMinimo' => $productosBajoMinimo,
        ]);
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Categoría',
            'Stock Disponible',
            'Stock Mínimo',
            'Precio Referencial',
            'Valor Total',
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->nombre,
            $producto->categoria->nombre ?? 'N/A',
            $producto->stock_disponible,
            $producto->stock_minimo,
            '$' . number_format($producto->precio_referencial ?? 0, 2),
            '$' . number_format(($producto->precio_referencial ?? 0) * $producto->stock_disponible, 2),
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFont()->setColor(['argb' => 'FFFFFF']);
        $sheet->getStyle('A1:F1')->getFill()->setStartColor(['argb' => '4287f5']);
    }

    public function title(): string
    {
        return 'Reporte de Almacén';
    }
}