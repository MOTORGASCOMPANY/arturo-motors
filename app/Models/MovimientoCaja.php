<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    // Métodos de pago permitidos (debe coincidir con el ENUM de la DB)
    const METODOS_PAGO = ['efectivo', 'tarjeta', 'transferencia', 'fise', 'otro'];

    // Solo estos métodos afectan el efectivo físico en caja
    const METODOS_EFECTIVO = ['efectivo'];

    protected $fillable = [
        'sesion_caja_id',
        'tipo',        // ingreso | egreso
        'monto',
        'concepto',
        'service_order_id',
        'usuario_id',
        'metodo_pago', // efectivo | tarjeta | transferencia | fise | otro (solo ingresos)
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    // ─── Relaciones ────────────────────────────────────────────────────

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ─── Scopes ────────────────────────────────────────────────────────

    public function scopeIngresos($query)
    {
        return $query->where('tipo', 'ingreso');
    }

    public function scopeEgresos($query)
    {
        return $query->where('tipo', 'egreso');
    }

    public function scopeEfectivo($query)
    {
        return $query->where('metodo_pago', 'efectivo');
    }

    // ─── Boot: Reglas de integridad ────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        // REGLA 1: El monto debe ser siempre mayor a 0
        static::saving(function (MovimientoCaja $movimiento) {
            if ($movimiento->monto <= 0) {
                abort(422, 'El monto del movimiento debe ser mayor a cero.');
            }
        });

        // REGLA 2: Ingresos DEBEN tener metodo_pago válido
        static::saving(function (MovimientoCaja $movimiento) {
            if ($movimiento->tipo === 'ingreso') {
                if (empty($movimiento->metodo_pago) || !in_array($movimiento->metodo_pago, self::METODOS_PAGO)) {
                    abort(422, 'Los ingresos deben tener un método de pago válido: ' .
                        implode(', ', self::METODOS_PAGO));
                }
            }
        });

        // REGLA 3: Egresos NO deben tener metodo_pago
        static::saving(function (MovimientoCaja $movimiento) {
            if ($movimiento->tipo === 'egreso' && !empty($movimiento->metodo_pago)) {
                abort(422, 'Los egresos no deben tener método de pago.');
            }
        });

        // REGLA 4: La sesión de caja DEBE estar abierta
        static::saving(function (MovimientoCaja $movimiento) {
            if ($movimiento->isDirty('sesion_caja_id') || $movimiento->exists === false) {
                $sesion = SesionCaja::find($movimiento->sesion_caja_id);
                if (!$sesion || $sesion->estado !== 'abierta') {
                    abort(422, 'No se pueden registrar movimientos en una sesión cerrada.');
                }
            }
        });
    }

    // ─── Helpers ────────────────────────────────────────────────────────

    /**
     * Indica si este movimiento afecta el efectivo físico en caja.
     */
    public function afectaEfectivo(): bool
    {
        return $this->tipo === 'ingreso' && in_array($this->metodo_pago, self::METODOS_EFECTIVO);
    }

    /**
     * Retorna los métodos de pago válidos.
     */
    public static function metodosPagoPermitidos(): array
    {
        return self::METODOS_PAGO;
    }
}
