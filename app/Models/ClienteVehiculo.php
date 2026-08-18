<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClienteVehiculo extends Pivot
{
    protected $table = 'cliente_vehiculo';

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'es_principal',
        'relacion',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    // Relaciones si necesitas consultar directamente la tabla intermedia
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }
}
