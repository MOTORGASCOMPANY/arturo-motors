<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialSesiones extends Component
{
    use WithPagination;

    public string $desde = '';
    public string $hasta = '';
    public string $estado = 'todos';
    public int $cant = 10;

    public function updating($property)
    {
        if (in_array($property, ['desde', 'hasta', 'estado', 'cant'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['desde', 'hasta', 'estado', 'cant']);
        $this->resetPage();
    }

    public function render()
    {
        $sesiones = SesionCaja::with(['abiertaPor', 'cerradaPor'])
            ->when($this->desde, fn ($q) => $q->whereDate('abierta_en', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('abierta_en', '<=', $this->hasta))
            ->when($this->estado !== 'todos', fn ($q) => $q->where('estado', $this->estado))
            ->orderByDesc('abierta_en')
            ->paginate($this->cant);

        return view('livewire.caja.historial-sesiones', compact('sesiones'));
    }
    
}
