<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    protected $table = 'sedes';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'estado', // 1 o true = Activa, 0 o false = Inactiva
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // Relación: Una sede tiene muchas citas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'sede_id');
    }

    // Scope para filtrar solo sedes activas
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /** Id de la primera sede activa; fallback por defecto. */
    public static function primeraActivaId(): int
    {
        return (int) (static::activas()->orderBy('id')->value('id') ?? 1);
    }

    /** Sedes activas distintas de una (destinos de traslado). */
    public function scopeExcepto($query, int $sedeId)
    {
        return $query->activas()->where('id', '!=', $sedeId)->orderBy('nombre');
    }
}
