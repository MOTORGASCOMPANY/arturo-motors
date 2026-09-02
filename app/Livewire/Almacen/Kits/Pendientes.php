<?php

namespace App\Livewire\Almacen\Kits;

use App\Models\ItemSerializado;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Pendientes extends Component
{
    use WithPagination;

    #[On('kit-abierto')]
    public function refrescar()
    {
        // vacío
    }

    public function render()
    {
        $kits = ItemSerializado::with('producto')
            ->where('estado', 'en_stock')
            ->where('sede_id', 1)
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->orderBy('created_at')
            ->paginate(15);

        return view('livewire.almacen.kits.pendientes', compact('kits'));
    }
}
