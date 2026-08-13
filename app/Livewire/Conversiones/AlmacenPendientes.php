<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use Livewire\Component;
use Livewire\WithPagination;

class AlmacenPendientes extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'tecnico'])
            ->where('estado', 'aprobado_conversion')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('cliente', function ($qCliente) {
                        $qCliente->where('nombre', 'like', '%' . $this->search . '%')
                            ->orWhere('apellido', 'like', '%' . $this->search . '%')
                            ->orWhere('num_doc', 'like', '%' . $this->search . '%');
                    })->orWhereHas('vehiculo', function ($qVehiculo) {
                        $qVehiculo->where('placa', 'like', '%' . $this->search . '%');
                    });
                });
            })
            //->orderBy('evaluado_en')
            ->orderBy('evaluado_en', 'asc') // Primero las solicitudes con más tiempo en espera
            ->paginate(10);

        return view('livewire.conversiones.almacen-pendientes', compact('ordenes'));
    }
}
