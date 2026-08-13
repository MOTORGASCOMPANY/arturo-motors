<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Livewire\Component;

class SelectorClienteVehiculo extends Component
{
    // Búsqueda y selección
    public string $buscarCliente = '';
    public ?int $clienteId = null;
    public array $clientesEncontrados = [];
    public ?int $vehiculoId = null;

    // Modales
    public bool $creandoClienteNuevo = false;
    public string $nuevoNombre = '';
    public string $nuevoApellido = '';
    public string $nuevoDocumento = '';
    public string $nuevoTelefono = '';

    public bool $creandoVehiculoNuevo = false;
    public string $nuevaPlaca = '';
    public string $nuevaMarca = '';
    public string $nuevoModelo = '';

    public function updatedBuscarCliente()
    {
        $termino = trim($this->buscarCliente);

        if (strlen($termino) < 3) {
            $this->clientesEncontrados = [];
            return;
        }

        $this->clientesEncontrados = Cliente::buscar($termino)
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function seleccionarCliente(int $clienteId)
    {
        $cliente = Cliente::find($clienteId);

        if ($cliente) {
            $this->clienteId = $cliente->id;
            $this->buscarCliente = trim($cliente->nombre . ' ' . $cliente->apellido);
            $this->clientesEncontrados = [];
            $this->vehiculoId = null;

            // Emitir evento al componente padre
            $this->dispatch('clienteSeleccionado', clienteId: $this->clienteId);
            $this->dispatch('vehiculoSeleccionado', vehiculoId: null);
        }
    }

    public function updatedVehiculoId($value)
    {
        $vId = $value ? (int)$value : null;
        $this->dispatch('vehiculoSeleccionado', vehiculoId: $vId);
    }

    public function abrirModalNuevoCliente()
    {
        $this->reset(['nuevoNombre', 'nuevoApellido', 'nuevoDocumento', 'nuevoTelefono']);
        $this->resetValidation();
        $this->creandoClienteNuevo = true;
    }

    public function guardarClienteNuevo()
    {
        $this->validate([
            'nuevoNombre' => 'required|string|max:100',
            'nuevoApellido' => 'nullable|string|max:100',
            'nuevoDocumento' => 'required|string|unique:clientes,documento',
            'nuevoTelefono' => 'nullable|string|max:20',
        ]);

        $cliente = Cliente::create([
            'nombre' => $this->nuevoNombre,
            'apellido' => $this->nuevoApellido,
            'documento' => $this->nuevoDocumento,
            'telefono' => $this->nuevoTelefono,
        ]);

        $this->seleccionarCliente($cliente->id);
        $this->creandoClienteNuevo = false;

        $this->dispatch('minToast', titulo: '¡Éxito!', mensaje: 'Cliente registrado correctamente.', icono: 'success');
    }

    public function getVehiculosProperty()
    {
        return $this->clienteId 
            ? Vehiculo::where('cliente_id', $this->clienteId)->get() 
            : collect();
    }

    public function abrirModalNuevoVehiculo()
    {
        $this->reset(['nuevaPlaca', 'nuevaMarca', 'nuevoModelo']);
        $this->resetValidation();
        $this->creandoVehiculoNuevo = true;
    }

    public function guardarVehiculoNuevo()
    {
        $this->validate([
            'nuevaPlaca' => 'required|string|unique:vehiculos,placa',
            'nuevaMarca' => 'required|string',
            'nuevoModelo' => 'required|string',
        ]);

        $vehiculo = Vehiculo::create([
            'cliente_id' => $this->clienteId,
            'placa' => strtoupper($this->nuevaPlaca),
            'marca' => $this->nuevaMarca,
            'modelo' => $this->nuevoModelo,
        ]);

        $this->vehiculoId = $vehiculo->id;
        $this->creandoVehiculoNuevo = false;

        $this->dispatch('vehiculoSeleccionado', vehiculoId: $this->vehiculoId);
        $this->dispatch('minToast', titulo: '¡Éxito!', mensaje: 'Vehículo registrado correctamente.', icono: 'success');
    }
    
    public function render()
    {
        return view('livewire.selector-cliente-vehiculo');
    }
}
