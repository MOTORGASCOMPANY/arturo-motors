<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $table = 'traslados';

    protected $fillable = ['sede_destino_id', 'enviado_por', 'observaciones', 'es_kit_completo', 'componentes_faltantes'];

    protected $casts = [
        'es_kit_completo' => 'boolean',
        'componentes_faltantes' => 'array',
    ];

    public function sedeDestino()
    {
        return $this->belongsTo(Sede::class, 'sede_destino_id');
    }

    public function enviadoPor()
    {
        return $this->belongsTo(User::class, 'enviado_por');
    }

    public function detalles()
    {
        return $this->hasMany(TrasladoDetalle::class, 'traslado_id');
    }
}
