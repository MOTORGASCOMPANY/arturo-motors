<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateRol extends Component
{
    public $nombre,$descripcion;
    public $open=false;

    protected $rules=[
        "nombre"=>"required|min:3",        
    ];   

    
    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }

    public function crearRol(){
        $this->validate();
        $Rol=Role::create(["name"=>$this->nombre,"guard_name"=>"web"]);
        $this->emitTo("admin-roles","render");
        $this->reset(["nombre","open"]);
        $this->emit("minAlert", ["titulo" => "¡BUEN TRABAJO!", "mensaje" => "Rol creado Correctamente", "icono" => "success"]); 
    }

    public function render()
    {
        return view('livewire.create-rol');
    }
}
