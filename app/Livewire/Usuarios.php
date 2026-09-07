<?php

namespace App\Livewire;

use App\Models\Roles;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class Usuarios extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Listado
    public $sort = 'id';

    public $direction = 'desc';

    public $cant = 10;

    public $search = '';

    // Propiedades para creación
    public $creando = false;

    public $new_name;

    public $new_email;

    public $new_password;

    public $new_dni;

    public $new_celular;

    // Propiedades para edición
    public $user_edit;

    public $name;

    public $email;

    public $dni;

    public $celular;

    public $direccion;

    public $fecha_nacimiento;

    public $numero_cuenta;

    public $sistema_pensionario;

    public $asignacion_familiar;

    public $beneficios;

    // Propiedad únicamente para los roles seleccionados (array de nombres)
    public $selectedRoles = [];

    public $editando = false;

    protected function rules()
    {
        if ($this->creando) {
            return [
                'new_name' => 'required|string|max:255',
                'new_email' => 'required|email|unique:users,email',
                'new_password' => 'required|string|min:6',
                'new_dni' => 'nullable|string|max:15',
                'new_celular' => 'nullable|string|max:20',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.($this->user_edit->id ?? 0),
            'dni' => 'nullable|string|max:15',
            'celular' => 'nullable|string|max:20',
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
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
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
        $this->asignacion_familiar = (bool) $user->asignacion_familiar;
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
        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Usuario actualizado correctamente.', icono: 'success');
    }

    public function abrirModalCrear()
    {
        abort_unless(auth()->user()->hasRole('Administrador del sistema'), 403);

        $this->reset([
            'creando', 'new_name', 'new_email', 'new_password', 'new_dni', 'new_celular',
        ]);
        $this->creando = true;
    }

    public function store()
    {
        abort_unless(auth()->user()->hasRole('Administrador del sistema'), 403);

        $this->validate();

        $user = User::create([
            'name' => $this->new_name,
            'email' => $this->new_email,
            'password' => Hash::make($this->new_password),
            'dni' => $this->new_dni,
            'celular' => $this->new_celular,
        ]);

        $this->reset([
            'creando', 'new_name', 'new_email', 'new_password', 'new_dni', 'new_celular',
        ]);

        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Usuario creado correctamente.', icono: 'success');
    }
}
