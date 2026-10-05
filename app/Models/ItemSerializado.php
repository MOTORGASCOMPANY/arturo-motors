<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReportePiezaNoEncajada;

class ItemSerializado extends Model
{
    use HasFactory;

    protected $table = 'items_serializados';

    protected $fillable = [
        'producto_id',
        'kit_padre_id',
        'serie',
        'atributos',
        'estado',
        'sede_id',
        'service_order_id',
        'vehiculo_instalado_id',
        'fecha_instalacion_reportada',
    ];

    protected $casts = [
        'atributos' => 'array',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function vehiculoInstalado()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_instalado_id');
    }

    public function kitPadre()
    {
        return $this->belongsTo(ItemSerializado::class, 'kit_padre_id');
    }

    public function piezasEnKit()
    {
        return $this->hasMany(ItemSerializado::class, 'kit_padre_id');
    }

    public function reportesPendientes()
    {
        return $this->hasMany(ReportePiezaNoEncajada::class, 'item_no_encajado_id');
    }

    // Scopes
    public function scopeEnStock($query)
    {
        return $query->where('estado', 'en_stock');
    }

    /**
     * Estados en los que un kit está disponible en almacén:
     * - 'en_stock':   kit sellado recibido por recepción.
     * - 'completado': kit armado/completado a mano.
     * Única definición de "kit disponible" — usar este scope en vez de
     * where('estado', 'en_stock') para no olvidar los dos estados.
     */
    public const ESTADOS_KIT_DISPONIBLE = ['en_stock', 'completado'];

    public function scopeKitDisponible($query)
    {
        return $query->whereIn('estado', self::ESTADOS_KIT_DISPONIBLE);
    }

    /** Estados de una pieza dentro de un kit que siguen reteniendo stock suelto. */
    public const ESTADOS_DENTRO_DE_KIT = ['en_stock', 'abierto', 'completado', 'asignado'];

    /** Estados en los que un kit ya se usó: consumido o instalado. */
    public const ESTADOS_KIT_CONSUMIDO = ['consumido', 'instalado'];

    public function scopeKitConsumido($query)
    {
        return $query->whereIn('estado', self::ESTADOS_KIT_CONSUMIDO);
    }

    /** Porcentaje de kits de una sede que ya se usaron. */
    public static function tasaConsumoKits(?int $sedeId = null): array
    {
        $sellados = static::esKit()->kitDisponible()->enSede($sedeId)->count();
        $consumidos = static::esKit()->kitConsumido()->enSede($sedeId)->count();
        $total = $sellados + $consumidos;

        return [
            'porcentaje' => $total > 0 ? round(($consumidos / $total) * 100) : 0,
            'sellados' => $sellados,
            'consumidos' => $consumidos,
        ];
    }

    /** Kits de una sede: sellados, completados y consumidos (gráfico de barras). */
    public static function contarKitsDeSede(int $sedeId): array
    {
        $base = static::esKit()->where('sede_id', $sedeId);

        return [
            'sellados' => (int) (clone $base)->where('estado', 'en_stock')->count(),
            'completados' => (int) (clone $base)->where('estado', 'completado')->count(),
            'consumidos' => (int) (clone $base)->kitConsumido()->count(),
        ];
    }

    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->where('serie', 'like', "%{$search}%");
        }
    }

    /** Restringe a una sede; null = todas. */
    public function scopeEnSede($query, ?int $sedeId)
    {
        return $query->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId));
    }

    /** Filtro por nombre de producto; la serie la busca scopeBuscar. */
    public function scopeBuscarProducto($query, ?string $buscar)
    {
        if (!$buscar) {
            return $query;
        }

        return $query->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$buscar}%"));
    }

    public function scopeSueltos($query)
    {
        return $query->whereNull('kit_padre_id');
    }

    public function scopeEsKit($query)
    {
        return $query->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true));
    }

    public function scopePiezaSerializada($query)
    {
        return $query->whereHas(
            'producto.categoria',
            fn ($q) => $q->where('es_kit', false)->where('es_serializado', true)
        );
    }

    /** Listado de piezas sueltas de una sede. */
    public function scopePiezasSueltasEnSede($query, ?int $sedeId, ?string $buscar = null)
    {
        return $query
            ->with(['producto.categoria', 'sede'])
            ->enStock()
            ->enSede($sedeId)
            ->sueltos()
            ->piezaSerializada()
            ->buscarProducto($buscar)
            ->orderByDesc('created_at');
    }

    /** Kits disponibles de una sede, con receta e hijos ya cargados. */
    public function scopeKitsDisponiblesEn($query, ?int $sedeId, ?string $buscar = null)
    {
        return $query
            ->esKit()
            ->kitDisponible()
            ->enSede($sedeId)
            ->sueltos()
            ->buscarProducto($buscar)
            ->with([
                'producto.categoria',
                'producto.componentes.componente',
                'sede',
                'piezasEnKit' => fn ($q) => $q->enStock(),
            ])
            ->orderByDesc('created_at');
    }

    public function scopeConProductoEn($query, array $nombres)
    {
        if (empty($nombres)) {
            return $query;
        }

        return $query->whereHas('producto', fn ($p) => $p->where(function ($qx) use ($nombres) {
            foreach ($nombres as $indice => $nombre) {
                $condicion = fn ($q) => $q->where('nombre', 'like', "%{$nombre}%");
                $indice === 0 ? $qx->where($condicion) : $qx->orWhere($condicion);
            }
        }));
    }

    public function scopeEnEstado($query, ?string $estado)
    {
        return $query->when($estado, fn ($q) => $q->where('estado', $estado));
    }

    public function scopeDeProducto($query, int $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    public function scopeHijosDe($query, int $kitId)
    {
        return $query->where('kit_padre_id', $kitId);
    }

    /** Kit raíz de una orden de conversión; excluye los hijos del kit. */
    public function scopeKitPadreDe($query, int $serviceOrderId)
    {
        return $query->where('service_order_id', $serviceOrderId)
            ->whereNull('kit_padre_id')
            ->whereHas('piezasEnKit');
    }

    public function scopeComponentesDeKit($query, array $productoIds)
    {
        return $query->where('estado', 'asignado')
            ->whereNotNull('kit_padre_id')
            ->whereIn('producto_id', $productoIds);
    }

    /** Unidades del producto montadas en kits; siguen reteniendo stock suelto. */
    public function scopeEnKitsDe($query, int $productoId, ?int $sedeId = null)
    {
        return $query->where('producto_id', $productoId)
            ->whereNotNull('kit_padre_id')
            ->whereIn('estado', self::ESTADOS_DENTRO_DE_KIT)
            ->where('sede_id', $sedeId);
    }

    /** Unidades del producto montadas en kits; se restan del stock suelto. */
    public static function montadasEnKit(int $productoId, ?int $sedeId = null): int
    {
        return static::enKitsDe($productoId, $sedeId)->count();
    }

    public function scopeDeOrden($query, int $serviceOrderId)
    {
        return $query->where('service_order_id', $serviceOrderId);
    }

    public function scopePorSerie($query, $serie)
    {
        return $query->where('serie', $serie);
    }

    public static function existeSerie($serie, ?int $exceptoItemId = null): bool
    {
        return static::porSerie($serie)
            ->when($exceptoItemId, fn ($q) => $q->where('id', '!=', $exceptoItemId))
            ->exists();
    }

    // Acción: asignar este item a una orden
    public function asignarA(ServiceOrder $orden)
    {
        $this->update([
            'estado' => 'asignado',
            'service_order_id' => $orden->id,
        ]);
    }

    // Acción: liberar el item (devolución)
    public function liberar()
    {
        $this->update([
            'estado' => 'en_stock',
            'service_order_id' => null,
        ]);
    }
}
