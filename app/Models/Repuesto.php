<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    use HasFactory;

    protected $table = 'repuestos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
    ];

    /**
     * Define la relación de uno a muchos con ConversionDetalle.
     * Un repuesto puede estar en muchos detalles de conversión.
     */
    public function conversionDetalles()
    {
        return $this->hasMany(ConversionDetalle::class, 'repuesto_id');
    }
}
