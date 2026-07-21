<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlCalidad extends Model
{
    use HasFactory;

    protected $table = 'control_calidad';

    protected $fillable = [
        'expediente_id',
        'jefe_taller_id',
        'fecha_control',
        'resultado', // 'aprobado', 'rechazado'
        'observaciones',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function jefeTaller()
    {
        return $this->belongsTo(User::class, 'jefe_taller_id');
    }

    /*public function conversion()
    {
        return $this->belongsTo(Conversion::class, 'conversion_id');
    }*/
}
