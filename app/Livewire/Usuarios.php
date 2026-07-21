<?php

namespace App\Livewire;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Usuarios extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Listado
    public $sort = 'id';
    public $direction = 'desc';
    public $cant = 10;
    public $search = '';


    // Propiedades para edición
    public $user_edit;
    public $name, $email, $dni, $celular, $direccion, $fecha_nacimiento;
    public $numero_cuenta, $sistema_pensionario, $asignacion_familiar, $beneficios;

    // Propiedad únicamente para los roles seleccionados (array de nombres)
    public $selectedRoles = [];

    //public $roles;

    public $editando = false;

    protected function rules() {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->user_edit->id ?? 0),
            'dni' => 'nullable|string|max:8|min:8',
            'celular' => 'nullable|string|max:9|min:9',
            'direccion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'numero_cuenta' => 'nullable|string',
            'sistema_pensionario' => 'nullable|string',
            'asignacion_familiar' => 'boolean',
            'beneficios' => 'nullable|string',
        ];
    }

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

    public function render()
    {
        $usuarios = User::with('roles')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate($this->cant);

        $roles = Role::all();

        return view('livewire.usuarios', compact('usuarios', 'roles'));
    }

    public function editarUsuario(User $user)
    {
        $this->user_edit = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->dni = $user->dni;
        $this->celular = $user->celular;
        $this->direccion = $user->direccion;
        $this->fecha_nacimiento = optional($user->fecha_nacimiento)->format('Y-m-d');
        $this->numero_cuenta = $user->numero_cuenta;
        $this->sistema_pensionario = $user->sistema_pensionario;
        $this->asignacion_familiar = (bool)$user->asignacion_familiar;
        $this->beneficios = $user->beneficios;

        // Asignamos solo los nombres de los roles que el usuario tiene actualmente
        $this->selectedRoles = $user->roles->pluck('name')->toArray();

        $this->editando = true;
    }

    public function actualizar()
    {
        $this->validate();

        $this->user_edit->update([
            'name' => $this->name,
            'email' => $this->email,
            'dni' => $this->dni,
            'celular' => $this->celular,
            'direccion' => $this->direccion,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'numero_cuenta' => $this->numero_cuenta,
            'sistema_pensionario' => $this->sistema_pensionario,
            'asignacion_familiar' => $this->asignacion_familiar,
            'beneficios' => $this->beneficios,
        ]);

        // Sincronizar roles usando el helper de Spatie
        $this->user_edit->syncRoles($this->selectedRoles);

        $this->reset(['editando', 'selectedRoles']);
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Usuario actualizado correctamente.", icono: "success");
    }
}
