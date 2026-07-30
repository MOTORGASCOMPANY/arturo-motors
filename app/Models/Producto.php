<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'marca',
        'atributos',
        'precio_referencial',
        'stock',
        'activo',
    ];

    protected $casts = [
        'atributos' => 'array',
        'precio_referencial' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaAlmacen::class, 'categoria_id');
    }

    public function items()
    {
        return $this->hasMany(ItemSerializado::class, 'producto_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoStock::class, 'producto_id');
    }

    // Accesor: stock real, ya sea contado o serializado
    public function getStockDisponibleAttribute()
    {
        if ($this->categoria->es_serializado) {
            return $this->items()->where('estado', 'en_stock')->count();
        }

        return $this->stock;
    }

    // Scopes
    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('marca', 'like', "%{$search}%");
        }
    }
}
