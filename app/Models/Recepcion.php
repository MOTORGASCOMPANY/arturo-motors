<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recepcion extends Model
{
     use HasFactory;

    protected $table = 'recepciones';

    protected $fillable = [
        'vehiculo_id',
        'asesor_id',
        'fecha_recepcion',
        'observaciones',
        'estado',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'recepcion_id');
    }
}
