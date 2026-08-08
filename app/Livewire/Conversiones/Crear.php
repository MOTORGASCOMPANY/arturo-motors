<?php

namespace App\Livewire\Conversiones;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Service;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Crear extends Component
{
    public string $buscarCliente = '';
    public ?int $clienteId = null;
    public array $clientesEncontrados = [];
    public ?int $vehiculoId = null;

    public bool $creandoVehiculoNuevo = false;
    public string $nuevaPlaca = '';
    public string $nuevaMarca = '';
    public string $nuevoModelo = '';

    public bool $creandoClienteNuevo = false;
    public string $nuevoNombre = '';
    public string $nuevoApellido = '';
    public string $nuevoDocumento = '';
    public string $nuevoTelefono = '';

    public ?int $serviceId = null;
    public $precioLista = 0;
    public $precioFinal = 0;
    public string $descuentoMotivo = '';

    public ?int $ordenCreadaId = null;

    public function updatedBuscarCliente()
    {
        $termino = trim($this->buscarCliente);
        $this->clientesEncontrados = strlen($termino) < 3
            ? []
            : Cliente::buscar($termino)->limit(8)->get()->toArray();
    }

    public function seleccionarCliente(int $clienteId)
    {
        $cliente = Cliente::find($clienteId);
        if ($cliente) {
            $this->clienteId = $cliente->id;
            $this->buscarCliente = trim($cliente->nombre . ' ' . $cliente->apellido);
            $this->clientesEncontrados = [];
            $this->vehiculoId = null;
        }
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

        $this->clienteId = $cliente->id;
        $this->buscarCliente = trim($cliente->nombre . ' ' . $cliente->apellido);
        $this->creandoClienteNuevo = false;
    }

    public function getVehiculosProperty()
    {
        return $this->clienteId ? Vehiculo::where('cliente_id', $this->clienteId)->get() : collect();
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
            'placa' => $this->nuevaPlaca,
            'marca' => $this->nuevaMarca,
            'modelo' => $this->nuevoModelo,
        ]);

        $this->vehiculoId = $vehiculo->id;
        $this->creandoVehiculoNuevo = false;
    }

    public function seleccionarServicio(int $serviceId)
    {
        $servicio = Service::findOrFail($serviceId);
        $this->serviceId = $servicio->id;
        $this->precioLista = $servicio->precio_base;
        $this->precioFinal = $servicio->precio_base;
    }

    public function crearOrden()
    {
        $this->validate([
            'clienteId' => 'required|exists:clientes,id',
            'vehiculoId' => 'required|exists:vehiculos,id',
            'serviceId' => 'required|exists:services,id',
            'precioFinal' => 'required|numeric|min:0',
        ]);

        if (bccomp($this->precioFinal, $this->precioLista, 2) !== 0 && empty($this->descuentoMotivo)) {
            $this->addError('descuentoMotivo', 'Indica el motivo del ajuste de precio.');
            return;
        }

        try {
            $orden = ServiceOrder::create([
                'cliente_id' => $this->clienteId,
                'vehiculo_id' => $this->vehiculoId,
                'service_id' => $this->serviceId,
                'estado' => 'creada',
                'precio_lista' => $this->precioLista,
                'precio_final' => $this->precioFinal,
                'descuento_motivo' => bccomp($this->precioFinal, $this->precioLista, 2) !== 0
                    ? $this->descuentoMotivo : null,
                'creado_por' => Auth::id(),
            ]);

            $this->ordenCreadaId = $orden->id;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al crear la orden. Intenta de nuevo.');
        }
    }

    public function render()
    {
        return view('livewire.conversiones.crear', [
            'servicios' => Service::activos()->where('tipo', 'conversion')->get(),
        ]);
    }
}
