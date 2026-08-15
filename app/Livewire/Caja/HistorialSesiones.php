<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialSesiones extends Component
{
    use WithPagination;

    public int $cant = 10;
    public string $desde = '';
    public string $hasta = '';
    public string $estado = 'todos';

    public function updating($property)
    {
        if (in_array($property, ['cant', 'desde', 'hasta', 'estado'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['cant', 'desde', 'hasta', 'estado']);
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
