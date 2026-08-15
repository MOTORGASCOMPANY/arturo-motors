<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'cliente_id',
        'placa',
        'marca',
        'modelo',
        'anio',
        'combustible',
        'serie',
        'color',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     *  hasMany asume que un vehiculo puede tener múltiples registros a lo largo del tiempo.
     *  hasOne asume que es un evento único y final que se aplica a un vehiculo.
     */

    // Si realmente solo hay un expediente por vehículo (relación 1 a 1), cambia la relación en el modelo:
    /*public function expediente()
    {
        return $this->hasOne(Expediente::class, 'vehiculo_id');
    }*/
    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class, 'vehiculo_id');
    }

    /*public function fise()
    {
        return $this->hasMany(FiseSolicitud::class, 'vehiculo_id');
    }*/

    public function cita()
    {
        return $this->hasMany(Cita::class, 'vehiculo_id');
    }

    // Scope para filtros y orden
    public function scopeBuscar($query, $search)
    {
        if ($search) {
            $query->where('placa', 'like', "%{$search}%")
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

    /*public function getRutaVistaCertificadoAttribute()
    {
        $ruta = null;
        switch ($this->Servicio->tipoServicio->id) {
            case 1: //tipo servicio = inicial gnv
                $ruta = route('certificadoInicialGnv', ['id' => $this->attributes['id']]);
                break;

            default:
                $ruta = null;
                break;
        }

        return $ruta;
    }*/
}
