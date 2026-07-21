<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    use HasFactory;

    protected $table = 'expedientes';

    protected $fillable = [
        'cita_id',
        'cliente_id',
        'vehiculo_id',
        'jefe_taller_id', // solo hay un jefe evaluar campo 
        'tecnico_id',
        'estado', // en_evaluacion', 'evaluacion_rechazada', 'aprobado_conversion', 'en_conversion', 'conversion_completada', 'en_control_calidad', 'listo_para_entrega', 'entregado', 'cancelado'
    ];

    // Relaciones

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function jefeTaller()
    {
        return $this->belongsTo(User::class, 'jefe_taller_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
    
    /**
     *  hasMany asume que un expediente puede tener múltiples registros a lo largo del tiempo.
     *  hasOne asume que es un evento único y final que se aplica a un expediente.
     */

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'expediente_id');
    }

    public function conversiones()
    {
        return $this->hasMany(Conversion::class, 'expediente_id');
    }

    public function controlesCalidad()
    {
        return $this->hasOne(ControlCalidad::class, 'expediente_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentosExpediente::class, 'expediente_id');
    }

    // Scope para filtros y orden
    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->whereHas('cliente', function ($q) use ($search) {
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
