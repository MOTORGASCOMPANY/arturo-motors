<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrasladoDetalle extends Model
{
    protected $table = 'traslado_detalles';

    protected $fillable = ['traslado_id', 'producto_id', 'item_serializado_id', 'cantidad'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function itemSerializado()
    {
        return $this->belongsTo(ItemSerializado::class, 'item_serializado_id');
    }
}
