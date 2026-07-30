<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $table = 'service_orders';

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'service_id',
        'cita_id',
        'estado',
        'precio_lista',
        'precio_final',
        'descuento_motivo',
        'descuento_autorizado_por',
        'checklist_evaluacion',
        'evaluacion_aprobada',
        'evaluacion_observaciones',
        'evaluado_por',
        'evaluado_en',
        'tecnico_id',
        'fecha_inicio_conversion',
        'fecha_fin_conversion',
        'creado_por',
    ];

    protected $casts = [
        'precio_lista' => 'decimal:2',
        'precio_final' => 'decimal:2',
        'checklist_evaluacion' => 'array',
        'evaluacion_aprobada' => 'boolean',
        'evaluado_en' => 'datetime',
        'fecha_inicio_conversion' => 'datetime',
        'fecha_fin_conversion' => 'datetime',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function evaluadoPor()
    {
        return $this->belongsTo(User::class, 'evaluado_por');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function items()
    {
        return $this->hasMany(ItemSerializado::class, 'service_order_id');
    }

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'service_order_id');
    }

    public function comprobante()
    {
        return $this->hasOne(Comprobante::class, 'service_order_id');
    }

    public function historialEstados()
    {
        return $this->hasMany(ServiceOrderStatusHistory::class, 'service_order_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'service_order_id');
    }

    // Scopes
    public function scopeTipoConversion($query)
    {
        return $query->whereHas('service', fn ($q) => $q->where('tipo', 'conversion'));
    }

    public function scopeEstado($query, $estado)
    {
        if ($estado && $estado !== 'todos') {
            $query->where('estado', $estado);
        }
    }
}
