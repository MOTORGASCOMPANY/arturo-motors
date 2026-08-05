<?php

namespace App\Livewire;

use App\Models\Cliente;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Component;

class ListaClientes extends Component
{
    use WithPagination;
    public $sort, $order, $cant, $search, $direction;
    // Propiedades para el modal y el formulario de edición
    public $open = false;
    public $editingCliente, $nombre, $apellido, $documento, $telefono, $email, $direccion;

    // Propiedades para el modal de creación
    public $openCreate = false;
    public $createNombre, $createApellido, $createDocumento, $createTelefono, $createEmail, $createDireccion;

    public function mount()
    {
        $this->direction = 'desc';
        $this->sort = 'id';
        $this->cant = 10;
        $this->open = false;
        $this->openCreate = false;
    }

    public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
        $this->resetPage(); // Resetear paginación al cambiar el orden
    }

    // Método para cargar los datos del cliente y abrir el modal
    public function edit(Cliente $cliente)
    {
        $this->editingCliente = $cliente;
        $this->nombre = $cliente->nombre;
        $this->apellido = $cliente->apellido;
        $this->documento = $cliente->documento;
        $this->telefono = $cliente->telefono;
        $this->email = $cliente->email;
        $this->direccion = $cliente->direccion;
        $this->open = true;
    }

    // Método para actualizar los datos del cliente
    public function updateCliente()
    {
        $this->validate([
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'documento' => [
                'required', 
                'string', 
                'max:8', 
                Rule::unique('clientes')->ignore($this->editingCliente->id)
            ],
            'telefono' => 'required|string|digits:9',
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:50', 
                Rule::unique('clientes')->ignore($this->editingCliente->id)
            ],
            'direccion' => 'nullable|string|max:100',
        ]);

        $this->editingCliente->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'documento' => $this->documento,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
        ]);

        $this->reset(['open', 'nombre', 'apellido', 'documento', 'telefono', 'email', 'direccion']);
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Cliente actualizado correctamente", icono: "success");
    }

    // Método para abrir el modal de creación
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->openCreate = true;
    }

    // Método para guardar un nuevo cliente
    public function storeCliente()
    {
        $this->validate([
            'createNombre' => 'required|string|max:50',
            'createApellido' => 'required|string|max:50',
            'createDocumento' => [
                'required', 
                'string', 
                'max:8', 
                Rule::unique('clientes', 'documento')
            ],
            'createTelefono' => 'required|string|digits:9',
            'createEmail' => [
                'required', 
                'string', 
                'email', 
                'max:50', 
                Rule::unique('clientes', 'email')
            ],
            'createDireccion' => 'nullable|string|max:100',
        ]);

        Cliente::create([
            'nombre' => $this->createNombre,
            'apellido' => $this->createApellido,
            'documento' => $this->createDocumento,
            'telefono' => $this->createTelefono,
            'email' => $this->createEmail,
            'direccion' => $this->createDireccion,
        ]);

        $this->resetCreateForm();
        $this->openCreate = false;
        $this->dispatch('minAlert', titulo: "¡BUEN TRABAJO!", mensaje: "Cliente creado correctamente", icono: "success");
    }

    // Método para limpiar el formulario de creación
    public function resetCreateForm()
    {
        $this->reset(['createNombre', 'createApellido', 'createDocumento', 'createTelefono', 'createEmail', 'createDireccion']);
    }

    public function render()
    {
        $clientes = Cliente::with(['vehiculos'])
            ->buscar($this->search)
            ->ordenar($this->sort, $this->direction)
            ->paginate($this->cant);

            return view('livewire.lista-clientes', compact('clientes'));
    }

}
