<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialSesiones extends Component
{
    use WithPagination;

    public string $search = '';
    public string $desde = '';
    public string $hasta = '';
    public string $estado = 'todos';
    public int $cant = 10;
    public string $filterAbiertaPor = '';

    public function updating($property)
    {
        if (in_array($property, ['search', 'desde', 'hasta', 'estado', 'cant', 'filterAbiertaPor'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'desde', 'hasta', 'estado', 'cant', 'filterAbiertaPor']);
        $this->resetPage();
    }

    public function render()
    {
        $sesiones = SesionCaja::with(['abiertaPor', 'cerradaPor'])
            ->when($this->search, function ($q) {
                $q->whereHas('abiertaPor', function ($uq) {
                    $uq->where('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->desde, fn ($q) => $q->whereDate('abierta_en', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('abierta_en', '<=', $this->hasta))
            ->when($this->estado !== 'todos', fn ($q) => $q->where('estado', $this->estado))
            ->when($this->filterAbiertaPor !== '', fn ($q) => $q->where('abierta_por', $this->filterAbiertaPor))
            ->orderByDesc('abierta_en')
            ->paginate($this->cant);

        $users = \App\Models\User::orderBy('name')->get();

        return view('livewire.caja.historial-sesiones', compact('sesiones', 'users'));
    }
}
