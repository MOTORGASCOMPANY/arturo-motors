<?php

namespace App\Livewire;

use App\Models\Roles;
use Livewire\Component;

class ListaRoles extends Component
{
    public $roles;

    public function mount(){
        //$this->roles=Roles::all();
        // Obtiene todos los roles excepto el que se llama 'Administrador del sistema'
        $this->roles = Roles::where('name', '!=', 'Administrador del sistema')->get();
    }

    public function render()
    {
        return view('livewire.lista-roles');
    }
}
