<?php

namespace App\Livewire;

use App\Models\Expediente;
use Livewire\Attributes\On;
use Livewire\Component;

class ExpedienteModal extends Component
{
    public $open = false; // controlar visibilidad del modal

    public $expediente;

    // Propiedades para los campos del formulario
    public $instalacion;

    public $cambio_tanque;

    public $revision;

    public $certificacion;

    public $servicio;

    public $cliente;

    public $dni;

    public $telefono_fijo;

    public $placa_actual;

    public $marca;

    public $modelo;

    public $anio;

    public $telefono_movil;

    public $placa_anterior;

    public $motor;

    public $color;

    public $combustible;

    public $inyectado;

    public $carburado;

    public $monopunto;

    public $motor_tipo;

    public $cil3;

    public $kilometraje;

    /**
     * Listener para abrir el modal y recibir el expediente.
     * #[On] le dice a Livewire que este método debe ejecutarse
     * cuando se emite un evento con el nombre 'open-expediente-modal'.
     *
     * @param  Expediente  $expediente  La instancia del modelo Expediente.
     * @return void
     */
    #[On('open-expediente-modal')]
    public function abrirModal(Expediente $expediente)
    {
        // Asignar el expediente recibido a la propiedad del componente
        $this->expediente = $expediente;
        $this->open = true;

        // Rellenar las propiedades del formulario con los datos del expediente
        if ($this->expediente) {
            $this->cliente = $this->expediente->cliente->nombre.' '.$this->expediente->cliente->apellido;
            $this->dni = $this->expediente->cliente->dni;
            $this->telefono_fijo = $this->expediente->cliente->telefono_fijo;
            $this->telefono_movil = $this->expediente->cliente->telefono_movil;
            $this->placa_actual = $this->expediente->vehiculo->placa;
            $this->placa_anterior = $this->expediente->vehiculo->placa_anterior;
            $this->marca = $this->expediente->vehiculo->marca;
            $this->modelo = $this->expediente->vehiculo->modelo;
            $this->motor = $this->expediente->vehiculo->motor;
            $this->color = $this->expediente->vehiculo->color;
            $this->anio = $this->expediente->vehiculo->anio;
            $this->combustible = $this->expediente->vehiculo->combustible;
            $this->kilometraje = $this->expediente->vehiculo->kilometraje;
        }
    }

    public function guardar() {}

    public function render()
    {
        return view('livewire.expediente-modal');
    }
}
