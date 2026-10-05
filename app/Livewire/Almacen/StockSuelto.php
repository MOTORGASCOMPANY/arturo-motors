<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\ProductoStockSede;

trait StockSuelto
{
    /** Stock suelto REAL (cantidad en sede − componentes lockeados dentro de kits). */
    private function sueltoDisponible(int $productoId, int $sedeId): int
    {
        $cantidad = ProductoStockSede::where('producto_id', $productoId)
            ->where('sede_id', $sedeId)
            ->sum('cantidad');

        $enKits = ItemSerializado::montadasEnKit($productoId, $sedeId);

        return max(0, (int) $cantidad - $enKits);
    }
}