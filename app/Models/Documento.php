<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'service_order_id',
        'tipo',
        'path',
        'nombre_original',
        'subido_por',
    ];

    // Relaciones
    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    // Accesor: URL pública para mostrar/descargar el archivo
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    // Scopes
    public function scopeTipo($query, $tipo)
    {
        if ($tipo) {
            $query->where('tipo', $tipo);
        }
    }
}
