<?php

namespace App\Livewire\ServiceOrders;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\MovimientoCaja;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\SesionCaja;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CrearSimple extends Component
{
    public int $paso = 1;

    // Paso 1: cliente y vehículo
    public string $buscarCliente = '';

    public ?int $clienteId = null;

    public array $clientesEncontrados = [];

    public ?int $vehiculoId = null;

    public bool $creandoVehiculoNuevo = false;

    public string $nuevaPlaca = '';

    public string $nuevaMarca = '';

    public string $nuevoModelo = '';

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

    // En lugar de public function buscar(), usa este hook:
    public function updatedBuscarCliente()
    {
        /*$this->clientesEncontrados = strlen($this->buscarCliente) < 3
            ? []
            : Cliente::buscar($this->buscarCliente)->limit(8)->get()->toArray();*/

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
        // $this->clienteId = $clienteId;
        // $this->clientesEncontrados = [];
        // $this->buscarCliente = Cliente::find($clienteId)->nombre;
        // $this->vehiculoId = null;

        $cliente = Cliente::find($clienteId);

        if ($cliente) {
            $this->clienteId = $cliente->id;
            // Mostramos el nombre completo en el input
            $this->buscarCliente = trim($cliente->nombre.' '.$cliente->apellido);
            $this->clientesEncontrados = []; // Oculta la lista desplegable
            $this->vehiculoId = null;
        }
    }

    public function getVehiculosProperty()
    {
        return $this->clienteId
            ? Vehiculo::where('cliente_id', $this->clienteId)->get()
            : collect();
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

    public function irAPaso2()
    {
        $this->validate([
            'clienteId' => 'required|exists:clientes,id',
            'vehiculoId' => 'required|exists:vehiculos,id',
        ]);

        $this->paso = 2;
    }

    public function seleccionarServicio(int $serviceId)
    {
        $servicio = Service::findOrFail($serviceId);
        $this->serviceId = $servicio->id;
        $this->precioLista = $servicio->precio_base;
        $this->precioFinal = $servicio->precio_base;
    }

    public function irAPaso3()
    {
        $this->validate([
            'serviceId' => 'required|exists:services,id',
            'precioFinal' => 'required|numeric|min:0',
        ]);

        if (bccomp($this->precioFinal, $this->precioLista, 2) !== 0 && empty($this->descuentoMotivo)) {
            $this->addError('descuentoMotivo', 'Indica el motivo del ajuste de precio.');

            return;
        }

        $this->paso = 3;
    }

    public function procesarCobro()
    {
        $sesion = SesionCaja::abierta()->first();

        if (! $sesion) {
            $this->addError('caja', 'No hay una caja abierta. Pide al cajero que abra caja antes de cobrar.');

            return;
        }

        $this->validate(['metodoPago' => 'required|in:efectivo,tarjeta,transferencia,otro']);

        DB::transaction(function () use ($sesion) {
            $orden = ServiceOrder::create([
                'cliente_id' => $this->clienteId,
                'vehiculo_id' => $this->vehiculoId,
                'service_id' => $this->serviceId,
                'estado' => 'entregada', // servicio simple: se cobra y entrega en un solo paso
                'precio_lista' => $this->precioLista,
                'precio_final' => $this->precioFinal,
                'descuento_motivo' => bccomp($this->precioFinal, $this->precioLista, 2) !== 0
                    ? $this->descuentoMotivo : null,
                'creado_por' => auth()->id(),
            ]);

            MovimientoCaja::create([
                'sesion_caja_id' => $sesion->id,
                'tipo' => 'ingreso',
                'monto' => $this->precioFinal,
                'concepto' => 'Cobro orden #'.$orden->id.' - '.$orden->service->nombre,
                'service_order_id' => $orden->id,
                'usuario_id' => auth()->id(),
            ]);

            $comprobante = Comprobante::create([
                'service_order_id' => $orden->id,
                'folio' => Comprobante::generarFolio(),
                'monto' => $this->precioFinal,
                'metodo_pago' => $this->metodoPago,
                'emitido_por' => auth()->id(),
            ]);

            $this->ordenCreadaId = $orden->id;
            $this->folioGenerado = $comprobante->folio;
        });

        $this->paso = 4;
    }

    public function render()
    {
        return view('livewire.service-orders.crear-simple', [
            'servicios' => Service::activos()->where('tipo', 'simple')->get(),
        ]);
    }
}
