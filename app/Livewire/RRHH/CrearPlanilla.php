<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use App\Models\Planilla;
use App\Models\Contrato;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CrearPlanilla extends Component
{
    public $abierto = false;
    public $periodo; // Inicia nulo

    public $lista_planilla = [];

    // Constante mensual, se dividirá en la lógica
    const MONTO_ASIGNACION_MENSUAL = 113.00;

    #[On('abrir-modal-planilla')]
    public function abrirModal()
    {
        // Limpiamos todo al abrir, no cargamos nada aún
        $this->reset(['periodo', 'lista_planilla']);
        $this->abierto = true;
    }

    // Solo cuando el usuario selecciona una fecha en el input date
    public function updatedPeriodo($value)
    {
        if ($value) {
            $this->cargarTrabajadores();
        } else {
            $this->lista_planilla = [];
        }
    }

    public function cargarTrabajadores()
    {
        // Solo buscamos en la BD si el periodo existe
        if (!$this->periodo) return;

        $contratosActivos = Contrato::with('user')
            ->where('status', 'Activo')
            ->get();

        $this->lista_planilla = [];

        foreach ($contratosActivos as $contrato) {

            // 1. Verificamos si ya existe planilla para este contrato en este periodo
            $existe = Planilla::where('contrato_id', $contrato->id)
                             ->whereDate('periodo', $this->periodo)
                             ->exists();

            if ($existe) continue; // Si ya existe, nos saltamos a este trabajador

            // 2. Cálculo dividido (Quincenal)
            $sueldoQuincenal = $contrato->sueldo_neto / 2;

            $tieneAsignacion = ($contrato->user->asignacion_familiar == 1);
            $asignacionQuincenal = $tieneAsignacion ? (self::MONTO_ASIGNACION_MENSUAL / 2) : 0;


            $this->lista_planilla[] = [
                'contrato_id'         => $contrato->id,
                'nombre'              => $contrato->user->name,
                'sueldo_base'         => number_format($sueldoQuincenal, 2, '.', ''),
                'asignacion_familiar' => number_format($asignacionQuincenal, 2, '.', ''),
                'horas_extras'        => 0,
                'movilidad'           => 0,
                'otros_ingresos'      => 0,
                'otros_descuentos'    => 0,
                'planilla'            => $contrato->user->beneficios,
                'numero_cuenta'       => $contrato->user->numero_cuenta,
                'observacion'         => '',
                'total'               => number_format($sueldoQuincenal + $asignacionQuincenal, 2, '.', ''),
                'banco'               => number_format($sueldoQuincenal + $asignacionQuincenal, 2, '.', ''),
                'efectivo'            => 0
            ];
        }

        if (empty($this->lista_planilla)) {
            $this->dispatch('minAlert', titulo: 'AVISO', mensaje: 'Todos los trabajadores ya tienen planilla en este periodo o no hay activos.', icono: 'info');
        }
    }

    public function updatedListaPlanilla($value, $key)
    {
        $parts = explode('.', $key);
        $index = $parts[0];

        if (!isset($this->lista_planilla[$index])) return;

        $this->recalcularFila($index);
    }
    /*private function recalcularFila($index)
    {
        $fila = &$this->lista_planilla[$index];

        $ingresos = floatval($fila['sueldo_base']) +
            floatval($fila['asignacion_familiar']) +
            floatval($fila['horas_extras']) +
            floatval($fila['movilidad']) +
            floatval($fila['otros_ingresos']);

        $fila['total'] = number_format($ingresos - floatval($fila['otros_descuentos']), 2, '.', '');
    }*/
    private function recalcularFila($index)
    {
        $fila = &$this->lista_planilla[$index];

        $s_base = floatval($fila['sueldo_base'] ?? 0);
        $asign = floatval($fila['asignacion_familiar'] ?? 0);
        $h_ext = floatval($fila['horas_extras'] ?? 0);
        $movil = floatval($fila['movilidad'] ?? 0);
        $otros = floatval($fila['otros_ingresos'] ?? 0);
        $dctos = floatval($fila['otros_descuentos'] ?? 0);

        // TOTAL: Todo sumado menos descuentos
        $totalVal = ($s_base + $asign + $h_ext + $movil + $otros) - $dctos;

        // BANCO: Todo excepto movilidad, menos descuentos
        $bancoVal = ($s_base + $asign + $h_ext + $otros) - $dctos;

        // EFECTIVO: Solo movilidad
        $efectivoVal = $movil;

        $fila['total'] = number_format($totalVal, 2, '.', '');
        $fila['banco'] = number_format($bancoVal, 2, '.', '');
        $fila['efectivo'] = number_format($efectivoVal, 2, '.', '');
    }

    public function guardarMasivo()
    {
        $this->validate([
            'periodo' => 'required|date',
            'lista_planilla' => 'required|array|min:1',
        ]);

        try {
            DB::transaction(function () {
                foreach ($this->lista_planilla as $item) {
                    Planilla::create([
                        'contrato_id'         => $item['contrato_id'],
                        'periodo'             => $this->periodo,
                        'sueldo_base'         => $item['sueldo_base'],
                        'asignacion_familiar' => $item['asignacion_familiar'],
                        'horas_extras'        => $item['horas_extras'],
                        'movilidad'           => $item['movilidad'],
                        'otros_ingresos'      => $item['otros_ingresos'],
                        'otros_descuentos'    => $item['otros_descuentos'],
                        'planilla'            => $item['planilla'],
                        'numero_cuenta'       => $item['numero_cuenta'],
                        'observacion'         => $item['observacion'],
                        'estado_pago'         => 0
                    ]);
                }
            });

            $this->abierto = false;
            $this->dispatch('refresh-planilla');
            $this->dispatch('minAlert', titulo: 'ÉXITO', mensaje: 'Planilla generada correctamente', icono: 'success');

        } catch (\Exception $e) {
            $this->dispatch('minAlert', titulo: 'ERROR', mensaje: 'Hubo un problema al guardar: ' . $e->getMessage(), icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.r-r-h-h.crear-planilla');
    }
}
