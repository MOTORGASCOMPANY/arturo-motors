<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'detalles_evaluacion';

    protected $fillable = [
        'evaluacion_id',
        'tarjeta_propiedad',
        'soat',
        'llave_contacto',
        'espejos',
        'antena',
        'plumillas',
        'vasos',
        'emblemas',
        'tapa_combustible',
        'bateria',
        'seguro_bateria',
        'claxon',
        'tapa_aceite',
        'tapa_radiador',
        'barita_capot',
        'espejo_anterior',
        'tapasoles',
        'radio',
        'reproductor_cd',
        'parlantes',
        'cenicero',
        'encendedor',
        'pisos',
        'fundas_forros',
        'cinturones',
        'llanta_repuesto',
        'gata_palanca',
        'llave_ruedas',
        'triangulo',
        'extintor',
        'linterna',
        'herramientas',
        'botiquin',        
    ];
}
