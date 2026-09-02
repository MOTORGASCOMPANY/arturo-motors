<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use Livewire\Component;

class Reporte extends Component
{
    public function render()
    {
        $productos = Producto::with('categoria')->where('activo', true)->get();

        $stockBajo = $productos->filter(fn ($p) => $p->stock_bajo);

        $valorTotal = $productos->sum(fn ($p) => ($p->precio_referencial ?? 0) * $p->stock_disponible);

        $valorPorCategoria = $productos
            ->groupBy(fn ($p) => $p->categoria->nombre)
            ->map(fn ($grupo) => $grupo->sum(fn ($p) => ($p->precio_referencial ?? 0) * $p->stock_disponible))
            ->sortByDesc(fn ($v) => $v);

        $sinPrecio = $productos->filter(fn ($p) => is_null($p->precio_referencial) && $p->stock_disponible > 0);

        return view('livewire.almacen.reporte', [
            'productos' => $productos,
            'stockBajo' => $stockBajo,
            'valorTotal' => $valorTotal,
            'valorPorCategoria' => $valorPorCategoria,
            'sinPrecio' => $sinPrecio,
            'labels' => $valorPorCategoria->keys()->toArray(),
            'data' => $valorPorCategoria->values()->toArray(),
        ]);
    }
}
