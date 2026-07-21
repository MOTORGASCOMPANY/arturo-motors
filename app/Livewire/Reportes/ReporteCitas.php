<?php

namespace App\Livewire\Reportes;

use App\Models\Cita;
use Livewire\Component;
use Livewire\WithPagination;

class ReporteCitas extends Component
{
    use WithPagination;

    public $sort = 'fecha_cita', $direction = 'desc', $cant = 10;
    public $search, $estado, $fechaInicio, $fechaFin;

    public function mount()
    {
        $this->estado = 'todos';
        // donde debemos inicializar fechaInicio y fechaFin
        //$this->fechaInicio = now()->subMonth()->format('Y-m-d');
        //$this->fechaFin = now()->format('Y-m-d');
    }

    public function updating($property)
    {
        // Resetea la paginación cuando cambia una propiedad que afecta el query
        if ($property === 'search' || $property === 'estado' || $property === 'fechaInicio' || $property === 'fechaFin') {
            $this->resetPage();
        }
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
    }

    public function render()
    {
        $citas = Cita::with(['cliente', 'vehiculo', 'asesor'])
            ->buscar($this->search)
            ->estado($this->estado)
            ->when($this->fechaInicio, function ($query) {
                $query->whereDate('fecha_cita', '>=', $this->fechaInicio);
            })
            ->when($this->fechaFin, function ($query) {
                $query->whereDate('fecha_cita', '<=', $this->fechaFin);
            })
            ->ordenar($this->sort, $this->direction)
            ->paginate($this->cant);

        return view('livewire.reportes.reporte-citas', compact('citas'));
    }
}
