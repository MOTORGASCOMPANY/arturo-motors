<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    use HasFactory;

    protected $table = 'planillas';

    protected $fillable = [
        'contrato_id',
        'periodo',

        'sueldo_base',
        'asignacion_familiar',
        'horas_extras',
        'movilidad',
        'otros_ingresos',
        'otros_descuentos',
        /* 'total_pagado', ELIMINADO del fillable porque es
        VIRTUAL GENERATED ALWAYS AS ( sueldo_base + asignacion_familiar + horas_extras + movilidad + otros_ingresos - otros_descuentos ) VIRTUAL, */

        /*pago_banco, `sueldo_base` + `asignacion_familiar` + `horas_extras` + `otros_ingresos` - `otros_descuentos` */
        /*pago_efectivo, movilidad */
        'planilla',
        'estado_pago',
        'fecha_pago',
        'numero_cuenta',
        'observacion',
    ];

    protected $appends = ['total_calculado'];

    protected $casts = [
        'periodo' => 'date',
        'fecha_pago' => 'date',
        'estado_pago' => 'boolean',
    ];

    public function getTotalCalculadoAttribute()
    {
        return (
            ($this->sueldo_base ?? 0) +
            ($this->asignacion_familiar ?? 0) +
            ($this->horas_extras ?? 0) +
            ($this->movilidad ?? 0) +
            ($this->otros_ingresos ?? 0)
        ) - ($this->otros_descuentos ?? 0);
    }

    // Relación inversa con Contrato
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    // Relación con los archivos de la planilla
    public function archivos()
    {
        return $this->hasMany(PlanillaArchivo::class, 'planilla_id');
    }
}
