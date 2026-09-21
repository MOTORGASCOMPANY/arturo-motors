<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportePiezaNoEncajada extends Model
{
    use HasFactory;

    protected $table = 'reporte_piezas_no_encajadas';

    protected $fillable = [
        'service_order_id',
        'item_no_encajado_id',
        'tecnico_id',
        'motivo_no_encaja',
        'estado',
        'item_nuevo_id',
        'almacen_user_id',
        'observaciones_almacen',
    ];

    // ═══════════════════════════════════════════════
    // RELACIONES
    // ═══════════════════════════════════════════════

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class, 'service_order_id');
    }

    public function itemNoEncajado()
    {
        return $this->belongsTo(ItemSerializado::class, 'item_no_encajado_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function itemNuevo()
    {
        return $this->belongsTo(ItemSerializado::class, 'item_nuevo_id');
    }

    public function almacenUser()
    {
        return $this->belongsTo(User::class, 'almacen_user_id');
    }

    // ═══════════════════════════════════════════════
    // SCOPES
    // ═══════════════════════════════════════════════

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeSolicitandoAlmacen($query)
    {
        return $query->where('estado', 'solicitando_almacen');
    }

    public function scopeParaAlmacen($query)
    {
        return $query->whereIn('estado', ['pendiente', 'solicitando_almacen']);
    }

    // ═══════════════════════════════════════════════
    // ACCIONES
    // ═══════════════════════════════════════════════

    public function marcarBuscando(): self
    {
        $this->update(['estado' => 'buscando_pieza']);
        return $this;
    }

    public function marcarPiezaEncontrada(int $itemNuevoId): self
    {
        $this->update([
            'estado' => 'pieza_encontrada',
            'item_nuevo_id' => $itemNuevoId,
        ]);
        return $this;
    }

    public function solicitarAlmacen(): self
    {
        $this->update(['estado' => 'solicitando_almacen']);
        return $this;
    }

    public function asignarAlmacen(int $userId): self
    {
        $this->update(['almacen_user_id' => $userId]);
        return $this;
    }

    public function kitAbierto(int $itemNuevoId, ?string $observaciones = null): self
    {
        $this->update([
            'estado' => 'kit_abierto',
            'item_nuevo_id' => $itemNuevoId,
            'observaciones_almacen' => $observaciones,
        ]);
        return $this;
    }

    public function resolver(): self
    {
        $this->update(['estado' => 'resuelto']);
        return $this;
    }

    public function cancelar(?string $motivo = null): self
    {
        $this->update([
            'estado' => 'cancelado',
            'observaciones_almacen' => $motivo,
        ]);
        return $this;
    }
}
