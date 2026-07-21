<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use App\Models\Contrato;
use App\Models\Vacacion;
use App\Models\VacacionAsignada;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class AsignarVacacion extends Component
{
    public $abierto = false;
    public $asignacionId; // Si existe, estamos editando
    public $idContrato;
    public $contrato;
    public $vacacionMaestra;

    // Campos del formulario
    public $tipo = 'Vacaciones';
    public $razon, $d_tomados, $f_inicio, $observacion, $especial = false;

    protected function rules()
    {
        return [
            'tipo' => 'required|string',
            'razon' => 'nullable|string|max:255',
            'd_tomados' => [
                'required', 'integer', 'min:1',
                function ($attribute, $value, $fail) {
                    if (!$this->especial && $this->vacacionMaestra) {
                        $original = 0;
                        if ($this->asignacionId) {
                            $asig = VacacionAsignada::find($this->asignacionId);
                            // Solo recuperamos para el cálculo si el registro original descontaba saldo
                            $original = ($asig && !$asig->especial) ? $asig->d_tomados : 0;
                        }
                        $disponibleReal = $this->vacacionMaestra->dias_restantes + $original;

                        if ($value > $disponibleReal) {
                            $fail("El empleado solo tiene {$disponibleReal} días disponibles.");
                        }
                    }
                }
            ],
            'f_inicio' => 'required|date',
            'observacion' => 'nullable|string',
            'especial' => 'boolean',
        ];
    }

    // Escucha el evento desde el componente padre
    #[On('abrir-asignar-vacacion')]
    public function abrirModal($idContrato)
    {
        $this->reset(['razon', 'd_tomados', 'f_inicio', 'observacion', 'especial', 'asignacionId']);
        $this->resetValidation();

        $this->idContrato = $idContrato;
        $this->contrato = Contrato::with(['user', 'vacaciones'])->findOrFail($idContrato);
        $this->vacacionMaestra = $this->contrato->vacaciones;

        $this->abierto = true;
    }

    #[On('editar-asignacion')]
    public function editarAsignacion($id)
    {
        $this->resetValidation();
        $asignacion = VacacionAsignada::with('vacacion.contrato')->findOrFail($id);

        $this->asignacionId = $id;
        $this->tipo = $asignacion->tipo;
        $this->razon = $asignacion->razon;
        $this->d_tomados = $asignacion->d_tomados;
        $this->f_inicio = $asignacion->f_inicio->format('Y-m-d');
        $this->observacion = $asignacion->observacion;
        $this->especial = (bool)$asignacion->especial;

        $this->vacacionMaestra = $asignacion->vacacion;

        if ($this->vacacionMaestra) {
            $this->idContrato = $this->vacacionMaestra->idContrato;
            $this->contrato = Contrato::with('user')->find($this->idContrato);
            $this->abierto = true;
        } else {
            $this->dispatch('minAlert', titulo: "Error", mensaje: "No se encontró el registro maestro.", icono: "error");
        }
    }

    public function guardar()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Asegurar que el maestro esté actualizado desde la BD
                $this->vacacionMaestra = $this->vacacionMaestra->fresh();

                if ($this->asignacionId) {
                    $asignacion = VacacionAsignada::lockForUpdate()->find($this->asignacionId);

                    // 1. Revertir saldo anterior SOLO si el registro era REGULAR
                    if (!$asignacion->getOriginal('especial')) {
                        $this->vacacionMaestra->increment('dias_restantes', $asignacion->getOriginal('d_tomados'));
                        $this->vacacionMaestra->decrement('dias_tomados', $asignacion->getOriginal('d_tomados'));
                    }

                    // 2. Actualizar registro
                    $asignacion->update([
                        'tipo' => $this->tipo,
                        'razon' => $this->razon,
                        'd_tomados' => $this->d_tomados,
                        'f_inicio' => $this->f_inicio,
                        'observacion' => $this->observacion,
                        'especial' => $this->especial,
                    ]);

                } else {
                    // Crear nuevo
                    VacacionAsignada::create([
                        'idVacacion' => $this->vacacionMaestra->id,
                        'tipo' => $this->tipo,
                        'razon' => $this->razon,
                        'd_tomados' => $this->d_tomados,
                        'f_inicio' => $this->f_inicio,
                        'observacion' => $this->observacion,
                        'especial' => $this->especial,
                    ]);
                }

                // 3. Aplicar nuevo saldo SOLO si el nuevo estado es REGULAR
                if (!$this->especial) {
                    $this->vacacionMaestra->decrement('dias_restantes', $this->d_tomados);
                    $this->vacacionMaestra->increment('dias_tomados', $this->d_tomados);
                }

                $this->vacacionMaestra->save();
            });

            $this->abierto = false;
            $this->dispatch('refresh-gestionar-vacaciones');
            $this->dispatch('minAlert', titulo: "¡ÉXITO!", mensaje: "Procesado correctamente.", icono: "success");

        } catch (\Exception $e) {
            $this->dispatch('minAlert', titulo: "Error", mensaje: "Error: " . $e->getMessage(), icono: "error");
        }
    }

    /*public function guardar()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // 1. Crear el registro en vacacion_asignada
                VacacionAsignada::create([
                    'idVacacion' => $this->vacacionMaestra->id,
                    'tipo' => $this->tipo,
                    'razon' => $this->razon,
                    'd_tomados' => $this->d_tomados,
                    'f_inicio' => $this->f_inicio,
                    'observacion' => $this->observacion,
                    'especial' => $this->especial ? 1 : 0,
                ]);

                // 2. Actualizar el saldo en la tabla vacaciones SOLO si NO es especial
                if (!$this->especial) {
                    $this->vacacionMaestra->decrement('dias_restantes', $this->d_tomados);
                    $this->vacacionMaestra->increment('dias_tomados', $this->d_tomados);
                }
            });

            $this->abierto = false;
            // Refrescar el componente padre
            $this->dispatch('refresh-gestionar-vacaciones');
            $this->dispatch('minAlert', titulo: "¡ÉXITO!", mensaje: "Vacaciones asignadas.", icono: "success");

        } catch (\Exception $e) {
            $this->dispatch('minAlert', titulo: "Error", mensaje: "No se pudo procesar la asignación.", icono: "error");
        }
    }*/

    public function render()
    {
        return view('livewire.r-r-h-h.asignar-vacacion');
    }
}
