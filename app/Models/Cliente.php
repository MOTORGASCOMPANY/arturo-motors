<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'tipo_persona',
        'nombre',
        'apellido',
        'razon_social',
        'documento',
        'telefono',
        'email',
        'direccion',
    ];

    public function vehiculos()
    {
        return $this->belongsToMany(Vehiculo::class, 'cliente_vehiculo')
                    ->using(ClienteVehiculo::class)
                    ->withPivot('es_principal', 'relacion')
                    ->withTimestamps();
    }

    // Nombre amigable para mostrar en vistas o selectores
    public function getNombreCompletoAttribute(): string
    {
        if ($this->tipo_persona === 'JURIDICA') {
            return $this->razon_social ?? '';
        }

        return trim("{$this->nombre} {$this->apellido}");
    }

    // Scope para filtros y orden
    public function scopeBuscar($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
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
