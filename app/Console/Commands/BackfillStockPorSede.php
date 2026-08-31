<?php

namespace App\Console\Commands;

use App\Models\Producto;
use App\Models\ProductoStockSede;
use Illuminate\Console\Command;

class BackfillStockPorSede extends Command
{

    protected $signature = 'almacen:backfill-stock-sede';
    protected $description = 'Migra productos.stock a producto_stock_sede con sede_id=1 (Arturo Motors)';

    public function handle()
    {
        $productos = Producto::where('stock', '>', 0)->get();

        $this->info("Migrando stock de {$productos->count()} producto(s) a Arturo Motors (sede_id=1)...");

        foreach ($productos as $producto) {
            ProductoStockSede::updateOrCreate(
                ['producto_id' => $producto->id, 'sede_id' => 1],
                ['cantidad' => $producto->stock]
            );

            $this->line("  - {$producto->nombre}: {$producto->stock} unidades");
        }

        $this->info('Listo. La columna productos.stock NO se modificó ni se borró — queda como respaldo.');
    }
}
