<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesionCaja extends Model
{
    use HasFactory;

    protected $table = 'sesiones_caja';

    protected $fillable = [
        'abierta_por',
        'monto_apertura',
        'abierta_en',
        'cerrada_en',
        'monto_cierre',
        'monto_esperado',
        'diferencia',
        'cerrada_por',
        'estado',
    ];

    protected $casts = [
        'monto_apertura' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
        'monto_esperado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'abierta_en' => 'datetime',
        'cerrada_en' => 'datetime',
    ];

    // Relaciones
    public function abiertaPor()
    {
        return $this->belongsTo(User::class, 'abierta_por');
    }

    public function cerradaPor()
    {
        return $this->belongsTo(User::class, 'cerrada_por');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'sesion_caja_id');
    }

    // Scopes
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }

    /**
     * Cerrar la sesión calculando el esperado SOLO con efectivo.
     * Fórmula: apertura + ingresos_efectivo - egresos
     * (Solo el efectivo está físicamente en el cajón)
     */
    public function cerrar(float $montoCierre, int $usuarioId): void
    {
        // Efectivo recibido a través de la relación indirecta
        $efectivoIngresos = $this->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', function ($q) {
                $q->where('metodo_pago', 'efectivo');
            })
            ->sum('monto');

        $egresos = $this->movimientos()->where('tipo', 'egreso')->sum('monto');
        $esperado = $this->monto_apertura + $efectivoIngresos - $egresos;

        $this->update([
            'monto_cierre' => $montoCierre,
            'monto_esperado' => $esperado,
            'diferencia' => $montoCierre - $esperado,
            'cerrada_en' => now(),
            'cerrada_por' => $usuarioId,
            'estado' => 'cerrada',
        ]);
    }
}
