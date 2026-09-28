<?php

namespace App\Livewire\Almacen;

use App\Models\CategoriaAlmacen;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoriaListado extends Component
{
    #[On('categoria-creada')]
    public function refrescar()
    {
        
        
    }

    public function render()
    {
        return view('livewire.almacen.categoria-listado', [
            'categorias' => CategoriaAlmacen::withCount('productos')->orderBy('nombre')->get(),
        ]);
    }
}
