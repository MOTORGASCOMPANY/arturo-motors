<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'service_order_status_history';

    protected $fillable = [
        'service_order_id',
        'estado_anterior',
        'estado_nuevo',
        'usuario_id',
    ];

    // Relaciones
    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Registrar un cambio de estado
    public static function registrar(ServiceOrder $orden, ?string $estadoAnterior, string $estadoNuevo, ?int $usuarioId): self
    {
        return static::create([
            'service_order_id' => $orden->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'usuario_id' => $usuarioId,
        ]);
    }
}
