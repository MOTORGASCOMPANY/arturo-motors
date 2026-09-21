<?php

namespace App\Livewire\Almacen\Traslados;

use App\Models\Traslado;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public ?Traslado $trasladoSeleccionado = null;
    public bool $mostrarDetalle = false;

    public function verDetalle(int $trasladoId)
    {
        $this->trasladoSeleccionado = Traslado::with(['sedeDestino', 'enviadoPor', 'detalles.producto', 'detalles.itemSerializado'])->find($trasladoId);
        $this->mostrarDetalle = true;
    }

    public function cerrarDetalle()
    {
        $this->mostrarDetalle = false;
        $this->trasladoSeleccionado = null;
    }

    public function render()
    {
        $traslados = Traslado::with(['sedeDestino', 'enviadoPor', 'detalles.producto', 'detalles.itemSerializado'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.almacen.traslados.listado', compact('traslados'));
    }
}
