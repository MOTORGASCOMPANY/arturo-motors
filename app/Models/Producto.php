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

    protected static function sedePrincipalId(): int
    {
        return \App\Models\Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function getStockDisponibleAttribute()
    {
        return $this->stockSueltoEnSede(self::sedePrincipalId());
    }

    /**
     * Stock suelto REAL disponible en una sede (lo que se puede vender/trasladar).
     *
     * - Kits: unidades disponibles (selladas 'en_stock' o 'completado' a mano).
     * - Serializados: items sueltos (fuera de kits) en stock.
     * - Cantidad: producto_stock_sede − componentes lockeados dentro de kits.
     */
    public function stockSueltoEnSede(int $sedeId): int
    {
        $categoria = $this->categoria;

        if ($categoria?->es_kit) {
            return $this->items()
                ->whereIn('estado', ItemSerializado::ESTADOS_KIT_DISPONIBLE)
                ->where('sede_id', $sedeId)
                ->count();
        }

        if ($categoria?->es_serializado) {
            return $this->items()
                ->where('estado', 'en_stock')
                ->where('sede_id', $sedeId)
                ->whereNull('kit_padre_id')
                ->count();
        }

        $cantidad = (int) $this->stockPorSede()
            ->where('sede_id', $sedeId)
            ->sum('cantidad');

        $enKits = $this->items()
            ->whereNotNull('kit_padre_id')
            ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
            ->where('sede_id', $sedeId)
            ->count();

        return max(0, $cantidad - $enKits);
    }

    public function stockTotal(): int
    {
        // Kits: cuenta también los completados a mano (mismo criterio que
        // stockSueltoEnSede) — ver ItemSerializado::ESTADOS_KIT_DISPONIBLE.
        if ($this->categoria?->es_kit) {
            return $this->items()
                ->whereIn('estado', ItemSerializado::ESTADOS_KIT_DISPONIBLE)
                ->count();
        }

        return $this->items()->where('estado', 'en_stock')->count();
    }

    public function stockAllStates(): int
    {
        return $this->items()->count();
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
