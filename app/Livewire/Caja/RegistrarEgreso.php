<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use App\Models\MovimientoCaja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RegistrarEgreso extends Component
{
    public $monto = 0;
    public string $concepto = '';

    public function registrar()
    {
        $sesion = SesionCaja::abierta()->orderByDesc('abierta_en')->first();

        if (!$sesion) {
            $this->addError('general', 'No hay una sesión de caja abierta para registrar egresos.');
            return;
        }
    
        $this->validate(
            [
                'monto' => 'required|numeric|min:0.01',
                'concepto' => 'required|string|max:150',
            ],
            [
                'monto.required' => 'El monto del egreso es obligatorio.',
                'monto.numeric' => 'Ingrese un valor numérico válido.',
                'monto.min' => 'El monto debe ser mayor a 0.00.',
                'concepto.required' => 'El concepto o motivo es obligatorio.',
                'concepto.string' => 'El concepto debe ser una cadena de texto.',
                'concepto.max' => 'El concepto no debe superar los 150 caracteres.',
            ]
        );

        MovimientoCaja::create([
            'sesion_caja_id' => $sesion->id,
            'tipo' => 'egreso',
            'monto' => (float) $this->monto,
            'concepto' => trim($this->concepto),
            'usuario_id' => Auth::id(),
        ]);

        $this->reset(['monto', 'concepto']);
        //session()->flash('mensaje', 'Egreso registrado correctamente.');
        $this->dispatch('minToast', titulo: '¡EGRESO!', mensaje: 'Egreso registrado correctamente.', icono: 'success');
    }

    /*public function render()
    {
        return view('livewire.caja.registrar-egreso');
    }*/

    public function render()
    {
        $sesionActiva = SesionCaja::abierta()->exists();

        return view('livewire.caja.registrar-egreso', [
            'sesionActiva' => $sesionActiva,
        ]);
    }
}
