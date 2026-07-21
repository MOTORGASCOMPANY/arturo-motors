<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsesorExterno extends Model
{
    use HasFactory;

    protected $table = 'asesores_externos';

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
    ];

    // Relaciones
    public function citas()
    {
        return $this->hasMany(Cita::class, 'asesor_externo_id');
    }
}
