<?php

namespace App\Livewire\RRHH;

use Livewire\Component;
use App\Models\Contrato;
use App\Models\VacacionAsignada;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class GestionarVacaciones extends Component
{
    use WithPagination;

    public $idContrato;
    public $contrato;

    public function mount($idContrato)
    {
        $this->idContrato = $idContrato;
        // Cargamos el contrato con su usuario y su registro maestro de vacaciones
        $this->contrato = Contrato::with(['user', 'vacaciones'])->findOrFail($idContrato);
    }

    #[On('refresh-gestionar-vacaciones')]
    public function refresh()
    {
        // Esto forzará el re-render y recargará $this->contrato y las asignaciones
        $this->contrato->load('vacaciones');
    }

    public function eliminarAsignacion($id)
    {
        $asignacion = VacacionAsignada::findOrFail($id);

        try {
            DB::transaction(function () use ($asignacion) {
                // Solo revertimos saldo si NO es una asignación especial
                if (!$asignacion->especial) {
                    $vacacionMaestra = $this->contrato->vacaciones;
                    $vacacionMaestra->increment('dias_restantes', $asignacion->d_tomados);
                    $vacacionMaestra->decrement('dias_tomados', $asignacion->d_tomados);
                }

                $asignacion->delete();
            });

            $this->dispatch('minAlert', titulo: "ELIMINADO", mensaje: "Registro eliminado y saldo revertido.", icono: "success");
            $this->refresh();

        } catch (\Exception $e) {
            $this->dispatch('minAlert', titulo: "Error", mensaje: "No se pudo eliminar el registro.", icono: "error");
        }
    }

    public function render()
    {
        // Obtenemos el detalle de días tomados (vacacion_asignada)
        // Usamos la relación a través de la tabla 'vacaciones'
        $idVacacionMaestra = $this->contrato->vacaciones->id ?? null;

        $asignaciones = $idVacacionMaestra
            ? VacacionAsignada::where('idVacacion', $idVacacionMaestra)
                ->orderBy('f_inicio', 'desc')
                ->paginate(10)
            : collect();

        return view('livewire.r-r-h-h.gestionar-vacaciones', [
            'asignaciones' => $asignaciones
        ]);
    }
}
