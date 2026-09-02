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
        'stock_minimo',
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

    public function stockPorSede()
    {
        return $this->hasMany(ProductoStockSede::class, 'producto_id');
    }

    public function componentes()
    {
        return $this->hasMany(KitComponente::class, 'producto_kit_id');
    }

    // Accesor: stock real, ya sea contado o serializado
    public function getStockDisponibleAttribute()
    {
        /*if ($this->categoria->es_serializado) {
            return $this->items()->where('estado', 'en_stock')->count();
        }
        return $this->stock;*/
        return $this->stockEnSede(1); // 1 = Arturo Motors (Callao)

    }
    public function stockEnSede(int $sedeId): int
    {
        if ($this->categoria->es_serializado) {
            return $this->items()
                ->where('estado', 'en_stock')
                ->where('sede_id', $sedeId)
                ->count();
        }

        return $this->stockPorSede()->where('sede_id', $sedeId)->value('cantidad') ?? 0;
    }

    public function getStockBajoAttribute()
    {
        return $this->stock_minimo > 0 && $this->stock_disponible <= $this->stock_minimo;
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
