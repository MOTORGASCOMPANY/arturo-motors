<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class CreatePermiso extends Component
{
    public $nombre, $descripcion;
    public $open = false;

    protected $rules = [
        "nombre" => "required|min:3",
        "descripcion" => "required|min:3",
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function crearPermiso()
    {
        $this->validate();
        $permiso = Permission::create(["name" => $this->nombre, "descripcion" => $this->descripcion, "guard_name" => "web"]);
        $this->dispatch('permiso-creado')->to(AdminPermisos::class);
        $this->reset(["nombre", "descripcion", "open"]);
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Permiso creado Correctamente", icono: "success");
    }

    public function render()
    {
        return view('livewire.create-permiso');
    }
}
