<?php

namespace App\Livewire;

use App\Models\Cliente;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ListaClientes extends Component
{
    use WithPagination;

    public $sort;

    public $order;

    public $cant;

    public $search;

    public $direction;

    // Propiedades para el modal y el formulario de edición
    public $open = false;

    public $editingCliente;

    public $nombre;

    public $apellido;

    public $documento;

    public $telefono;

    public $email;

    public $direccion;

    // Propiedades para el modal de creación
    public $openCreate = false;

    public $createNombre;

    public $createApellido;

    public $createDocumento;

    public $createTelefono;

    public $createEmail;

    public $createDireccion;

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
        try {
            $this->validate(
                [
                    'nombre' => 'required|string|max:50',
                    'apellido' => 'required|string|max:50',
                    'documento' => [
                        'required',
                        'string',
                        'max:8',
                        Rule::unique('clientes')->ignore($this->editingCliente->id),
                    ],
                    'telefono' => 'required|string|digits:9',
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:50',
                        Rule::unique('clientes')->ignore($this->editingCliente->id),
                    ],
                    'direccion' => 'nullable|string|max:100',
                ],
                [
                    'nombre.required' => 'El nombre es obligatorio',
                    'nombre.max' => 'El nombre no puede tener más de 50 caracteres',
                    'apellido.required' => 'El apellido es obligatorio',
                    'apellido.max' => 'El apellido no puede tener más de 50 caracteres',
                    'documento.required' => 'El documento es obligatorio',
                    'documento.max' => 'El documento no puede tener más de 8 caracteres',
                    'documento.unique' => 'Este documento ya está registrado',
                    'telefono.required' => 'El teléfono es obligatorio',
                    'telefono.digits' => 'El teléfono debe tener exactamente 9 dígitos',
                    'email.required' => 'El correo es obligatorio',
                    'email.email' => 'El correo no es válido',
                    'email.max' => 'El correo no puede tener más de 50 caracteres',
                    'email.unique' => 'Este correo ya está registrado',
                ]
            );

            $this->editingCliente->update([
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'documento' => $this->documento,
                'telefono' => $this->telefono,
                'email' => $this->email,
                'direccion' => $this->direccion,
            ]);

            $this->reset(['open', 'nombre', 'apellido', 'documento', 'telefono', 'email', 'direccion']);
            $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Cliente actualizado correctamente', icono: 'success');
        } catch (ValidationException $e) {
            $errores = $e->errors();
            $campos = [
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'documento' => 'Documento',
                'telefono' => 'Teléfono',
                'email' => 'Correo',
                'direccion' => 'Dirección',
            ];
            $lista = [];
            foreach ($errores as $campo => $mensajes) {
                $nombre = $campos[$campo] ?? $campo;
                $lista[] = '- '.$nombre.': '.$mensajes[0];
            }
            $this->dispatch('minAlert', titulo: 'Error de validación', mensaje: "Corrija los siguientes campos:\n".implode("\n", $lista), icono: 'error');
        }
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
        try {
            $this->validate(
                [
                    'createNombre' => 'required|string|max:50',
                    'createApellido' => 'required|string|max:50',
                    'createDocumento' => [
                        'required',
                        'string',
                        'max:8',
                        Rule::unique('clientes', 'documento'),
                    ],
                    'createTelefono' => 'required|string|digits:9',
                    'createEmail' => [
                        'required',
                        'string',
                        'email',
                        'max:50',
                        Rule::unique('clientes', 'email'),
                    ],
                    'createDireccion' => 'nullable|string|max:100',
                ],
                [
                    'createNombre.required' => 'El nombre es obligatorio',
                    'createNombre.max' => 'El nombre no puede tener más de 50 caracteres',
                    'createApellido.required' => 'El apellido es obligatorio',
                    'createApellido.max' => 'El apellido no puede tener más de 50 caracteres',
                    'createDocumento.required' => 'El documento es obligatorio',
                    'createDocumento.max' => 'El documento no puede tener más de 8 caracteres',
                    'createDocumento.unique' => 'Este documento ya está registrado',
                    'createTelefono.required' => 'El teléfono es obligatorio',
                    'createTelefono.digits' => 'El teléfono debe tener exactamente 9 dígitos',
                    'createEmail.required' => 'El correo es obligatorio',
                    'createEmail.email' => 'El correo no es válido',
                    'createEmail.max' => 'El correo no puede tener más de 50 caracteres',
                    'createEmail.unique' => 'Este correo ya está registrado',
                ]
            );

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
            $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Cliente creado correctamente', icono: 'success');
        } catch (ValidationException $e) {
            $errores = $e->errors();
            $campos = [
                'createNombre' => 'Nombre',
                'createApellido' => 'Apellido',
                'createDocumento' => 'Documento',
                'createTelefono' => 'Teléfono',
                'createEmail' => 'Correo',
                'createDireccion' => 'Dirección',
            ];
            $lista = [];
            foreach ($errores as $campo => $mensajes) {
                $nombre = $campos[$campo] ?? $campo;
                $lista[] = '- '.$nombre.': '.$mensajes[0];
            }
            $this->dispatch('minAlert', titulo: 'Error de validación', mensaje: "Corrija los siguientes campos:\n".implode("\n", $lista), icono: 'error');
        }
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
