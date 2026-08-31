<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoStockSede extends Model
{
    protected $table = 'producto_stock_sede';

    protected $fillable = ['producto_id', 'sede_id', 'cantidad'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }
}
