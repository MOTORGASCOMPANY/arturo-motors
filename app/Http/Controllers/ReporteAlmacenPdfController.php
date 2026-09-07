<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteAlmacenPdfController extends Controller
{
    public function __invoke(Request $request)
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

        $pdf = Pdf::loadView('pdfs.reporte-almacen', [
            'productos' => $productos,
            'stockBajo' => $stockBajo,
            'valorTotal' => $valorTotal,
            'valorPorCategoria' => $valorPorCategoria,
            'sinPrecio' => $sinPrecio,
            'productosBajoMinimo' => $productosBajoMinimo,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-almacen-' . now()->format('Y-m-d-Hi') . '.pdf');
    }
}