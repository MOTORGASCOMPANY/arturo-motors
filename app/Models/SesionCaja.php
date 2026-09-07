<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    // ─── Relaciones ────────────────────────────────────────────────────

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

    // ─── Scopes ────────────────────────────────────────────────────────

    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }

    // ─── Boot: Reglas de integridad ────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        // REGLA 1: Solo UNA sesión puede estar abierta a la vez
        static::creating(function (SesionCaja $sesion) {
            if (static::abierta()->exists()) {
                abort(422, 'Ya existe una caja abierta. Ciérrala antes de abrir una nueva.');
            }
        });

        // REGLA 2: La apertura no puede ser negativa
        static::saving(function (SesionCaja $sesion) {
            if ($sesion->monto_apertura < 0) {
                abort(422, 'El monto de apertura no puede ser negativo.');
            }
        });

        // REGLA 3: Al cerrar, calcular monto_esperado y diferencia AUTOMÁTICAMENTE
        //           El cierre DEBE ser >= 0
        static::saving(function (SesionCaja $sesion) {
            if ($sesion->isDirty('estado') && $sesion->estado === 'cerrada') {
                if ($sesion->monto_cierre === null || $sesion->monto_cierre < 0) {
                    abort(422, 'El monto de cierre debe ser un valor positivo.');
                }

                // Recalcular esperado SIEMPRE desde los movimientos (nunca confiar en valor previo)
                $esperado = self::calcularEsperado($sesion->id);
                $sesion->monto_esperado = $esperado;
                $sesion->diferencia = round($sesion->monto_cierre - $esperado, 2);

                // REGLA 4: Alertar si la diferencia es sospechosamente grande (> S/50)
                if (abs($sesion->diferencia) > 50) {
                    abort(422, 'La diferencia de S/ ' . number_format(abs($sesion->diferencia), 2) .
                        ' es mayor a S/50. Verifica el monto de cierre.');
                }
            }
        });
    }

    // ─── Métodos de cálculo ────────────────────────────────────────────

    /**
     * Calcula el monto esperado: apertura + TODOS los ingresos - TODOS los egresos
     * Incluye efectivo, tarjeta, transferencia, FISE y otros.
     * El efectivo solo se usa para arrastrar el cierre como apertura del día siguiente.
     */
    public static function calcularEsperado(int $sesionId): float
    {
        $sesion = static::findOrFail($sesionId);

        $ingresos = $sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->sum('monto');

        $egresos = $sesion->movimientos()
            ->where('tipo', 'egreso')
            ->sum('monto');

        return round($sesion->monto_apertura + $ingresos - $egresos, 2);
    }

    /**
     * Obtiene el monto esperado en caja (todos los métodos - egresos).
     */
    public function getEfectivoFisicoAttribute(): float
    {
        return self::calcularEsperado($this->id);
    }

    /**
     * Obtiene el cierre de la última sesión cerrada (para encadenar aperturas).
     */
    public static function getLastCierre(): ?float
    {
        $ultima = static::where('estado', 'cerrada')
            ->orderByDesc('cerrada_en')
            ->first();

        return $ultima ? (float) $ultima->monto_cierre : null;
    }

    /**
     * Cierra la sesión con todas las validaciones de integridad.
     */
    public function cerrar(float $montoCierre, int $usuarioId): void
    {
        if ($this->estado !== 'abierta') {
            abort(422, 'Esta sesión de caja ya está cerrada.');
        }

        if ($montoCierre < 0) {
            abort(422, 'El monto de cierre no puede ser negativo.');
        }

        // SIEMPRE recalcular desde movimientos — nunca confiar en valor previo
        $esperado = self::calcularEsperado($this->id);
        $diferencia = round($montoCierre - $esperado, 2);

        if (abs($diferencia) > 50) {
            abort(422, 'La diferencia de S/ ' . number_format(abs($diferencia), 2) .
                ' es mayor a S/50. Verifica el monto de cierre.');
        }

        $this->update([
            'monto_cierre' => $montoCierre,
            'monto_esperado' => $esperado,
            'diferencia' => $diferencia,
            'cerrada_en' => now(),
            'cerrada_por' => $usuarioId,
            'estado' => 'cerrada',
        ]);
    }
}
