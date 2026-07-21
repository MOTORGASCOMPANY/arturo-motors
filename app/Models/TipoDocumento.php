<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $table = 'tipo_documentos';

    protected $fillable = [
        'nombre',
        'requerido',
        'vencible',
    ];

    // Relación: Un tipo de documento puede estar en muchos registros de usuarios
    public function documentos()
    {
        return $this->hasMany(DocumentoUsuario::class, 'tipo_documento_id');
    }
}
