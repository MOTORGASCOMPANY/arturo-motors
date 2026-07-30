<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSerializado extends Model
{
    use HasFactory;

    protected $table = 'items_serializados';

    protected $fillable = [
        'producto_id',
        'serie',
        'atributos',
        'estado',
        'service_order_id',
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

    // Scopes
    public function scopeEnStock($query)
    {
        return $query->where('estado', 'en_stock');
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
