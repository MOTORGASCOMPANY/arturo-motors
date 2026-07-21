<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    use HasFactory;

    protected $table = 'conversiones';

    protected $fillable = [
        'expediente_id',
        'tecnico_id',
        'fecha_inicio',
        'fecha_fin',
        'estado', // 'en_proceso', 'completado', 'certificado'
        'observaciones',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    /**
     * Define la relación de uno a muchos con ConversionDetalle.
     * Una conversión tiene muchos detalles (repuestos y cantidades).
     */
    public function conversionDetalles()
    {
        return $this->hasMany(ConversionDetalle::class, 'conversion_id');
    }


    /*public function controlCalidad()
    {
        return $this->hasMany(ControlCalidad::class, 'conversion_id');
    }*/

    /*public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }*/

    // Scope para filtros y orden
    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->whereHas('expediente.cliente', function ($q) use ($search) {
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
        if ($estado) {
            return $query->where('estado', $estado);
        }
        return $query;
    }
}
