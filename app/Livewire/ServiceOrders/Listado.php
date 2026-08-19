<?php

namespace App\Livewire\ServiceOrders;

use App\Models\ServiceOrder;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $buscar = '';
    public ?string $desde = null;
    public ?string $hasta = null;
    public string $tipo = 'todos';

    public function limpiarFiltros(): void
    {
        $this->reset(['buscar', 'desde', 'hasta', 'tipo']);
        $this->resetPage();
    }

    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    public function updatedDesde(): void
    {
        $this->resetPage();
    }

    public function updatedHasta(): void
    {
        $this->resetPage();
    }

    public function updatedTipo(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'comprobante'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('cliente', function ($cq) {
                        $cq->where('nombre', 'like', "%{$this->buscar}%")
                            ->orWhere('apellido', 'like', "%{$this->buscar}%")
                            ->orWhere('documento', 'like', "%{$this->buscar}%");
                    })->orWhereHas('vehiculo', function ($vq) {
                        $vq->where('placa', 'like', "%{$this->buscar}%");
                    });
                });
            })
            ->when($this->desde, fn ($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->when($this->tipo !== 'todos', function ($q) {
                if ($this->tipo === 'conversion') {
                    $q->tipoConversion();
                } else {
                    $q->whereHas('service', fn ($sq) => $sq->where('tipo', 'simple'));
                }
            })
            ->latest()
            ->paginate(15);

        return view('livewire.service-orders.listado', compact('ordenes'));
    }
}
