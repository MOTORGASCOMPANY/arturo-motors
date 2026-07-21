<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'expediente_id',
        'tecnico_id',
        'fecha_evaluacion',
        'resultado', // 'apto', 'no apto'
        'observaciones',
    ];

    protected $casts = [
        'fecha_evaluacion' => 'date',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function detalles()
    {
        return $this->hasOne(DetalleEvaluacion::class, 'evaluacion_id');
    }

    /*public function conversiones()
    {
        return $this->hasMany(Conversion::class, 'evaluacion_id');
    }*/
}
