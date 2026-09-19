<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitPiezaExtraida extends Model
{
    use HasFactory;

    protected $table = 'kit_piezas_extraidas';

    protected $fillable = [
        'item_serializado_id',
        'producto_componente_id',
        'cantidad_extraida',
        'service_order_id',
        'extraida_por',
        'extraida_en',
    ];

    protected $casts = [
        'extraida_en' => 'datetime',
        'cantidad_extraida' => 'integer',
    ];

    // Relaciones
    public function kit()
    {
        return $this->belongsTo(ItemSerializado::class, 'item_serializado_id');
    }

    public function productoComponente()
    {
        return $this->belongsTo(Producto::class, 'producto_componente_id');
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'extraida_por');
    }

    // Scopes
    public function scopeDeKit($query, int $kitId)
    {
        return $query->where('item_serializado_id', $kitId);
    }

    public function scopeParaOrden($query, int $serviceOrderId)
    {
        return $query->where('service_order_id', $serviceOrderId);
    }
}
