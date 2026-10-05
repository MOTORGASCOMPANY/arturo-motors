<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    // Scopes

    /** Restringe a una sede; null = todas. */
    public function scopeEnSede($query, ?int $sedeId)
    {
        return $query->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId));
    }

    public function scopeSinSerKitNiSerializado($query)
    {
        return $query->whereHas(
            'producto.categoria',
            fn ($q) => $q->where('es_serializado', false)->where('es_kit', false)
        );
    }

    public function scopeBuscadoPorNombre($query, ?string $buscar)
    {
        if (!$buscar) {
            return $query;
        }

        return $query->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$buscar}%"));
    }

    // Consultas de listado

    /** Stock suelto real por cantidad en una sede; resta componentes en kits. */
    public static function sueltosPorCantidadEn(?int $sedeId, ?string $buscar = null): Collection
    {
        $itemsEnKits = ItemSerializado::query()
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
            ->whereNotNull('kit_padre_id')
            ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
            ->enSede($sedeId)
            ->get()
            ->groupBy(fn ($i) => $i->producto_id . ':' . $i->sede_id)
            ->map->count();

        return static::query()
            ->with(['producto.categoria', 'sede'])
            ->sinSerKitNiSerializado()
            ->enSede($sedeId)
            ->buscadoPorNombre($buscar)
            ->where('cantidad', '>', 0)
            ->get()
            ->map(function ($stock) use ($itemsEnKits) {
                $enKits = $itemsEnKits[$stock->producto_id . ':' . $stock->sede_id] ?? 0;
                $stock->cantidad_suelta_real = max(0, $stock->cantidad - $enKits);

                return $stock;
            })
            ->filter(fn ($s) => $s->cantidad_suelta_real > 0)
            ->values();
    }
}
