<?php

namespace App\Livewire\Fise;

use App\Models\SesionCaja;
use App\Models\FisePago;
use Livewire\Component;

class Detalle extends Component
{
    public ?SesionCaja $sesion = null;
    public $movimientos;
    public $fisePagos;
    public array $resumen = [];
    public array $caja = [];

    public function mount(SesionCaja $sesion): void
    {
        $this->sesion = $sesion;
        $this->cargarDatos();
    }

    private function cargarDatos(): void
    {
        // Cargar SOLO movimientos FISE
        $this->movimientos = $this->sesion->movimientos()
            ->where('metodo_pago', 'fise')
            ->with(['serviceOrder.vehiculo', 'serviceOrder.cliente', 'usuario'])
            ->orderBy('created_at')
            ->get();

        // Estados de cobro FISE
        $serviceOrderIds = $this->movimientos->pluck('service_order_id')->filter()->unique();

        $this->fisePagos = FisePago::whereIn('service_order_id', $serviceOrderIds)
            ->get()
            ->keyBy('service_order_id');

        // Resumen FISE
        $this->resumen = [
            'total_fise'       => $this->movimientos->sum('monto'),
            'cobrado'          => $this->fisePagos->where('estado', 'pagado')->sum('monto_pagado'),
            'pendiente'        => $this->fisePagos->where('estado', 'pendiente')->sum('monto_total'),
            'cantidad'         => $this->movimientos->count(),
            'cantidad_cobrada' => $this->fisePagos->where('estado', 'pagado')->count(),
            'cantidad_pendiente' => $this->fisePagos->where('estado', 'pendiente')->count(),
        ];

        // Resumen de caja completo (incluye FISE)
        $totalIngresos = $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto');
        $cajaConvencional = $this->sesion->movimientos()->where('tipo', 'ingreso')->where('metodo_pago', '!=', 'fise')->sum('monto');

        $this->caja = [
            'apertura'          => $this->sesion->monto_apertura,
            'ingresos'          => $totalIngresos,
            'egresos'           => $totalEgresos,
            'monto_esperado'    => $this->sesion->monto_esperado,
            'monto_cierre'      => $this->sesion->monto_cierre,
            'diferencia'        => $this->sesion->diferencia,
            'caja_convencional' => $cajaConvencional,
        ];
    }

    public function render()
    {
        return view('livewire.fise.detalle');
    }
}
