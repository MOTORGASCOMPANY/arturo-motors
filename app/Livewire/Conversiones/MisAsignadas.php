<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MisAsignadas extends Component
{
    use WithPagination;

    public string $estado = 'pendientes'; // pendientes | todas
    public string $search = '';
    public string $filterGranularEstado = '';
    public string $filterFechaDesde = '';
    public string $filterFechaHasta = '';

    public function updatedEstado(): void { $this->resetPage(); }
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterGranularEstado(): void { $this->resetPage(); }
    public function updatedFilterFechaDesde(): void { $this->resetPage(); }
    public function updatedFilterFechaHasta(): void { $this->resetPage(); }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->where('tecnico_id', Auth::id())
            ->when($this->estado === 'pendientes', function ($q) {
                $q->whereIn('estado', ['en_evaluacion', 'aprobado_conversion', 'en_conversion']);
            })
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('cliente', function ($c) {
                        $c->where('nombre', 'like', "%{$this->search}%")
                          ->orWhere('apellido', 'like', "%{$this->search}%")
                          ->orWhere('documento', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('vehiculo', function ($v) {
                        $v->where('placa', 'like', "%{$this->search}%");
                    })
                    ->orWhere('id', $this->search);
                });
            })
            ->when($this->filterGranularEstado !== '', fn ($q) => $q->where('estado', $this->filterGranularEstado))
            ->when($this->filterFechaDesde !== '', fn ($q) => $q->where('created_at', '>=', $this->filterFechaDesde))
            ->when($this->filterFechaHasta !== '', fn ($q) => $q->where('created_at', '<=', $this->filterFechaHasta))
            ->latest()
            ->paginate(10);

        return view('livewire.conversiones.mis-asignadas', compact('ordenes'));
    }
}
