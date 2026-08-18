<?php

namespace App\Livewire\ServiceOrders;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\MovimientoCaja;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class CrearSimple extends Component
{
    public int $paso = 1;

    // Paso 1: cliente y vehículo
    //public string $buscarCliente = '';
    public ?int $clienteId = null;
    //public array $clientesEncontrados = [];
    public ?int $vehiculoId = null;

    //public bool $creandoVehiculoNuevo = false;
    //public string $nuevaPlaca = '';
    //public string $nuevaMarca = '';
    //public string $nuevoModelo = '';

    // Paso 1: cliente nuevo
    //public bool $creandoClienteNuevo = false;
    //public string $nuevoNombre = '';
    //public string $nuevoApellido = '';
    //public string $nuevoDocumento = '';
    //public string $nuevoTelefono = '';

    // Paso 2: servicio y precio
    public ?int $serviceId = null;

    public $precioLista = 0;

    public $precioFinal = 0;

    public string $descuentoMotivo = '';

    // Paso 3: cobro
    public string $metodoPago = 'efectivo';

    // Resultado
    public ?int $ordenCreadaId = null;

    public ?string $folioGenerado = null;

    #[On('clienteSeleccionado')]
    public function actualizarCliente(?int $clienteId)
    {
        $this->clienteId = $clienteId;
        if (!$clienteId) {
            $this->reset(['vehiculoId', 'serviceId', 'precioLista', 'precioFinal', 'descuentoMotivo']);
        }
    }

    #[On('vehiculoSeleccionado')]
    public function actualizarVehiculo(?int $vehiculoId)
    {
        $this->vehiculoId = $vehiculoId;
        if (!$vehiculoId) {
            $this->reset(['serviceId', 'precioLista', 'precioFinal', 'descuentoMotivo']);
        }
    }

    // En lugar de public function buscar(), usa este hook:
    /*public function updatedBuscarCliente()
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
            $this->buscarCliente = trim($cliente->nombre.' '.$cliente->apellido);
            $this->clientesEncontrados = [];
            $this->vehiculoId = null;
        }
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

        $this->clienteId = $cliente->id;
        $this->buscarCliente = trim($cliente->nombre . ' ' . $cliente->apellido);
        $this->creandoClienteNuevo = false;
        $this->clientesEncontrados = [];

        $this->dispatch('minToast', titulo: '¡Éxito!', mensaje: 'Cliente registrado correctamente.', icono: 'success');
    }*/

    /*public function getVehiculosProperty()
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

        $this->dispatch('minToast', titulo: '¡Éxito!', mensaje: 'Vehículo registrado correctamente.', icono: 'success');
    }*/

    public function irAPaso2()
    {
        $this->validate([
            'clienteId' => 'required|exists:clientes,id',
            'vehiculoId' => 'required|exists:vehiculos,id',
        ], [
            'clienteId.required' => 'Debes seleccionar un cliente.',
            'vehiculoId.required' => 'Debes seleccionar un vehículo.',
        ]);

        $this->paso = 2;
    }

    public function seleccionarServicio(int $serviceId)
    {
        $servicio = Service::findOrFail($serviceId);
        $this->serviceId = $servicio->id;
        $this->precioLista = $servicio->precio_base;
        $this->precioFinal = $servicio->precio_base;
        $this->descuentoMotivo = '';
    }

    public function irAPaso3()
    {
        $this->validate([
            'serviceId' => 'required|exists:services,id',
            'precioFinal' => 'required|numeric|min:0',
        ], [
            'serviceId.required' => 'Debes seleccionar un servicio.',
        ]);

        if (bccomp($this->precioFinal, $this->precioLista, 2) !== 0 && empty($this->descuentoMotivo)) {
            $this->addError('descuentoMotivo', 'Indica el motivo del ajuste de precio.');

            return;
        }

        $this->paso = 3;
    }

    public function procesarCobro()
    {
        $sesion = SesionCaja::abierta()->orderByDesc('abierta_en')->first();

        if (! $sesion) {
            $this->addError('caja', 'No hay una caja abierta. Pide al cajero que abra caja antes de cobrar.');
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'No hay una caja abierta actualmente.', icono: 'error');
            return;
        }

        $this->validate([
            'metodoPago' => 'required|in:efectivo,tarjeta,transferencia,otro',
            'clienteId' => 'required|exists:clientes,id',
            'vehiculoId' => 'required|exists:vehiculos,id',
            'serviceId' => 'required|exists:services,id',
            'precioFinal' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($sesion) {
                $orden = ServiceOrder::create([
                    'cliente_id' => $this->clienteId,
                    'vehiculo_id' => $this->vehiculoId,
                    'service_id' => $this->serviceId,
                    'estado' => 'entregada',
                    'precio_lista' => $this->precioLista,
                    'precio_final' => $this->precioFinal,
                    'descuento_motivo' => bccomp((string)$this->precioFinal, (string)$this->precioLista, 2) !== 0 ? $this->descuentoMotivo : null,
                    'creado_por' => Auth::id(),
                ]);

                MovimientoCaja::create([
                    'sesion_caja_id' => $sesion->id,
                    'tipo' => 'ingreso',
                    'monto' => $this->precioFinal,
                    'concepto' => 'Cobro orden #' . $orden->id . ' - ' . $orden->service->nombre,
                    'service_order_id' => $orden->id,
                    'usuario_id' => Auth::id(),
                ]);

                $comprobante = Comprobante::create([
                    'service_order_id' => $orden->id,
                    'folio' => Comprobante::generarFolio(),
                    'monto' => $this->precioFinal,
                    'metodo_pago' => $this->metodoPago,
                    'emitido_por' => Auth::id(),
                ]);

                $this->ordenCreadaId = $orden->id;
                $this->folioGenerado = $comprobante->folio;
            });
        } catch (\Throwable $e) {
            report($e); // queda en tu log de Laravel
            $this->addError('caja', 'Ocurrió un error al procesar el cobro. Intenta de nuevo.');
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Error al procesar el cobro.', icono: 'error');
            return;
        }

        $this->paso = 4;

        $this->dispatch('minToast', titulo: '¡Orden Creada!', mensaje: "La orden #{$this->ordenCreadaId} ha sido procesada con éxito.", icono: 'success');
    }

    public function render()
    {
        return view('livewire.service-orders.crear-simple', [
            'servicios' => Service::activos()->where('tipo', 'simple')->get(),
        ]);
    }
}