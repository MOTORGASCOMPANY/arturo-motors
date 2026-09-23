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

    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->where('serie', 'like', "%{$search}%");
        }
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
