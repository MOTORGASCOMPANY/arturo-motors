<?php

namespace App\Livewire\Reportes;

use App\Models\Cita;
use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;

class ReporteCitas extends Component
{
    use WithPagination;

    public $sort = 'fecha_cita';

    public $direction = 'desc';

    public $cant = 10;

    public $search;

    public $estado;

    public $fechaInicio;

    public $fechaFin;

    public $sede_id = 'todos';

    public function mount()
    {
        $this->estado = 'todos';
        // donde debemos inicializar fechaInicio y fechaFin
        // $this->fechaInicio = now()->subMonth()->format('Y-m-d');
        // $this->fechaFin = now()->format('Y-m-d');
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'estado', 'fechaInicio', 'fechaFin', 'sede_id'])) {
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
        $sedes = Sede::all();
        
        $citas = Cita::with(['cliente', 'vehiculo', 'asesor', 'sede'])
            ->buscar($this->search)
            ->estado($this->estado)
            ->when($this->sede_id && $this->sede_id !== 'todos', function ($query) {
                $query->where('sede_id', $this->sede_id);
            })
            ->when($this->fechaInicio, function ($query) {
                $query->whereDate('fecha_cita', '>=', $this->fechaInicio);
            })
            ->when($this->fechaFin, function ($query) {
                $query->whereDate('fecha_cita', '<=', $this->fechaFin);
            })
            ->ordenar($this->sort, $this->direction)
            ->paginate($this->cant);

        return view('livewire.reportes.reporte-citas', compact('citas', 'sedes'));
    }
}
