<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoles extends Component
{
    use WithPagination;

    // Listado
    public $sort = 'id';
    public $direction = 'desc';
    public $cant = 10;
    public $search = '';

    // Edición
    public $editando = false;
    public $rol = null;                // rol actual en edición
    public $name = '';                 // nombre editable del rol
    public $selectedPermisos = [];     // permisos seleccionados por nombre
    public $permisos = [];             // colección de permisos para el modal

    protected $rules=[
        "name"=>"required",
        "selectedPermisos"=>"array|min:1",

    ];

    protected $messages = [
        'selectedPermisos.required' => 'Selecciona al menos un permiso.',
        'selectedPermisos.min' => 'Debes seleccionar como mínimo un permiso para este rol.',
    ];

    // Cuando se actualiza el buscador, vuelve a la primera página
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
        $this->resetPage();
    }

    public function editaRol($id)
    {
        $rol = Role::findOrFail($id);

        $this->rol = $rol;
        $this->name = $rol->name;

        // Cargamos todos los permisos para listarlos
        $this->permisos = Permission::orderBy('name')->get();

        // Marcamos los permisos actuales del rol (por nombre)
        $this->selectedPermisos = $rol->permissions()->pluck('name')->all();

        $this->editando = true;
    }

    public function actualizar()
    {
        $this->validate();

        $rol = $this->rol;

        $rol->name = $this->name;
        $rol->save();

        $rol->syncPermissions($this->selectedPermisos);

        $nombre = $rol->name;

        $this->reset(['editando', 'rol', 'name', 'selectedPermisos', 'permisos']);
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Se actualizó correctamente el Rol de {$nombre}", icono: "success");
    }

    public function render()
    {
        $roles = Role::query()
            ->when($this->search, function ($q) {
                return $q->where('name', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sort, $this->direction)
            ->paginate($this->cant);

        return view('livewire.admin-roles', compact('roles'));
    }

}
