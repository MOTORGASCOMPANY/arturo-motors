<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'comprobantes';

    protected $fillable = [
        'service_order_id',
        'folio',
        'monto',
        'metodo_pago',
        'emitido_por',
        'pdf_path',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    // Relaciones
    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function emitidoPor()
    {
        return $this->belongsTo(User::class, 'emitido_por');
    }

    // Generar el siguiente folio correlativo, ej: 2026-00001
    public static function generarFolio(): string
    {
        $anio = now()->year;
        $ultimo = static::where('folio', 'like', "{$anio}-%")
            ->orderByDesc('id')
            ->first();

        $siguiente = $ultimo
            ? ((int) substr($ultimo->folio, 5)) + 1
            : 1;

        return sprintf('%d-%05d', $anio, $siguiente);
    }
}
