<?php

namespace App\Livewire;

use App\Models\Cita;
use App\Models\Conversion;
use App\Models\FiseSolicitud;
use Livewire\Component;

class DashboardCards extends Component
{
    public $conversionesActivas;
    public $conversionesMes;
    public $citasHoy;
    public $fisePendientes;

    public function mount()
    {
        // Conversiones activas
        $this->conversionesActivas = Conversion::where('estado', 'en_proceso')->count();

        // Conversiones finalizadas este mes
        $this->conversionesMes = Conversion::where('estado', 'finalizado')
            ->whereMonth('fecha_fin', now()->month)
            ->whereYear('fecha_fin', now()->year)
            ->count();

        // Citas de hoy
        $this->citasHoy = Cita::whereDate('fecha_cita', today())->count();

        // Solicitudes FISE pendientes
        $this->fisePendientes = FiseSolicitud::where('estado', 'pendiente')->count();
    }
    
    public function render()
    {
        return view('livewire.dashboard-cards');
    }
}
