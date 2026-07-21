<?php

namespace App\Livewire;

use App\Models\Cita;
use App\Models\Expediente;
use App\Models\Vehiculo;
use Livewire\Component;

class DashboardTables extends Component
{
    public $citasProximas;
    public $vehiculosRecientes;
    public $expedientesAbiertos;

    public function mount()
    {
        $this->citasProximas = Cita::orderBy('fecha_cita', 'asc')->take(5)->get();
        $this->vehiculosRecientes = Vehiculo::latest()->take(5)->get();
        $this->expedientesAbiertos = Expediente::where('estado', 'en_proceso')->take(5)->get();
    }
    
    public function render()
    {
        return view('livewire.dashboard-tables');
    }
}
