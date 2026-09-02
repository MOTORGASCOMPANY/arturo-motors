<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitComponente extends Model
{
    protected $table = 'kit_componentes';

    protected $fillable = ['producto_kit_id', 'producto_componente_id', 'cantidad_esperada'];

    public function productoKit()
    {
        return $this->belongsTo(Producto::class, 'producto_kit_id');
    }

    public function componente()
    {
        return $this->belongsTo(Producto::class, 'producto_componente_id');
    }
}
