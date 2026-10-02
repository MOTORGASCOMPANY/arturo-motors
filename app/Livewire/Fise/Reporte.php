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
        // Rango por defecto de todos los reportes: 1 del mes en curso -> hoy.
        $this->desde = now()->startOfMonth()->format('Y-m-d');
        $this->hasta = now()->format('Y-m-d');
    }

    public function render(FiseReporteService $servicio)
    {
        $datos = $servicio->datos($this->desde, $this->hasta);

        $this->dispatch('chart-data-fise',
            labels: $datos['labels'],
            pagados: $datos['pagosPagadosPorDia'],
            parciales: $datos['pagosParcialesPorDia'],
            pendientes: $datos['pagosPendientesPorDia'],
            tasa: $datos['tasaPagoPorDia'],
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
