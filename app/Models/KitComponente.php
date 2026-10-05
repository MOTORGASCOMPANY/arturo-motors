<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    // Scopes

    public function scopeDeKit($query, int $kitId)
    {
        return $query->where('producto_kit_id', $kitId);
    }

    // Consultas

    /** Receta de un kit: cada componente con su producto y categoría. */
    public static function recetaDe(int $kitId): Collection
    {
        return static::query()
            ->join('productos', 'producto_componente_id', '=', 'productos.id')
            ->join('categorias_almacen', 'productos.categoria_id', '=', 'categorias_almacen.id')
            ->deKit($kitId)
            ->select(
                'productos.id as producto_id',
                'productos.nombre',
                'categorias_almacen.es_serializado',
                'kit_componentes.cantidad_esperada as cantidad'
            )
            ->get();
    }

    /** Cantidades esperadas por componente: [producto_componente_id => cantidad]. */
    public static function cantidadesDe(int $kitId): array
    {
        return static::query()
            ->deKit($kitId)
            ->pluck('cantidad_esperada', 'producto_componente_id')
            ->toArray();
    }

    // Acciones

    public static function eliminarDe(int $kitId): void
    {
        static::query()->deKit($kitId)->delete();
    }

    public static function quitarComponente(int $kitId, int $productoId): void
    {
        static::query()
            ->deKit($kitId)
            ->where('producto_componente_id', $productoId)
            ->delete();
    }

    public static function agregarComponente(int $kitId, int $productoId, int $cantidad): void
    {
        static::create([
            'producto_kit_id' => $kitId,
            'producto_componente_id' => $productoId,
            'cantidad_esperada' => $cantidad,
        ]);
    }

    /** Reemplaza la receta; filas con producto_componente_id y cantidad_esperada. */
    public static function reemplazarReceta(int $kitId, iterable $filas): void
    {
        static::eliminarDe($kitId);

        foreach ($filas as $fila) {
            static::agregarComponente($kitId, (int) $fila['producto_componente_id'], (int) $fila['cantidad_esperada']);
        }
    }
}
