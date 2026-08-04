<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardArturo extends Component
{
    public $totalClientes;
    public $totalVehiculos;
    public $totalCitas;
    public $totalUsuarios;
    public $citasPendientes;
    public $citasHoy;
    public $ultimasCitas;
    public $ultimosClientes;

    public function mount()
    {
        $this->totalClientes = Cliente::count();
        $this->totalVehiculos = Vehiculo::count();
        $this->totalCitas = Cita::count();
        $this->totalUsuarios = User::count();
        $this->citasPendientes = Cita::where('estado', 'pendiente')->count();
        $this->citasHoy = Cita::whereDate('fecha_cita', today())->count();
        $this->ultimasCitas = Cita::with(['cliente', 'vehiculo'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $this->ultimosClientes = Cliente::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-arturo');
    }
}
