<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AbrirCaja extends Component
{
    public $montoApertura = 0;

    public function abrir()
    {
        if (SesionCaja::abierta()->exists()) {
            $this->addError('general', 'Ya hay una caja abierta. Ciérrala antes de abrir una nueva.');
            return;
        }

        $this->validate(['montoApertura' => 'required|numeric|min:0']);

        SesionCaja::create([
            //'abierta_por' => auth()->id(),
            'abierta_por' => Auth::id(),
            'monto_apertura' => $this->montoApertura,
            'abierta_en' => now(),
            'estado' => 'abierta',
        ]);

        session()->flash('mensaje', 'Caja abierta correctamente.');
        $this->redirect(request()->header('Referer') ?? '/', navigate: true);
    }

    public function render()
    {
        return view('livewire.caja.abrir-caja', [
            'sesionActiva' => SesionCaja::abierta()->with('abiertaPor')->orderByDesc('abierta_en')->first(),
        ]);
    }
}
