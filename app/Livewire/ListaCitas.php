<?php

namespace App\Livewire;

use App\Models\AsesorExterno;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Sede;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListaCitas extends Component
{
    use WithPagination;

    public $sort = 'created_at', $order, $cant = 10, $search = '', $direction = 'desc';

    // Datos para dialog modal , crear cita, cliente y vehicul0
    public $open = false;

    // Propiedades del Formulario
    public $documento;
    public $nombre;
    public $apellido;
    public $telefono;
    public $email;
    public $direccion;

    public $placa;
    public $marca;
    public $modelo;
    public $anio;
    public $serie;
    public $color;
    public $combustible;

    public $sede_id = 1; // ID predeterminado (Arturo Motors "Callao")
    public $fecha_cita;
    public $motivo;

    // Control de asesor externo
    public $is_externo = false;
    public $asesor_externo_id = null;

    // Modal y Propiedades para Aceptar Cita & Crear Órden
    public $openAceptarModal = false;
    public $citaSeleccionada = null;
    public $serviceId = null;
    public $precioLista = 0;
    public $precioFinal = 0;
    public $descuentoMotivo = '';

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'documento'         => 'nullable|string|max:20',
            'nombre'            => 'required|string|max:100',
            'apellido'          => 'nullable|string|max:100',
            'telefono'          => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:100',
            'direccion'         => 'nullable|string|max:255',

            'placa'             => 'required|string|max:15',
            'marca'             => 'nullable|string|max:50',
            'modelo'            => 'nullable|string|max:50',
            'anio'              => 'nullable|numeric',
            'serie'             => 'nullable|string|max:50',
            'color'             => 'nullable|string|max:50',
            'combustible'       => 'nullable|string|max:50',

            'sede_id'           => 'required|exists:sedes,id',
            'fecha_cita'        => 'required|date|after_or_equal:today',
            'motivo'            => 'nullable|string|max:500',

            'asesor_externo_id' => 'required_if:is_externo,true',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Marca el método con el atributo #[On] para que escuche los eventos.
    #[On('refrescarListaCitas')]
    public function actualizarListaCitas() {}

    // Marca estado de cita como rechazado
    #[On('marcarCitaComoRechazada')]
    public function marcarCitaComoRechazada($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->estado = 'rechazada';
        $cita->save();

        $this->dispatch('citaRechazada');
    }

    // Método para abrir el modal de Aceptar Cita
    public function abrirModalAceptar($citaId)
    {
        $this->citaSeleccionada = Cita::with(['cliente', 'vehiculo'])->findOrFail($citaId);
        $this->reset(['serviceId', 'precioLista', 'precioFinal', 'descuentoMotivo']);
        $this->resetValidation();
        $this->openAceptarModal = true;
    }

    // Seleccionar servicio e inicializar precios
    public function seleccionarServicio($serviceId)
    {
        $servicio = Service::find($serviceId);
        if ($servicio) {
            $this->serviceId = $servicio->id;
            $this->precioLista = $servicio->precio_base ?? 0;
            $this->precioFinal = $servicio->precio_base ?? 0;
        }
    }

    // Procesar Aceptación de la Cita y Crear la Orden de Servicio
    public function procesarAceptacionCita()
    {
        $this->validate([
            'serviceId'   => 'required|exists:services,id',
            'precioFinal' => 'required|numeric|min:0',
            'descuentoMotivo' => 'nullable|string|max:255',
        ], [
            'serviceId.required' => 'Debe seleccionar un tipo de servicio.',
            'precioFinal.required' => 'El precio acordado es obligatorio.',
        ]);

        if (!$this->citaSeleccionada) {
            return;
        }

        try {
            DB::transaction(function () {
                $cita = Cita::findOrFail($this->citaSeleccionada->id);
                $cita->estado = 'aceptada';
                $cita->save();

                $ordenExiste = ServiceOrder::where('cita_id', $cita->id)->exists();

                if (!$ordenExiste) {
                    ServiceOrder::create([
                        'cliente_id'       => $cita->cliente_id,
                        'vehiculo_id'      => $cita->vehiculo_id,
                        'service_id'       => $this->serviceId,
                        'cita_id'          => $cita->id,
                        'estado'           => 'creada',
                        'precio_lista'     => $this->precioLista,
                        'precio_final'     => $this->precioFinal,
                        'descuento_motivo' => $this->descuentoMotivo,
                        'creado_por'       => Auth::id(),
                    ]);
                }
            });

            $this->openAceptarModal = false;
            $this->dispatch('citaAceptada');

        } catch (\Throwable $e) {
            Log::error('Error al aceptar cita y generar la orden: ' . $e->getMessage(), [
                'cita_id' => $this->citaSeleccionada->id ?? null,
                'exception' => $e
            ]);

            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un problema al aceptar la cita y crear la orden.', icono: 'error');
        }
    }

    // Marca estado de cita como aceptada y crea Expediente
    /*#[On('marcarCitaComoAceptada')]
    public function marcarCitaComoAceptada($id)
    {
        $cita = Cita::findOrFail($id);

        try {
            DB::transaction(function () use ($cita) {
                // actualiza estado de la cita
                $cita->estado = 'aceptada';
                $cita->save();

                // Crear Orden de Servicio solo si no se ha creado previamente para esta cita
                $ordenExiste = ServiceOrder::where('cita_id', $cita->id)->exists();

                if (! $ordenExiste) {
                    // Consultar el servicio para obtener el precio base
                    $servicio = Service::find($cita->service_id);
                    $precioBase = $servicio?->precio_base ?? 0;

                    ServiceOrder::create([
                        'cliente_id'   => $cita->cliente_id,
                        'vehiculo_id'  => $cita->vehiculo_id,
                        'service_id'   => $cita->service_id,
                        'cita_id'      => $cita->id,
                        'estado'       => 'creada',
                        'precio_lista' => $precioBase,
                        'precio_final' => $precioBase,
                        'creado_por'   => Auth::id(),
                    ]);
                }
            });

            $this->dispatch('citaAceptada');

        } catch (\Throwable $e) {
            Log::error('Error al aceptar cita y generar la orden: ' . $e->getMessage(), [
                'cita_id' => $id,
                'exception' => $e
            ]);

            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Ocurrió un problema al aceptar la cita y crear la orden.', icono: 'error');
        }
    }*/   
    
    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $citas = Cita::with(['cliente', 'vehiculo', 'asesor', 'asesorExterno', 'sede'])
            // Si el usuario tiene el rol de Vendedor y no es Administrador ni Jefe de Taller,
            // filtramos solo sus citas asociadas.
            ->when($user->hasRole('Vendedor') && !$user->hasAnyRole(['Administrador del sistema', 'Cliente', 'Tecnico', 'Jefe de Taller', 'Almacen']), function ($query) use ($user) {
                $query->where('asesor_id', $user->id);
            })
            ->buscar($this->search)
            ->ordenar($this->sort, $this->direction)
            ->paginate($this->cant);

        $sedes = Sede::orderBy('nombre', 'asc')->get();
        
        // Se obtienen los asesores externos para el selector
        $asesoresExternos = AsesorExterno::orderBy('nombre', 'asc')->get();
        $servicios = Service::activos()->where('tipo', 'conversion')->get();
        //'servicios' => Service::activos()->where('tipo', 'conversion')->get(),

        return view('livewire.lista-citas', compact('citas', 'sedes', 'asesoresExternos', 'servicios'));
    }

    public function crearCita()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $docLimpio = trim($this->documento ?? '');
                $nombreLimpio = mb_strtoupper(trim($this->nombre));
                $apellidoLimpio = mb_strtoupper(trim($this->apellido ?? ''));

                // 1. Obtener o crear Cliente
                if (!empty($docLimpio)) {
                    // Si se ingresó documento, se busca o crea por documento
                    $cliente = Cliente::firstOrCreate(
                        ['documento' => $docLimpio],
                        [
                            'tipo_persona' => 'NATURAL',
                            'nombre'       => $nombreLimpio,
                            'apellido'     => $apellidoLimpio,
                            'telefono'     => trim($this->telefono ?? ''),
                            'email'        => trim($this->email ?? ''),
                            'direccion'    => mb_strtoupper(trim($this->direccion ?? '')),
                        ]
                    );
                } else {
                    // Si no se ingresó documento, se busca por nombre/apellido o se crea uno nuevo
                    $cliente = Cliente::where('nombre', $nombreLimpio)
                        ->where('apellido', $apellidoLimpio)
                        ->first();

                    if (!$cliente) {
                        $cliente = Cliente::create([
                            'tipo_persona' => 'NATURAL',
                            'documento'    => null,
                            'nombre'       => $nombreLimpio,
                            'apellido'     => $apellidoLimpio,
                            'telefono'     => trim($this->telefono ?? ''),
                            'email'        => trim($this->email ?? ''),
                            'direccion'    => mb_strtoupper(trim($this->direccion ?? '')),
                        ]);
                    }
                }

                // 2. Obtener o crear Vehículo
                $placaLimpia = mb_strtoupper(trim($this->placa));
                $vehiculo = Vehiculo::where('placa', $placaLimpia)->first();

                if (!$vehiculo) {
                    $vehiculo = Vehiculo::create([
                        'placa'  => $placaLimpia,
                        'marca'       => mb_strtoupper(trim($this->marca)),
                        'modelo'      => mb_strtoupper(trim($this->modelo)),
                        'anio'        => $this->anio,
                        'serie'       => mb_strtoupper(trim($this->serie)),
                        'color'       => mb_strtoupper(trim($this->color)),
                        'combustible' => mb_strtoupper(trim($this->combustible)),
                    ]);

                    // Asociar como Propietario Principal en la pivot cliente_vehiculo
                    $vehiculo->clientes()->attach($cliente->id, [
                        'es_principal' => true,
                        'relacion'     => 'PROPIETARIO',
                    ]);
                } else {
                    // Verificar si el cliente ya está vinculado a este vehículo
                    $yaAsociado = $vehiculo->clientes()->where('cliente_id', $cliente->id)->exists();

                    if (!$yaAsociado) {
                        $tienePrincipal = $vehiculo->clientes()->wherePivot('es_principal', true)->exists();

                        $vehiculo->clientes()->attach($cliente->id, [
                            'es_principal' => !$tienePrincipal,
                            'relacion'     => $tienePrincipal ? 'ASOCIADO' : 'PROPIETARIO',
                        ]);
                    }
                }

                // 3. Determinar el Asesor Externo (si aplica)
                $asesorExternoId = $this->is_externo ? $this->asesor_externo_id : null;

                // 4. Registrar la Cita con la estructura correcta
                Cita::create([
                    'cliente_id'        => $cliente->id,
                    'vehiculo_id'       => $vehiculo->id,
                    'asesor_id'         => Auth::id(),
                    'asesor_externo_id' => $asesorExternoId,
                    'sede_id'           => $this->sede_id,
                    'fecha_cita'        => Carbon::parse($this->fecha_cita),
                    'motivo'            => $this->motivo,
                    'estado'            => 'pendiente',
                ]);
            });

            $this->limpiarFormulario();
            $this->open = false;
            $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'La cita se programó correctamente.', icono: 'success');

        } catch (\Exception $e) {
            $this->dispatch('minAlert', titulo: '¡ERROR!', mensaje: 'No se pudo procesar la solicitud: ', icono: 'error');
        }
    }

    public function limpiarFormulario()
    {
        $this->reset([
            'documento', 'nombre', 'apellido', 'telefono', 'email', 'direccion',
            'placa', 'marca', 'modelo', 'anio', 'serie', 'color', 'combustible',
            'fecha_cita', 'motivo', 'is_externo', 'asesor_externo_id'
        ]);
        $this->sede_id = 1;
        $this->resetValidation();
    }
}

/*public function crearCita22()
    {
        $this->validate();

        // 1️ Buscar o crear cliente
        $cliente = Cliente::firstOrCreate(
            // Busca por documento
            ['documento' => $this->documento],
            // Crea si no existe
            [
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'telefono' => $this->telefono,
                'email' => $this->email,
                'direccion' => $this->direccion,
            ]
        );

        // 2️ Verificar si el vehículo ya existe por placa
        $vehiculoExistente = Vehiculo::with('clientes')->where('placa', $this->placa)->first();
        if ($vehiculoExistente) {
            // Si el vehículo existe, asignarlo
            $vehiculo = $vehiculoExistente;
            // Validar que pertenezca al mismo cliente via pivot
            if (!$vehiculo->clientes->contains($cliente->id)) {
                $this->dispatch('minAlert', titulo: '¡ERROR!', mensaje: 'El vehiculo ingresado pertenece a otro cliente.', icono: 'error');

                return;
            }
        } else {
            // Crear vehículo si no existe
            $vehiculo = Vehiculo::create([
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'anio' => $this->anio,
                'placa' => $this->placa,
                'combustible' => $this->combustible,
                'serie' => $this->serie,
                'color' => $this->color,
            ]);
            // Vincular al cliente como propietario principal
            $vehiculo->clientes()->attach($cliente->id, [
                'es_principal' => true,
                'relacion' => 'Propietario',
            ]);
        }

        // 3️ Determinar asesor interno o externo
        $asesor_id = $this->is_externo ? null : Auth::id();
        $asesor_externo_id = $this->is_externo ? $this->asesor_externo_id : null;

        // 4 Crear cita
        $cita = Cita::create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'asesor_id' => $asesor_id,
            'asesor_externo_id' => $asesor_externo_id,
            'fecha_cita' => Carbon::parse($this->fecha_cita),
            'motivo' => $this->motivo,
            'estado' => 'pendiente',
        ]);

        $this->open = false;
        $fechaFormateada = Carbon::parse($cita->fecha_cita)->translatedFormat('l, d F Y');
        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Se ha programado una cita para el '.$fechaFormateada, icono: 'success');
        // línea para refrescar la paginación
        $this->resetPage();
        $this->reset(['nombre', 'apellido', 'documento', 'telefono', 'email', 'direccion', 'marca', 'modelo', 'anio', 'placa', 'combustible', 'serie', 'color', 'fecha_cita', 'motivo', 'is_externo', 'asesor_externo_id']);
    }
*/
/*public function order($sort)
    {
        if ($this->sort === $sort) {
            $this->direction = $this->direction === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
    }
*/