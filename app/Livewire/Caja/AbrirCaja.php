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
     * Carga el cierre de la última sesión como efectivo del día anterior.
     * El usuario puede modificarlo si cuadra manualmente.
     */
    public function cargarEfectivoAnterior(): void
    {
        $this->efectivoAnterior = SesionCaja::getLastCierre();
        $this->montoApertura = $this->efectivoAnterior ?? 0;
    }

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
        // REGLA 1: Ya hay una caja abierta
        $abierta = SesionCaja::abierta()->first();
        if ($abierta) {
            $this->addError('general', 'Ya hay una caja abierta. Ciérrala antes de abrir una nueva.');
            return;
        }

        // REGLA 2: Validar monto
        $this->validate(
            ['montoApertura' => 'required|numeric|min:0'],
            [
                'montoApertura.required' => 'El monto inicial es obligatorio.',
                'montoApertura.numeric' => 'Debe ser un valor numérico.',
                'montoApertura.min' => 'El monto no puede ser negativo.',
            ]
        );

        // REGLA 3: Si hay cierre anterior, alertar si la apertura difiere mucho
        if ($this->efectivoAnterior !== null && $this->cuadrar === false) {
            $diferencia = abs($this->montoApertura - $this->efectivoAnterior);
            if ($diferencia > 10) {
                $this->addError('general',
                    'El monto de apertura S/ ' . number_format($this->montoApertura, 2) .
                    ' difiere en S/ ' . number_format($diferencia, 2) .
                    ' del cierre anterior (S/ ' . number_format($this->efectivoAnterior, 2) . '). ' .
                    'Si cuadras manualmente, marca la casilla "Cuadrar caja".'
                );
                return;
            }
        }

        // REGLA 4: Si cuadra manualmente, debe ser exactamente 0
        if ($this->cuadrar && $this->montoApertura != 0) {
            $this->addError('general', 'Al cuadrar caja, el monto debe ser S/ 0.00.');
            return;
        }

        // Crear sesión (el modelo valida que solo haya una abierta)
        SesionCaja::create([
            'abierta_por' => Auth::id(),
            'monto_apertura' => $this->montoApertura,
            'abierta_en' => now(),
            'estado' => 'abierta',
        ]);

        $this->dispatch('minToast',
            titulo: '¡Caja Abierta!',
            mensaje: 'Sesión iniciada con S/ ' . number_format($this->montoApertura, 2),
            icono: 'success'
        );

        $this->cargarEfectivoAnterior(); // Recargar para mostrar sesión activa
    }

    public function render()
    {
        return view('livewire.caja.abrir-caja', [
            'sesionActiva' => SesionCaja::abierta()->with('abiertaPor')->orderByDesc('abierta_en')->first(),
        ]);
    }
}
