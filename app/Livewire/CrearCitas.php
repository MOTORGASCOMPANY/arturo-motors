<?php

namespace App\Livewire;

use App\Models\AsesorExterno;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CrearCitas extends Component
{
    public $open = false;

    // Datos del cliente
    public $nombre;

    public $apellido;

    public $documento;

    public $telefono;

    public $email;

    public $direccion;

    // Datos del vehículo
    public $marca;

    public $modelo;

    public $anio;

    public $placa;

    public $combustible;

    public $serie;

    public $color;

    // Datos de la cita
    public $fecha_cita;

    public $motivo;

    // Control asesor interno/externo
    public $is_externo = false;           // checkbox: false = asesor interno (por defecto)

    public $asesor_externo_id = null;     // id seleccionado si is_externo = true

    public $asesores; // todos los asesores

    protected $rules = [
        // Cliente
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',
        'documento' => 'required|digits:8',
        'telefono' => 'nullable|digits:9',
        'email' => 'nullable|email|max:150',
        'direccion' => 'nullable|string|max:255',

        // Vehículo
        'marca' => 'required|string|max:50',
        'modelo' => 'required|string|max:50',
        'anio' => 'required|integer|min:1900|max:2100',
        'placa' => 'required|string|size:6',
        'combustible' => 'required|string|max:20',
        'serie' => 'nullable|string|max:50',
        'color' => 'nullable|string|max:50',

        // Cita
        // 'fecha_cita' => 'required|date|after_or_equal:today',
        'fecha_cita' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
        'motivo' => 'nullable|string|max:255',

        // Asesor externo: obligatorio solo si is_externo = 1 (checkbox true)
        'asesor_externo_id' => 'required_if:is_externo,1|nullable|exists:asesores_externos,id',
    ];

    public function mount()
    {
        $this->asesores = AsesorExterno::orderBy('nombre')->get();
    }

    public function crearCita()
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

        // 2️ Crear vehículo
        /*$vehiculo = Vehiculo::create([
            'cliente_id' => $cliente->id,
            'marca'      => $this->marca,
            'modelo'     => $this->modelo,
            'anio'       => $this->anio,
            'placa'      => $this->placa,
            'combustible' => $this->combustible,
            'serie'      => $this->serie,
            'color'      => $this->color,
        ]);*/

        // 2️ Verificar si el vehículo ya existe por placa
        $vehiculoExistente = Vehiculo::where('placa', $this->placa)->first();
        if ($vehiculoExistente) {
            // Si el vehículo existe, asignarlo
            $vehiculo = $vehiculoExistente;
            // Opcional: Validar que pertenezca al mismo cliente
            if ($vehiculo->cliente_id != $cliente->id) {
                $this->dispatch('minAlert', titulo: '¡ERROR!', mensaje: 'El vehiculo ingresado pertenece a otro cliente.', icono: 'error');

                return;
            }
        } else {
            // Crear vehículo si no existe
            $vehiculo = Vehiculo::create([
                'cliente_id' => $cliente->id,
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'anio' => $this->anio,
                'placa' => $this->placa,
                'combustible' => $this->combustible,
                'serie' => $this->serie,
                'color' => $this->color,
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
            // 'fecha_cita'  => $this->fecha_cita,
            'fecha_cita' => Carbon::parse($this->fecha_cita),
            'motivo' => $this->motivo,
            'estado' => 'pendiente',
        ]);

        $this->open = false;
        $fechaFormateada = Carbon::parse($cita->fecha_cita)->translatedFormat('l, d F Y');
        $this->dispatch('minAlert', titulo: '¡BUEN TRABAJO!', mensaje: 'Se ha programado una cita para el '.$fechaFormateada, icono: 'success');
        $this->dispatch('refrescarListaCitas');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.crear-citas');
    }
}
