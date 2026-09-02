<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CerrarCaja extends Component
{
    public $montoCierre = 0;
    public ?SesionCaja $sesion = null;

    public function mount()
    {
        $this->cargarSesion();
    }

    public function cargarSesion()
    {
        $this->sesion = SesionCaja::abierta()->orderByDesc('abierta_en')->first();
    }

    /**
     * Total de todos los ingresos (todos los métodos)
     */
    public function getTotalIngresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto') : 0;
    }

    /**
     * Ingresos SOLO en efectivo (a través de la relación indirecta)
     */
    public function getEfectivoIngresosProperty()
    {
        if (!$this->sesion) return 0;

        return $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', function ($q) {
                $q->where('metodo_pago', 'efectivo');
            })
            ->sum('monto');
    }

    /**
     * Ingresos por tarjeta
     */
    public function getTarjetaIngresosProperty()
    {
        if (!$this->sesion) return 0;

        return $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', function ($q) {
                $q->where('metodo_pago', 'tarjeta');
            })
            ->sum('monto');
    }

    /**
     * Ingresos por transferencia
     */
    public function getTransferenciaIngresosProperty()
    {
        if (!$this->sesion) return 0;

        return $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', function ($q) {
                $q->where('metodo_pago', 'transferencia');
            })
            ->sum('monto');
    }

    /**
     * Ingresos por otro método
     */
    public function getOtroIngresosProperty()
    {
        if (!$this->sesion) return 0;

        return $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', function ($q) {
                $q->where('metodo_pago', 'otro');
            })
            ->sum('monto');
    }

    public function getTotalEgresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto') : 0;
    }

    /**
     * Monto esperado SOLO en efectivo:
     * apertura + ingresos_efectivo - egresos
     * (Los egresos siempre salen de la caja física)
     */
    public function getMontoEsperadoProperty()
    {
        if (!$this->sesion) return 0;
        return $this->sesion->monto_apertura + $this->efectivoIngresos - $this->totalEgresos;
    }

    /**
     * Monto esperado total (todos los métodos) — para referencia
     */
    public function getMontoEsperadoTotalProperty()
    {
        if (!$this->sesion) return 0;
        return $this->sesion->monto_apertura + $this->totalIngresos - $this->totalEgresos;
    }

    public function cerrar()
    {
        if (!$this->sesion) {
            $this->addError('general', 'No hay una caja abierta.');
            return;
        }

        $this->validate(
            ['montoCierre' => 'required|numeric|min:0'],
            [
                'montoCierre.required' => 'El monto real en caja es obligatorio.',
                'montoCierre.numeric' => 'Debe ingresar un valor numérico válido.',
                'montoCierre.min' => 'El monto no puede ser un valor negativo.',
            ]
        );

        $this->sesion->cerrar((float) $this->montoCierre, Auth::id());

        $this->dispatch('minToast', titulo: '¡Caja Cerrada!', mensaje: 'Sesión cerrada con S/ ' . number_format($this->montoCierre, 2), icono: 'success');

        $this->sesion = null;
        $this->montoCierre = 0;
    }

    public function render()
    {
        return view('livewire.caja.cerrar-caja');
    }
}
