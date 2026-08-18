<?php

namespace App\Livewire;

use App\Models\Conversion;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardCharts extends Component
{
    public $conversionesPorMes;

    public $vehiculosPorCombustible;

    public function mount()
    {
        // Conversiones por mes (últimos 6 meses)
        $this->conversionesPorMes = Conversion::select(
            DB::raw('MONTH(fecha_inicio) as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('fecha_inicio', now()->year)
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Vehículos por combustible
        $this->vehiculosPorCombustible = Vehiculo::select('combustible', DB::raw('COUNT(*) as total'))
            ->groupBy('combustible')
            ->pluck('total', 'combustible')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard-charts');
    }
}
