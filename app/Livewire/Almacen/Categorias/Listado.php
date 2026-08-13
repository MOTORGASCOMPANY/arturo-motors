<?php

namespace App\Livewire\Almacen\Categorias;

use App\Models\CategoriaAlmacen;
use Livewire\Attributes\On;
use Livewire\Component;

class Listado extends Component
{
    #[On('categoria-creada')]
    public function refrescar()
    {
        // Método vacío a propósito: el solo hecho de escuchar el evento
        // le indica a Livewire que debe re-renderizar este componente.
    }

    public function render()
    {
        return view('livewire.almacen.categorias.listado', [
            'categorias' => CategoriaAlmacen::withCount('productos')->orderBy('nombre')->get(),
        ]);
    }
}
