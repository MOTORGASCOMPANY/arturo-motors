<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversionDetalle extends Model
{
    use HasFactory;

    protected $table = 'conversion_detalles';

    protected $fillable = [
        'conversion_id',
        'repuesto_id',
        'cantidad_utilizada',
    ];

    /**
     * Define la relación inversa con Conversion.
     * Un detalle pertenece a una única conversión.
     */
    public function conversion()
    {
        return $this->belongsTo(Conversion::class, 'conversion_id');
    }

    /**
     * Define la relación inversa con Repuesto.
     * Un detalle pertenece a un único repuesto.
     */
    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'repuesto_id');
    }
}
