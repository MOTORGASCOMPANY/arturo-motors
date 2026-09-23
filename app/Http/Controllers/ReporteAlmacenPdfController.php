<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Sede;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteAlmacenPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $sedes = Sede::activas()->orderBy('id')->get();
        $productos = Producto::with('categoria')->where('activo', true)->get();

        // Filtros
        $filtroSede = $request->input('sede_id') ? (int) $request->input('sede_id') : null;
        $filtroStock = $request->input('stock', 'todos');

        // Stock bajo
        $stockBajo = $productos->filter(fn ($p) => $p->stock_bajo);

        // Matriz producto × sede
        $distribucion = $productos->map(function ($p) use ($sedes) {
            $porSede = [];
            foreach ($sedes as $s) {
                $porSede[$s->id] = $p->stockSueltoEnSede($s->id);
            }
            return [
                'producto' => $p,
                'por_sede' => $porSede,
                'total' => array_sum($porSede),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        // Aplicar filtros
        if ($filtroSede) {
            $distribucion = $distribucion->filter(
                fn ($row) => $row['por_sede'][$filtroSede] > 0
            );
        }

        $distribucion = match ($filtroStock) {
            'con_stock' => $distribucion->filter(fn ($row) => $row['total'] > 0),
            // Rebuild desde todos los productos (igual que el reporte web):
            // $distribucion ya viene filtrado a total > 0, así que sin_stock
            // tenía que partir de cero — antes devolvía collect() vacío.
            'sin_stock' => $productos->map(function ($p) use ($sedes) {
                $porSede = [];
                foreach ($sedes as $s) {
                    $porSede[$s->id] = $p->stockSueltoEnSede($s->id);
                }
                return [
                    'producto' => $p,
                    'por_sede' => $porSede,
                    'total' => array_sum($porSede),
                ];
            })->filter(fn ($row) => $row['total'] === 0),
            'stock_bajo' => $distribucion->filter(fn ($row) => $row['producto']->stock_bajo),
            default => $distribucion,
        };

        // Totales
        $totalItems = $distribucion->sum('total');
        $productosConStock = $distribucion->count();

        // Stock por sede
        $stockPorSede = $sedes->mapWithKeys(function ($s) use ($productos) {
            $total = $productos->sum(fn ($p) => $p->stockSueltoEnSede($s->id));
            return [$s->nombre => $total];
        });

        // Stock por categoría
        $stockPorCategoria = $distribucion
            ->groupBy(fn ($row) => $row['producto']->categoria->nombre)
            ->map(fn ($grupo) => $grupo->sum('total'))
            ->sortByDesc(fn ($v) => $v);

        $pdf = Pdf::loadView('pdfs.reporte-almacen', [
            'sedes' => $sedes,
            'distribucion' => $distribucion,
            'stockBajo' => $stockBajo,
            'totalItems' => $totalItems,
            'productosConStock' => $productosConStock,
            'stockPorSede' => $stockPorSede,
            'stockPorCategoria' => $stockPorCategoria,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-almacen-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}
