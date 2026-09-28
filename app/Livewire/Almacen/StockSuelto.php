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

        $enKits = ItemSerializado::where('producto_id', $productoId)
            ->whereNotNull('kit_padre_id')
            ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
            ->where('sede_id', $sedeId)
            ->count();

        return max(0, (int) $cantidad - $enKits);
    }
}