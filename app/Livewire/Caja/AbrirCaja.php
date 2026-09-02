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
     * Obtiene el efectivo de la última sesión cerrada (monto_cierre).
     */
    public function cargarEfectivoAnterior(): void
    {
        $ultimaSesion = SesionCaja::where('estado', 'cerrada')
            ->orderByDesc('cerrada_en')
            ->first();

        if ($ultimaSesion && $ultimaSesion->monto_cierre > 0) {
            $this->efectivoAnterior = (float) $ultimaSesion->monto_cierre;
            $this->montoApertura = $this->efectivoAnterior;
        } else {
            // Si no hay sesión cerrada o monto_cierre es 0, dejar en 0
            $this->efectivoAnterior = null;
            $this->montoApertura = 0;
        }
    }

    /**
     * Al marcar/desmarcar checkbox: alternar entre editable y automático
     */
    public function updatedCuadrar(bool $value): void
    {
        if (!$value && $this->efectivoAnterior !== null) {
            // Al desmarcar: volver al efectivo anterior
            $this->montoApertura = $this->efectivoAnterior;
        } elseif ($value) {
            // Al marcar: limpiar para que ingrese manual
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
