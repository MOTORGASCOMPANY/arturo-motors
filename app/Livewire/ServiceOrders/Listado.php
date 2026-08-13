<?php

namespace App\Livewire\ServiceOrders;

use App\Models\ServiceOrder;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $buscar = '';
    public string $tipo = 'todos';
    public string $estado = 'todos';
    public string $desde = '';
    public string $hasta = '';

    public function updating($property)
    {
        if (in_array($property, ['buscar', 'tipo', 'estado', 'desde', 'hasta'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['buscar', 'tipo', 'estado', 'desde', 'hasta']);
        $this->resetPage();
    }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'comprobante'])
            ->when($this->buscar, function ($q) {
                $termino = $this->buscar;
                $q->whereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$termino}%")
                                                      ->orWhere('documento', 'like', "%{$termino}%"))
                  ->orWhereHas('vehiculo', fn ($v) => $v->where('placa', 'like', "%{$termino}%"));
            })
            ->when($this->tipo !== 'todos', fn ($q) => $q->whereHas('service', fn ($s) => $s->where('tipo', $this->tipo)))
            ->when($this->estado !== 'todos', fn ($q) => $q->where('estado', $this->estado))
            ->when($this->desde, fn ($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.service-orders.listado', compact('ordenes'));
    }
}
