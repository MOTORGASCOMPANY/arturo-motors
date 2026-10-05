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
        return Sede::primeraActivaId();
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
     *
     * $sedeId = null → todas las sedes (filtro "Todas" de los reportes).
     */
    public function stockSueltoEnSede(?int $sedeId = null): int
    {
        $categoria = $this->categoria;

        if ($categoria?->es_kit) {
            return $this->items()
                ->whereIn('estado', ItemSerializado::ESTADOS_KIT_DISPONIBLE)
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->count();
        }

        if ($categoria?->es_serializado) {
            return $this->items()
                ->where('estado', 'en_stock')
                ->whereNull('kit_padre_id')
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->count();
        }

        $cantidad = (int) $this->stockPorSede()
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->sum('cantidad');

        $enKits = $this->items()
            ->whereNotNull('kit_padre_id')
            ->whereIn('estado', ItemSerializado::ESTADOS_DENTRO_DE_KIT)
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
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

    /**
     * Verifica si el producto tiene stock bajo en una sede específica.
     *
     * @param int|null $sedeId null → todas las sedes
     * @return bool
     */
    public function stockBajoEnSede(?int $sedeId = null): bool
    {
        return $this->stock_minimo > 0 && $this->stockSueltoEnSede($sedeId) <= $this->stock_minimo;
    }

    /**
     * @deprecated Usar stockBajoEnSede($sedeId) en su lugar.
     * Mantiene compatibilidad: usa sede principal (Callao = 1).
     */
    public function getStockBajoAttribute(): bool
    {
        return $this->stockBajoEnSede(self::sedePrincipalId());
    }

    // Scopes
    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('marca', 'like', "%{$search}%");
        }
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /** Producto por nombre exacto. */
    public static function porNombre(string $nombre): ?self
    {
        return static::query()->where('nombre', $nombre)->first();
    }

    public static function existeConNombre(string $nombre, ?int $exceptoId = null): bool
    {
        return static::query()
            ->where('nombre', 'LIKE', $nombre)
            ->when($exceptoId, fn ($q) => $q->where('id', '!=', $exceptoId))
            ->exists();
    }

    /** Productos por id, con su categoría cargada. */
    public static function porIds(array $ids): \Illuminate\Database\Eloquent\Builder
    {
        return static::query()->with('categoria')->whereIn('id', $ids);
    }

    public static function conCategoria(int $id): ?self
    {
        return static::query()->with('categoria')->find($id);
    }

    /** Comprueba la categoría; acepta id, nombre o instancia. */
    public static function esSerializable(mixed $producto): bool
    {
        if (is_int($producto)) {
            $producto = static::with('categoria')->find($producto);
        }
        if (is_string($producto)) {
            $producto = static::porNombre($producto);
        }
        if (!$producto instanceof self) {
            return false;
        }

        return (bool) ($producto->categoria->es_serializado ?? false);
    }

    /** Filtro por nombre; scopeBuscar además busca la marca. */
    public function scopeBuscadoPorNombre($query, ?string $buscar)
    {
        if (!$buscar) {
            return $query;
        }

        return $query->where('nombre', 'like', "%{$buscar}%");
    }

    public function scopeSinSerKit($query)
    {
        return $query->whereHas('categoria', fn ($q) => $q->where('es_kit', false));
    }

    /** Productos por cantidad: ni kits ni serializados. */
    public function scopePorCantidad($query)
    {
        return $query->whereHas(
            'categoria',
            fn ($q) => $q->where('es_kit', false)->where('es_serializado', false)
        );
    }

    public function scopeConStockEn($query, int $sedeId)
    {
        return $query->whereHas(
            'stockPorSede',
            fn ($q) => $q->where('sede_id', $sedeId)->where('cantidad', '>', 0)
        );
    }

    /** Productos por cantidad disponibles en una sede; stock suelto en $p->disponible. */
    public static function conCantidadDisponibleEn(int $sedeId): \Illuminate\Support\Collection
    {
        return static::query()
            ->with('categoria')
            ->activo()
            ->porCantidad()
            ->conStockEn($sedeId)
            ->get()
            ->map(fn ($p) => tap($p, fn ($p) => $p->disponible = $p->stockSueltoEnSede($sedeId)))
            ->filter(fn ($p) => $p->disponible > 0)
            ->values();
    }

    public static function kitsActivos(): \Illuminate\Support\Collection
    {
        return static::query()
            ->with('categoria')
            ->activo()
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', true))
            ->get();
    }

    public static function serializadosActivos(): \Illuminate\Support\Collection
    {
        return static::query()
            ->with('categoria')
            ->activo()
            ->whereHas('categoria', fn ($q) => $q->where('es_serializado', true)->where('es_kit', false))
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();
    }

    public static function porCantidadActivos(): \Illuminate\Support\Collection
    {
        return static::query()
            ->with('categoria')
            ->activo()
            ->porCantidad()
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();
    }
}
