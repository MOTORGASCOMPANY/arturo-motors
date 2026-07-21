<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiseSolicitud extends Model
{
    use HasFactory;

    protected $table = 'fise_solicitudes';

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'fecha_solicitud', //evaluar si es necesario
        'fecha_respuesta',  //evaluar si es necesario
        'estado', //enum('pendiente', 'aprobado', 'rechazado')
        'observaciones',
        // deberia haber un campo para relacion con cita ?
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_respuesta' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }
}
