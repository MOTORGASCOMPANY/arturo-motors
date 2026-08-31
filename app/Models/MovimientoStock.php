<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    use HasFactory;

    protected $table = 'movimientos_stock';

    protected $fillable = [
        'producto_id',
        'sede_id',
        'tipo',
        'cantidad',
        'service_order_id',
        'motivo',
        'usuario_id',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    // Scopes
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo', 'salida');
    }

    // Registrar movimiento y actualizar el stock del producto en un solo paso
    public static function registrar(Producto $producto, string $tipo, int $cantidad, ?int $serviceOrderId, int $usuarioId, ?string $motivo = null, int $sedeId = 1): self
    {
        $movimiento = static::create([
            'producto_id' => $producto->id,
            'sede_id' => $sedeId,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'service_order_id' => $serviceOrderId,
            'motivo' => $motivo,
            'usuario_id' => $usuarioId,
        ]);

        $stockSede = ProductoStockSede::firstOrCreate(
            ['producto_id' => $producto->id, 'sede_id' => $sedeId],
            ['cantidad' => 0]
        );

        //$producto->increment('stock', $tipo === 'entrada' ? $cantidad : -$cantidad);
        $stockSede->increment('cantidad', $tipo === 'entrada' ? $cantidad : -$cantidad);

        return $movimiento;
    }
}
