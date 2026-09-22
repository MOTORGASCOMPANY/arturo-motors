<?php

namespace App\Livewire\Almacen\Recepciones;

use App\Models\ItemSerializado;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getRecepcionesProperty()
    {
        return ItemSerializado::with('producto.categoria')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($this->search, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->search}%"));
            })
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.almacen.recepciones.listado', [
            'recepciones' => $this->recepciones,
        ]);
    }
}
