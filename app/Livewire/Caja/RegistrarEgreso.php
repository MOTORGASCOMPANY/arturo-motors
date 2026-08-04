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
            $this->addError('general', 'No hay una caja abierta.');
            return;
        }

        $this->validate([
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:150',
        ]);

        MovimientoCaja::create([
            'sesion_caja_id' => $sesion->id,
            'tipo' => 'egreso',
            'monto' => $this->monto,
            'concepto' => $this->concepto,
            //'usuario_id' => auth()->id(),
            'usuario_id' => Auth::id(),
        ]);

        $this->reset(['monto', 'concepto']);
        session()->flash('mensaje', 'Egreso registrado correctamente.');
    }

    public function render()
    {
        return view('livewire.caja.registrar-egreso');
    }
}
