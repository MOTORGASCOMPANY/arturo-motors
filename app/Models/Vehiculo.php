<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'anio',
        'combustible',
        'serie',
        'color',
    ];

    // Todos los propietarios/asociados
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_vehiculo')
                    ->using(ClienteVehiculo::class)
                    ->withPivot('es_principal', 'relacion')
                    ->withTimestamps();
    }

    // Obtener el propietario principal
    public function clientePrincipal()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_vehiculo')
                    ->using(ClienteVehiculo::class)
                    ->wherePivot('es_principal', true)
                    ->withPivot('relacion')
                    ->limit(1);
    }

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class, 'vehiculo_id');
    }
    
    public function cita()
    {
        return $this->hasMany(Cita::class, 'vehiculo_id');
    }

    // Scope para filtros y orden
    public function scopeBuscar($query, $search)
    {
        if (!empty($search)) {
            $query->where('placa', 'like', "%{$search}%")
                ->orWhereHas('clientes', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('razon_social', 'like', "%{$search}%")
                      ->orWhere('documento', 'like', "%{$search}%");
                });
        }
    }

    public function scopeOrdenar($query, $sort, $direction)
    {
        return $query->orderBy($sort, $direction);
    }

}
