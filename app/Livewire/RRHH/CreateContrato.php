<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use App\Models\Contrato;
use App\Models\User;
use App\Models\Vacacion;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateContrato extends Component
{
    public $abierto = false;
    public $contratoId; // Para saber si estamos editando

    // Campos del formulario (Nombres en español)
    public $user_id, $fecha_ingreso, $fecha_inicio_contrato, $fecha_vencimiento;
    public $cargo, $tipo_contrato = 'Plazo Fijo', $sueldo_bruto, $sueldo_neto;
    public $status = 'Activo';

    protected function rules()
    {
        return [
            //'user_id' => 'required|exists:users,id',
            'user_id' => $this->contratoId
                ? 'required|exists:users,id'
                : 'required|exists:users,id|unique:contratos,user_id',
            'fecha_ingreso' => 'required|date',
            'fecha_inicio_contrato' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_inicio_contrato',
            'cargo' => 'required|string|max:150',
            'tipo_contrato' => 'required|string',
            'sueldo_bruto' => 'required|numeric|min:0',
            'sueldo_neto' => 'required|numeric|min:0',
            'status' => 'required|in:Activo,Vencido,Finalizado',
        ];
    }

    // Mensajes personalizados para la validación de contrato único
    protected $messages = [
        'user_id.unique' => 'Este empleado ya tiene un contrato registrado.',
    ];

    #[On('abrir-modal-crear')]
    public function abrirModal()
    {
        $this->reset();
        $this->resetValidation();
        $this->abierto = true;
    }

    #[On('editar-contrato')]
    public function editarContrato($id)
    {
        $this->resetValidation();
        $this->contratoId = $id;
        $contrato = Contrato::find($id);

        $this->user_id = $contrato->user_id;
        $this->fecha_ingreso = $contrato->fecha_ingreso->format('Y-m-d');
        $this->fecha_inicio_contrato = $contrato->fecha_inicio_contrato->format('Y-m-d');
        $this->fecha_vencimiento = $contrato->fecha_vencimiento ? $contrato->fecha_vencimiento->format('Y-m-d') : null;
        $this->cargo = $contrato->cargo;
        $this->tipo_contrato = $contrato->tipo_contrato;
        $this->sueldo_bruto = $contrato->sueldo_bruto;
        $this->sueldo_neto = $contrato->sueldo_neto;
        $this->status = $contrato->status;

        $this->abierto = true;
    }

    /*public function updatedStatus($value)
    {
        // Si el contrato se marca como Finalizado en status y no tiene fecha de vencimiento,
        // sugerimos la fecha actual como fecha de cierre.
        if ($value === 'Finalizado' && !$this->fecha_vencimiento) {
            $this->fecha_vencimiento = now()->format('Y-m-d');
        }
    }*/

    // Calcula los días de vacaciones según el periodo (ingreso a vencimiento).
    private function calcularDiasPorPeriodo($fechaIngreso, $fechaVencimiento)
    {
        if (!$fechaVencimiento) {
            return 0;
        }

        $inicio = Carbon::parse($fechaIngreso);
        $fin = Carbon::parse($fechaVencimiento);

        if ($fin->lt($inicio)) return 0;

        // Calculamos la diferencia total en días del contrato
        $diasPeriodo = $inicio->diffInDays($fin);

        // Regla: (Días del contrato * 15 días de ley) / 365 días del año
        $ganados = ($diasPeriodo * 15) / 365;

        return (int) round($ganados);
    }

    /*public function guardar()
    {
        $this->validate();

        $datos = [
            'user_id' => $this->user_id,
            'fecha_ingreso' => $this->fecha_ingreso,
            'fecha_inicio_contrato' => $this->fecha_inicio_contrato,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'cargo' => $this->cargo,
            'tipo_contrato' => $this->tipo_contrato,
            'sueldo_bruto' => $this->sueldo_bruto,
            'sueldo_neto' => $this->sueldo_neto,
            'status' => $this->status,
        ];

        $mensajeExito = "";

        DB::transaction(function () use ($datos, &$mensajeExito) {
            if ($this->contratoId) {
                $contrato = Contrato::find($this->contratoId);
                $contrato->update($datos);

                // Recalcular vacaciones por cambio de periodo
                if ($contrato->vacaciones) {
                    $nuevosGanados = $this->calcularDiasPorPeriodo($this->fecha_ingreso, $this->fecha_vencimiento);
                    $contrato->vacaciones->update([
                        'dias_ganados' => $nuevosGanados,
                        'dias_restantes' => $nuevosGanados - $contrato->vacaciones->dias_tomados
                    ]);
                }

                $mensajeExito = "Contrato actualizado con éxito.";
            } else {
                $nuevoContrato = Contrato::create($datos);

                // Cálculo basado en el periodo de contrato
                $diasGanados = $this->calcularDiasPorPeriodo($this->fecha_ingreso, $this->fecha_vencimiento);

                Vacacion::create([
                    'idContrato'     => $nuevoContrato->id,
                    'dias_ganados'   => $diasGanados,
                    'dias_tomados'   => 0,
                    'dias_restantes' => $diasGanados,
                ]);

                $mensajeExito = "Contrato y registro de vacaciones creados.";
            }
        });

        $this->abierto = false;
        $this->dispatch('refresh-tabla-contratos');
        $this->dispatch('minAlert', titulo: "¡HECHO!", mensaje: $mensajeExito, icono: "success");
    }*/

    public function guardar()
    {
        $this->validate();

        $datos = [
            'user_id' => $this->user_id,
            'fecha_ingreso' => $this->fecha_ingreso,
            'fecha_inicio_contrato' => $this->fecha_inicio_contrato,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'cargo' => $this->cargo,
            'tipo_contrato' => $this->tipo_contrato,
            'sueldo_bruto' => $this->sueldo_bruto,
            'sueldo_neto' => $this->sueldo_neto,
            'status' => $this->status,
        ];

        $mensajeExito = "";
        $tipoIcono = "success";

        try {
            DB::transaction(function () use ($datos, &$mensajeExito, &$tipoIcono) {
                // Calculamos los días ganados según el nuevo periodo
                $nuevosGanados = $this->calcularDiasPorPeriodo($this->fecha_ingreso, $this->fecha_vencimiento);

                if ($this->contratoId) {
                    $contrato = Contrato::with('vacaciones')->find($this->contratoId);

                    // VALIDACIÓN DE SEGURIDAD:
                    // Si ya tiene días tomados, el nuevo cálculo no puede ser menor a lo ya disfrutado.
                    if ($contrato->vacaciones && $nuevosGanados < $contrato->vacaciones->dias_tomados) {
                        throw new \Exception("No puedes reducir el periodo tanto. El empleado ya ha tomado {$contrato->vacaciones->dias_tomados} días.");
                    }

                    $contrato->update($datos);

                    if ($contrato->vacaciones) {
                        $contrato->vacaciones->update([
                            'dias_ganados' => $nuevosGanados,
                            'dias_restantes' => $nuevosGanados - $contrato->vacaciones->dias_tomados
                        ]);
                    }
                    $mensajeExito = "Contrato actualizado y vacaciones recalculadas.";
                } else {
                    $nuevoContrato = Contrato::create($datos);

                    Vacacion::create([
                        'idContrato'     => $nuevoContrato->id,
                        'dias_ganados'   => $nuevosGanados,
                        'dias_tomados'   => 0,
                        'dias_restantes' => $nuevosGanados,
                    ]);
                    $mensajeExito = "Contrato y registro de vacaciones creados correctamente.";
                }
            });
        } catch (\Exception $e) {
            $mensajeExito = $e->getMessage();
            $tipoIcono = "error";
            $this->dispatch('minAlert', titulo: "¡Atención!", mensaje: $mensajeExito, icono: $tipoIcono);
            return; // Detenemos la ejecución
        }

        $this->abierto = false;
        $this->dispatch('refresh-tabla-contratos');
        $this->dispatch('minAlert', titulo: "¡HECHO!", mensaje: $mensajeExito, icono: $tipoIcono);
    }

    public function render()
    {
        // Solo cargamos usuarios que no tengan contrato activo o todos para renovar
        $usuarios = User::orderBy('name', 'asc')->get();
        return view('livewire.r-r-h-h.create-contrato', compact('usuarios'));
    }
}
