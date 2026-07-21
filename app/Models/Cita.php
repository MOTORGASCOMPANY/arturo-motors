<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'asesor_id',
        'asesor_externo_id', // agregado
        'fecha_cita',
        'motivo',
        'estado', //enum('pendiente', 'aceptada', 'rechazada', 'cancelada')
    ];

    protected $casts = [
        'fecha_cita' => 'datetime',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function asesorExterno()
    {
        return $this->belongsTo(AsesorExterno::class, 'asesor_externo_id');
    }

    public function expediente()
    {
        return $this->hasOne(Expediente::class, 'cita_id');
    }


    public function getNombreAsesorAttribute()
    {
        if ($this->asesor_externo_id) {
            return $this->asesorExterno?->nombre;
        }

        return $this->asesor?->name; // Asumiendo que en User el campo es "name"
    }



    // Accesores

    // Scope para filtros y orden
    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->where('motivo', 'like', "%{$search}%")
                ->orWhereHas('cliente', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('documento', 'like', "%{$search}%");
                });
        }
    }

    public function scopeOrdenar($query, $sort, $direction)
    {
        return $query->orderBy($sort, $direction);
    }

    public function scopeEstado($query, $estado)
    {
        if ($estado && $estado !== 'todos') {
            $query->where('estado', $estado);
        }
    }
}
