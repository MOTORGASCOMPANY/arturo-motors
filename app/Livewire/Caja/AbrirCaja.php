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
     * Obtiene el efectivo real de la última sesión cerrada.
     * Fórmula: monto_cierre de la sesión anterior (que ya es efectivo real contado).
     */
    public function cargarEfectivoAnterior(): void
    {
        $ultimaSesion = SesionCaja::where('estado', 'cerrada')
            ->with('movimientos.serviceOrder.comprobante')
            ->orderByDesc('cerrada_en')
            ->first();

        if ($ultimaSesion) {
            // El monto_cierre ya es el efectivo real contado al cerrar
            $this->efectivoAnterior = (float) $ultimaSesion->monto_cierre;
            // Autocompletar si no está cuadrando
            if (!$this->cuadrar) {
                $this->montoApertura = $this->efectivoAnterior;
            }
        }
    }

    /**
     * Cuando cambia el checkbox, ajustar el monto automáticamente
     */
    public function updatedCuadrar(bool $value): void
    {
        if (!$value && $this->efectivoAnterior !== null) {
            // Al desmarcar, volver al efectivo anterior
            $this->montoApertura = $this->efectivoAnterior;
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
