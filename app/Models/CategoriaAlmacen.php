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

    public function scopeSinKits($query)
    {
        return $query->where('es_kit', false);
    }

    /** Categorías por cantidad: ni kit ni serializadas. */
    public function scopePorCantidad($query)
    {
        return $query->where('es_serializado', false)->where('es_kit', false);
    }

    public function scopeConEsquema($query)
    {
        return $query->whereNotNull('esquema_atributos');
    }

    // Consultas de listado

    public static function porCantidadOrdenadas(): \Illuminate\Support\Collection
    {
        return static::query()->porCantidad()->orderBy('nombre')->get();
    }

    public static function serializadasConEsquema(): \Illuminate\Support\Collection
    {
        return static::query()->serializadas()->sinKits()->conEsquema()->orderBy('nombre')->get();
    }
}
