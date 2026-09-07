<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use App\Models\Sede;
use Livewire\Component;

class Reporte extends Component
{
    public function render()
    {
        $sedes = Sede::activas()->orderBy('id')->get();
        $productos = Producto::with('categoria')->where('activo', true)->get();

        // Stock bajo evalua solo contra Arturo Motors, porque es sede principa se compra/reabastece
        $stockBajo = $productos->filter(fn ($p) => $p->stock_bajo);

        // Matriz producto × sede, solo productos con algo de stock en cualquier lado
        $distribucion = $productos->map(function ($p) use ($sedes) {
            $porSede = [];
            foreach ($sedes as $s) {
                $porSede[$s->id] = $p->stockEnSede($s->id);
            }
            return [
                'producto' => $p,
                'por_sede' => $porSede,
                'total' => array_sum($porSede),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        // Valor total del inventario, sumando todas las sedes
        $valorTotal = $productos->sum(function ($p) use ($sedes) {
            $totalUnidades = $sedes->sum(fn ($s) => $p->stockEnSede($s->id));
            return ($p->precio_referencial ?? 0) * $totalUnidades;
        });

        // Valor desglosado por sede, para el gráfico
        $valorPorSede = $sedes->mapWithKeys(function ($s) use ($productos) {
            $valor = $productos->sum(fn ($p) => ($p->precio_referencial ?? 0) * $p->stockEnSede($s->id));
            return [$s->nombre => $valor];
        });

        $sinPrecio = $productos->filter(function ($p) use ($sedes) {
            $totalUnidades = $sedes->sum(fn ($s) => $p->stockEnSede($s->id));
            return is_null($p->precio_referencial) && $totalUnidades > 0;
        });

        return view('livewire.almacen.reporte', [
            'sedes' => $sedes,
            'distribucion' => $distribucion,
            'stockBajo' => $stockBajo,
            'valorTotal' => $valorTotal,
            'sinPrecio' => $sinPrecio,
            'labels' => $valorPorSede->keys()->toArray(),
            'data' => $valorPorSede->values()->toArray(),
        ]);
    }
}

/*public function render()
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
*/