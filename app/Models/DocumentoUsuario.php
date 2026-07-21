<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoUsuario extends Model
{
    use HasFactory;

    protected $table = 'documentos_usuarios';

    protected $fillable = [
        'user_id',
        'tipo_documento_id',
        'nombre',
        'ruta',
        'extension',
        'fecha_emision',
        'fecha_expiracion',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_expiracion' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }
}
