<?php

namespace App\Livewire\Fise;

use App\Services\FiseReporteService;
use Livewire\Component;

class Reporte extends Component
{
    public string $desde = '';
    public string $hasta = '';

    public function mount(): void
    {
        $this->desde = now()->startOfMonth()->format('Y-m-d');
        $this->hasta = now()->format('Y-m-d');
    }

    public function render(FiseReporteService $servicio)
    {
        $datos = $servicio->datos($this->desde, $this->hasta);

        $this->dispatch('chart-data-fise',
            labels: $datos['labels'],
            aprobadas: $datos['aprobadasPorDia'],
            rechazadas: $datos['rechazadasPorDia'],
            pendientes: $datos['pendientesPorDia'],
        );

        $this->dispatch('chart-pagos-fise',
            labels: $datos['labels'],
            montoTotal: $datos['montoTotalPorDia'],
            montoPagado: $datos['montoPagadoPorDia'],
            saldoPendiente: $datos['saldoPendientePorDia'],
        );

        return view('livewire.fise.reporte', $datos);
    }
}
