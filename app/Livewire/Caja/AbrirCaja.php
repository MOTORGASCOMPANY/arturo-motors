<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AbrirCaja extends Component
{
    public $montoApertura = 0;
    public bool $cuadrar = false;
    public ?float $efectivoAnterior = null;

    public function mount(): void
    {
        $this->cargarEfectivoAnterior();
    }

    /**
     * Calcula el efectivo que debería haber en caja usando la fórmula:
     * apertura + ingresos_efectivo - egresos
     *
     * NO depende de monto_cierre (que puede estar mal ingresado).
     */
    public function cargarEfectivoAnterior(): void
    {
        $ultimaSesion = SesionCaja::where('estado', 'cerrada')
            ->with(['movimientos' => function ($q) {
                $q->with('serviceOrder.comprobante');
            }])
            ->orderByDesc('cerrada_en')
            ->first();

        if (!$ultimaSesion) {
            $this->efectivoAnterior = null;
            $this->montoApertura = 0;
            return;
        }

        // Efectivo recibido (a través de la relación indirecta)
        $efectivoIngresos = $ultimaSesion->movimientos
            ->where('tipo', 'ingreso')
            ->filter(function ($m) {
                return $m->serviceOrder
                    && $m->serviceOrder->comprobante
                    && $m->serviceOrder->comprobante->metodo_pago === 'efectivo';
            })
            ->sum('monto');

        $egresos = $ultimaSesion->movimientos->where('tipo', 'egreso')->sum('monto');

        $this->efectivoAnterior = (float) $ultimaSesion->monto_apertura + $efectivoIngresos - $egresos;
        $this->montoApertura = $this->efectivoAnterior;
    }

    /**
     * Al marcar/desmarcar checkbox: alternar entre editable y automático
     */
    public function updatedCuadrar(bool $value): void
    {
        if (!$value && $this->efectivoAnterior !== null) {
            $this->montoApertura = $this->efectivoAnterior;
        } elseif ($value) {
            $this->montoApertura = 0;
        }
    }

    public function abrir(): void
    {
        if (SesionCaja::abierta()->exists()) {
            $this->addError('general', 'Ya hay una caja abierta. Ciérrala antes de abrir una nueva.');
            return;
        }

        $this->validate(
            ['montoApertura' => 'required|numeric|min:0'],
            [
                'montoApertura.required' => 'El monto inicial es obligatorio.',
                'montoApertura.numeric' => 'Debe ser un valor numérico.',
                'montoApertura.min' => 'El monto no puede ser negativo.',
            ]
        );

        SesionCaja::create([
            'abierta_por' => Auth::id(),
            'monto_apertura' => $this->montoApertura,
            'abierta_en' => now(),
            'estado' => 'abierta',
        ]);

        $this->dispatch('minToast', titulo: '¡Caja Abierta!', mensaje: 'La sesión de caja se inició con S/ ' . number_format($this->montoApertura, 2), icono: 'success');
    }

    public function render()
    {
        return view('livewire.caja.abrir-caja', [
            'sesionActiva' => SesionCaja::abierta()->with('abiertaPor')->orderByDesc('abierta_en')->first(),
        ]);
    }
}
