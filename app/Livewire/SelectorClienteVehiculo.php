<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SelectorClienteVehiculo extends Component
{
    // Búsqueda principal
    public string $buscarVehiculo = '';
    public ?int $vehiculoId = null;
    public ?int $clienteId = null;
    public array $vehiculosEncontrados = [];

    // Selección cuando hay múltiples propietarios
    public array $asociadosDisponibles = [];
    public bool $mostrarSeleccionAsociados = false;
    public ?array $vehiculoSeleccionadoTemp = null;

    // Control de modales
    public bool $creandoVehiculoNuevo = false;
    public bool $vinculandoSegundoCliente = false;

    // CAMPOS PARA REGISTRO UNIFICADO (VEHÍCULO NUEVO) ---
    public string $nuevaPlaca = '';
    public string $nuevaMarca = '';
    public string $nuevoModelo = '';
    public ?int $nuevoAnio = null;
    public string $nuevoCombustible = 'GASOLINA';
    public string $nuevaSerie = '';
    public string $nuevoColor = '';

    // Cliente 1 (Principal)
    public string $tipoPersona = 'NATURAL';
    public string $nuevoNombre = '';
    public string $nuevoApellido = '';
    public string $nuevaRazonSocial = '';
    public string $nuevoDocumento = '';
    public string $nuevoTelefono = '';
    public string $nuevoEmail = '';
    public string $nuevaDireccion = '';

    // Cliente 2 (Copropietario Opcional al crear vehículo)
    public bool $incluirSegundoCliente = false;
    public string $tipoPersona2 = 'NATURAL';
    public string $nuevoNombre2 = '';
    public string $nuevoApellido2 = '';
    public string $nuevaRazonSocial2 = '';
    public string $nuevoDocumento2 = '';
    public string $nuevoTelefono2 = '';

    // BÚSQUEDA O VINCULACIÓN DE SEGUNDO CLIENTE POST-CREACIÓN
    public string $buscarClientePivote = '';
    public array $clientesPivoteEncontrados = [];
    public ?int $segundoClienteId = null;

    // Helper para consultar vehículo con sus clientes y datos pivote
    private function queryVehiculoConClientes()
    {
        return Vehiculo::with(['clientes' => fn($q) => $q->withPivot('es_principal', 'relacion')]);
    }

    public function updatedBuscarVehiculo()
    {
        $termino = trim($this->buscarVehiculo);

        if (strlen($termino) < 2) {
            $this->vehiculosEncontrados = [];
            return;
        }

        $this->vehiculosEncontrados = Vehiculo::with(['clientes' => function ($q) {
            $q->withPivot('es_principal', 'relacion');
        }])
            ->buscar($termino)
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function seleccionarVehiculo(int $vehiculoId)
    {
        $vehiculo = $this->queryVehiculoConClientes()->find($vehiculoId);
        if (!$vehiculo) return;

        $clientes = $vehiculo->clientes;

        // Limpiar las coincidencias inmediatamente al hacer clic
        $this->vehiculosEncontrados = [];

        if ($clientes->isEmpty()) {
            $this->vehiculoId = $vehiculo->id;
            $this->clienteId = null;
            $this->buscarVehiculo = $vehiculo->placa . ' — Sin propietario asignado';
            $this->finalizarSeleccion();
            return;
        }

        if ($clientes->count() === 1) {
            $c = $clientes->first();
            $this->confirmarSeleccion($vehiculo, $c->id, $c->nombre_completo);
            return;
        }

        $this->vehiculoSeleccionadoTemp = $vehiculo->toArray();
        $this->asociadosDisponibles = $clientes->map(fn($c) => [
            'id' => $c->id,
            'nombre_completo' => $c->nombre_completo,
            'documento' => $c->documento,
            'es_principal' => (bool)$c->pivot->es_principal,
            'relacion' => $c->pivot->relacion,
        ])->toArray();

        $this->mostrarSeleccionAsociados = true;
    }

    public function seleccionarTitular(int $clienteId)
    {
        if (!$this->vehiculoSeleccionadoTemp) return;

        $vehiculo = Vehiculo::find($this->vehiculoSeleccionadoTemp['id']);
        $cliente = Cliente::find($clienteId);

        if ($vehiculo && $cliente) {
            $this->confirmarSeleccion($vehiculo, $cliente->id, $cliente->nombre_completo);
        }

        // Aseguramos limpiar todos los estados temporales
        $this->reset([
            'mostrarSeleccionAsociados', 
            'vehiculoSeleccionadoTemp', 
            'asociadosDisponibles',
            'vehiculosEncontrados'
        ]);
    }

    private function confirmarSeleccion(Vehiculo $vehiculo, int $clienteId, string $nombreCliente)
    {
        $this->vehiculoId = $vehiculo->id;
        $this->clienteId = $clienteId;
        $this->buscarVehiculo = "{$vehiculo->placa} — {$nombreCliente}";
        $this->vehiculosEncontrados = [];

        $this->finalizarSeleccion();
    }

    private function finalizarSeleccion()
    {
        $this->dispatch('clienteSeleccionado', clienteId: $this->clienteId);
        $this->dispatch('vehiculoSeleccionado', vehiculoId: $this->vehiculoId);
    }

    public function deseleccionar()
    {
        $this->reset(['vehiculoId', 'clienteId', 'buscarVehiculo', 'vehiculosEncontrados', 'mostrarSeleccionAsociados']);
        $this->dispatch('clienteSeleccionado', clienteId: null);
        $this->dispatch('vehiculoSeleccionado', vehiculoId: null);
    }

    // REGISTRO UNIFICADO DE VEHÍCULO + CLIENTE(S)
    public function abrirModalNuevoVehiculo()
    {
        $this->reset([
            'nuevaPlaca', 'nuevaMarca', 'nuevoModelo', 'nuevoAnio', 'nuevoCombustible', 'nuevaSerie', 'nuevoColor',
            'tipoPersona', 'nuevoNombre', 'nuevoApellido', 'nuevaRazonSocial', 'nuevoDocumento', 'nuevoTelefono', 'nuevoEmail', 'nuevaDireccion',
            'incluirSegundoCliente', 'tipoPersona2', 'nuevoNombre2', 'nuevoApellido2', 'nuevaRazonSocial2', 'nuevoDocumento2', 'nuevoTelefono2'
        ]);

        $this->nuevaPlaca = strtoupper(trim($this->buscarVehiculo));
        $this->resetValidation();
        $this->creandoVehiculoNuevo = true;
    }

    public function guardarVehiculoNuevo()
    {
        // Reglas de validación para el vehículo
        $rules = [
            'nuevaPlaca' => 'required|string|unique:vehiculos,placa',
            'nuevaMarca' => 'required|string|max:50',
            'nuevoModelo' => 'required|string|max:50',
            'nuevoAnio' => 'nullable|integer|between:1900,' . (date('Y') + 1),
            'nuevoCombustible' => 'required|string',
            'nuevaSerie' => 'nullable|string|max:100',
            'nuevoColor' => 'nullable|string|max:50',

            // Cliente 1
            'tipoPersona' => 'required|in:NATURAL,JURIDICA',
            'nuevoDocumento' => 'required|string|unique:clientes,documento',
            'nuevoTelefono' => 'nullable|string|max:20',
            'nuevoEmail' => 'nullable|email|max:150',
            'nuevaDireccion' => 'nullable|string|max:255',
        ];

        if ($this->tipoPersona === 'JURIDICA') {
            $rules['nuevaRazonSocial'] = 'required|string|max:200';
        } else {
            $rules['nuevoNombre'] = 'required|string|max:100';
            $rules['nuevoApellido'] = 'nullable|string|max:100';
        }

        // Si se activa la casilla de segundo cliente (copropietario)
        if ($this->incluirSegundoCliente) {
            $rules['tipoPersona2'] = 'required|in:NATURAL,JURIDICA';
            $rules['nuevoDocumento2'] = 'required|string|different:nuevoDocumento|unique:clientes,documento';

            if ($this->tipoPersona2 === 'JURIDICA') {
                $rules['nuevaRazonSocial2'] = 'required|string|max:200';
            } else {
                $rules['nuevoNombre2'] = 'required|string|max:100';
                $rules['nuevoApellido2'] = 'nullable|string|max:100';
            }
        }

        $this->validate($rules);

        DB::transaction(function () {
            // 1. Crear Vehículo
            $vehiculo = Vehiculo::create([
                'placa' => strtoupper(trim($this->nuevaPlaca)),
                'marca' => trim($this->nuevaMarca),
                'modelo' => trim($this->nuevoModelo),
                'anio' => $this->nuevoAnio,
                'combustible' => $this->nuevoCombustible,
                'serie' => $this->nuevaSerie,
                'color' => $this->nuevoColor,
            ]);

            // 2. Crear Cliente 1
            $cliente1 = Cliente::create([
                'tipo_persona' => $this->tipoPersona,
                'nombre' => $this->tipoPersona === 'NATURAL' ? $this->nuevoNombre : null,
                'apellido' => $this->tipoPersona === 'NATURAL' ? $this->nuevoApellido : null,
                'razon_social' => $this->tipoPersona === 'JURIDICA' ? $this->nuevaRazonSocial : null,
                'documento' => $this->nuevoDocumento,
                'telefono' => $this->nuevoTelefono,
                'email' => $this->nuevoEmail,
                'direccion' => $this->nuevaDireccion,
            ]);

            // Vincular Cliente 1 como Principal
            $vehiculo->clientes()->attach($cliente1->id, [
                'es_principal' => true,
                'relacion' => 'PROPIETARIO',
            ]);

            // 3. Crear Cliente 2 si fue seleccionado
            if ($this->incluirSegundoCliente) {
                $cliente2 = Cliente::create([
                    'tipo_persona' => $this->tipoPersona2,
                    'nombre' => $this->tipoPersona2 === 'NATURAL' ? $this->nuevoNombre2 : null,
                    'apellido' => $this->tipoPersona2 === 'NATURAL' ? $this->nuevoApellido2 : null,
                    'razon_social' => $this->tipoPersona2 === 'JURIDICA' ? $this->nuevaRazonSocial2 : null,
                    'documento' => $this->nuevoDocumento2,
                    'telefono' => $this->nuevoTelefono2,
                ]);

                $vehiculo->clientes()->attach($cliente2->id, [
                    'es_principal' => false,
                    'relacion' => 'COPROPIETARIO',
                ]);
            }

            $this->creandoVehiculoNuevo = false;
            $this->seleccionarVehiculo($vehiculo->id);
        });

        $this->dispatch('minToast', titulo: '¡Éxito!', mensaje: 'Vehículo y propietario(s) registrados correctamente.', icono: 'success');
    }

    // VINCULAR COPROPIETARIO A VEHÍCULO YA EXISTENTE
    /*public function updatedBuscarClientePivote()
    {
        $termino = trim($this->buscarClientePivote);

        if (strlen($termino) < 2) {
            $this->clientesPivoteEncontrados = [];
            return;
        }

        $this->clientesPivoteEncontrados = Cliente::buscar($termino)
            ->limit(5)
            ->get()
            ->toArray();
    }
    public function abrirModalAsignarOtroCliente()
    {
        $this->reset(['segundoClienteId', 'buscarClientePivote', 'clientesPivoteEncontrados']);
        $this->resetValidation();
        $this->vinculandoSegundoCliente = true;
    }
    public function vincularClienteExistenteAVehiculo()
    {
        $this->validate([
            'segundoClienteId' => 'required|exists:clientes,id',
        ]);

        if (!$this->vehiculoId) {
            return;
        }

        $vehiculo = Vehiculo::find($this->vehiculoId);

        if ($vehiculo) {
            $vehiculo->clientes()->syncWithoutDetaching([
                $this->segundoClienteId => [
                    'es_principal' => false,
                    'relacion' => 'COPROPIETARIO',
                ]
            ]);

            $this->vinculandoSegundoCliente = false;
            $this->seleccionarVehiculo($this->vehiculoId);

            $this->dispatch('minToast', titulo: '¡Asociado!', mensaje: 'Copropietario vinculado con éxito.', icono: 'success');
        }
    }*/

    public function render()
    {
        return view('livewire.selector-cliente-vehiculo');
    }
}
