<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\On;

class AdminPermisos extends Component
{
    use WithPagination;

    public $sort, $direction, $cant, $search;
    public $editando = false;
    // Propiedades para edición
    public $permisoId, $name, $descripcion;

    protected $rules = [
        'name' => 'required|string|min:3',
        'descripcion' => 'nullable|string'
    ];

    public function mount()
    {
        $this->direction = 'desc';
        $this->sort = 'id';
        $this->cant = 10;
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
    }

    public function editarPermiso($id)
    {
        $permiso = Permission::findOrFail($id);
        $this->permisoId = $permiso->id;
        $this->name = $permiso->name;
        $this->descripcion = $permiso->descripcion;
        $this->editando = true;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function actualizar()
    {
        $this->validate();
        $permiso = Permission::findOrFail($this->permisoId);
        $permiso->update([
            'name' => $this->name,
            'descripcion' => $this->descripcion,
        ]);

        $this->reset(['editando', 'permisoId', 'name', 'descripcion']);
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Se actualizó correctamente el permiso", icono: "success");
    }


    #[On('permiso-creado')]
    public function render()
    {
        $permisos = Permission::where('name', 'like', '%' . $this->search . '%')
            ->orderBy($this->sort, $this->direction)
            ->paginate($this->cant);
        return view('livewire.admin-permisos', compact('permisos'));
    }
}
