<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    use HasFactory;

    // Especificamos la tabla ya que no es plural en inglés (vacations)
    protected $table = 'vacaciones';

    protected $fillable = [
        'idContrato',
        'dias_ganados',
        'dias_tomados',
        'dias_restantes'
    ];

    // Relación inversa: Una vacación pertenece a un contrato
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'idContrato');
    }

    // Relación: Una vacación tiene muchos detalles/asignaciones
    public function asignaciones()
    {
        return $this->hasMany(VacacionAsignada::class, 'idVacacion');
    }
}
