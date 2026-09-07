<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FisePago extends Model
{
    protected $fillable = [
        'service_order_id',
        'monto_total',
        'monto_pagado',
        'fecha_pago',
        'pagado_por',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'monto_total'  => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'fecha_pago'   => 'date',
    ];

    // Relaciones
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function pagadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pagado_por');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeParciales($query)
    {
        return $query->where('estado', 'parcial');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }
}
