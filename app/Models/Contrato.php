<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos';

    protected $fillable = [
        'user_id',
        'fecha_ingreso',
        'fecha_inicio_contrato',
        'fecha_vencimiento',
        'cargo',
        'tipo_contrato', // Plazo Fijo, Indeterminado, Temporal, Por Locación
        'sueldo_bruto',
        'sueldo_neto',
        'status', // enum('Activo', 'Vencido', 'Finalizado')
        'contrato_path'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_inicio_contrato' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vacaciones() {
        return $this->hasOne(Vacacion::class, 'idContrato');
    }

    public function planillas()
    {
        return $this->hasMany(Planilla::class, 'contrato_id');
    }
}
