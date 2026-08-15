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

    public function getTotalIngresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto') : 0;
    }

    public function getTotalEgresosProperty()
    {
        return $this->sesion ? $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto') : 0;
    }

    public function getMontoEsperadoProperty()
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

        //$this->validate(['montoCierre' => 'required|numeric|min:0']);
        $this->validate(
            ['montoCierre' => 'required|numeric|min:0'],
            [
                'montoCierre.required' => 'El monto real en caja es obligatorio.',
                'montoCierre.numeric' => 'Debe ingresar un valor numérico válido.',
                'montoCierre.min' => 'El monto no puede ser un valor negativo.',
            ]
        );

        $this->sesion->cerrar((float) $this->montoCierre, Auth::id());

        //session()->flash('mensaje', 'Caja cerrada correctamente.');
        //$this->redirect(request()->header('Referer') ?? '/', navigate: true);

        // Limpiamos la propiedad para que la vista pase automáticamente al estado sin caja
        $this->sesion = null;
        $this->montoCierre = 0;
    }

    public function render()
    {
        return view('livewire.caja.cerrar-caja');
    }
}
