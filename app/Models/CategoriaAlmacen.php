<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaAlmacen extends Model
{
    use HasFactory;

    protected $table = 'categorias_almacen';

    protected $fillable = [
        'nombre',
        'es_serializado',
        'es_kit',
        'esquema_atributos',
    ];

    protected $casts = [
        'es_serializado' => 'boolean',
        'esquema_atributos' => 'array',
        'es_kit' => 'boolean'
    ];

    // Relaciones
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Scopes
    public function scopeSerializadas($query)
    {
        return $query->where('es_serializado', true);
    }
}
