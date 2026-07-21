<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentosExpediente extends Model
{
    use HasFactory;

    protected $table = 'documentos_expediente';

    protected $fillable = [
        'expediente_id',
        'tipo_documento_id',
        'nombre',
        'ruta',
        'extension',
    ];

    // Relaciones
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TiposDocumento::class, 'tipo_documento_id');
    }
}
