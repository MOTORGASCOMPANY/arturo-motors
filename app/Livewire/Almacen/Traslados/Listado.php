<?php

namespace App\Livewire\Almacen\Traslados;

use App\Models\Traslado;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public function render()
    {
        $traslados = Traslado::with(['sedeDestino', 'enviadoPor', 'detalles.producto', 'detalles.itemSerializado'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.almacen.traslados.listado', compact('traslados'));
    }
}
