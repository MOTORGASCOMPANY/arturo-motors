<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'nombre',
        'tipo',
        'precio_base',
        'activo',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class, 'service_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeTipo($query, $tipo)
    {
        if ($tipo && $tipo !== 'todos') {
            $query->where('tipo', $tipo);
        }
    }
}
