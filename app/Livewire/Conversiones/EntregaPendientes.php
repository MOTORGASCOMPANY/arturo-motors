<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use Livewire\Component;
use Livewire\WithPagination;

class EntregaPendientes extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterFechaDesde = '';
    public string $filterFechaHasta = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterFechaDesde(): void { $this->resetPage(); }
    public function updatedFilterFechaHasta(): void { $this->resetPage(); }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->where('estado', 'conversion_completada')
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
            ->when($this->filterFechaDesde !== '', fn ($q) => $q->where('fecha_fin_conversion', '>=', $this->filterFechaDesde))
            ->when($this->filterFechaHasta !== '', fn ($q) => $q->where('fecha_fin_conversion', '<=', $this->filterFechaHasta))
            ->orderBy('fecha_fin_conversion', 'desc')
            ->paginate(10);

        return view('livewire.conversiones.entrega-pendientes', compact('ordenes'));
    }
}
