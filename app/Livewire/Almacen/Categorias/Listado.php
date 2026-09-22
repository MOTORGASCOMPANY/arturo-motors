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
        
        
    }

    public function render()
    {
        return view('livewire.almacen.categorias.listado', [
            'categorias' => CategoriaAlmacen::withCount('productos')->orderBy('nombre')->get(),
        ]);
    }
}
