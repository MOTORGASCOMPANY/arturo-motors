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

    // ─── Propiedades calculadas ────────────────────────────────────────

    public function getTotalIngresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto') : 0;
    }

    private function ingresosPorMetodo(string $metodo): float
    {
        if (!$this->sesion) return 0;

        return $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', $metodo)
            ->sum('monto');
    }

    public function getEfectivoIngresosProperty(): float  { return $this->ingresosPorMetodo('efectivo'); }
    public function getTarjetaIngresosProperty(): float   { return $this->ingresosPorMetodo('tarjeta'); }
    public function getTransferenciaIngresosProperty(): float { return $this->ingresosPorMetodo('transferencia'); }
    public function getOtroIngresosProperty(): float      { return $this->ingresosPorMetodo('otro'); }
    public function getFiseIngresosProperty(): float      { return $this->ingresosPorMetodo('fise'); }

    public function getTotalEgresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto') : 0;
    }

    /**
     * Monto esperado: apertura + TODOS los ingresos - TODOS los egresos.
     * Incluye efectivo, tarjeta, transferencia, FISE y otros.
     */
    public function getMontoEsperadoProperty()
    {
        if (!$this->sesion) return 0;
        return round($this->sesion->monto_apertura + $this->totalIngresos - $this->totalEgresos, 2);
    }

    public function getDiferenciaProperty()
    {
        return round($this->montoCierre - $this->montoEsperado, 2);
    }

    // ─── Cerrar sesión ─────────────────────────────────────────────────

    public function cerrar()
    {
        if (!$this->sesion) {
            $this->addError('general', 'No hay una caja abierta.');
            return;
        }

        // REGLA 1: Validar formato del monto
        $this->validate(
            ['montoCierre' => 'required|numeric|min:0'],
            [
                'montoCierre.required' => 'El monto real en caja es obligatorio.',
                'montoCierre.numeric' => 'Debe ingresar un valor numérico válido.',
                'montoCierre.min' => 'El monto no puede ser un valor negativo.',
            ]
        );

        // REGLA 2: Alertar si la diferencia es sospechosa (> S/10)
        $diferencia = $this->diferencia;
        if (abs($diferencia) > 10) {
            $this->dispatch('minAlert',
                titulo: '¡Atención!',
                mensaje: 'La diferencia es S/ ' . number_format(abs($diferencia), 2) .
                    ($diferencia > 0 ? ' (sobra)' : ' (falta)') .
                    '. ¿Estás seguro de que el monto es correcto?',
                icono: 'warning'
            );
            return;
        }

        // REGLA 3: Cerrar sesión (el modelo recalcula monto_esperado y diferencia automáticamente)
        $this->sesion->cerrar((float) $this->montoCierre, Auth::id());

        $this->dispatch('minToast',
            titulo: '¡Caja Cerrada!',
            mensaje: 'Sesión cerrada con S/ ' . number_format($this->montoCierre, 2) .
                ($diferencia != 0 ? ' | Diferencia: S/ ' . number_format($diferencia, 2) : ' | Cuadrada ✓'),
            icono: 'success'
        );

        $this->sesion = null;
        $this->montoCierre = 0;
    }

    public function render()
    {
        return view('livewire.caja.cerrar-caja');
    }
}
