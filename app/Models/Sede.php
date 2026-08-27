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
}
