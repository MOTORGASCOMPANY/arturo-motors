<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AsignarTecnico extends Component
{
    use WithPagination;

    public array $tecnicoSeleccionado = []; // [ordenId => tecnicoId]

    public function asignar(int $ordenId)
    {
        $tecnicoId = $this->tecnicoSeleccionado[$ordenId] ?? null;

        if (!$tecnicoId) {
            $this->addError("tecnico.$ordenId", 'Selecciona un técnico.');
            return;
        }

        $orden = ServiceOrder::find($ordenId);

        if (!$orden || $orden->estado !== 'creada') {
            $this->dispatch('minToast', titulo: '¡ATENCIÓN!', mensaje: 'Esta orden ya fue procesada o no está disponible.', icono: 'warning');
            return;
        }

        try {
            $orden->update([
                'tecnico_id' => $tecnicoId,
                'estado' => 'en_evaluacion',
            ]);

            // Limpiamos la selección de esa orden
            unset($this->tecnicoSeleccionado[$ordenId]);

            // Disparamos la notificación de éxito
            $this->dispatch('minToast', titulo: '¡TÉCNICO ASIGNADO!', mensaje: "Orden #{$ordenId} asignada correctamente.", icono: 'success');

        } catch (\Throwable $e) {
            report($e);
            //$this->addError("tecnico.$ordenId", 'Ocurrió un error al asignar. Intenta de nuevo.');
            //return;
            $this->dispatch('minToast', titulo: '¡ERROR!', mensaje: 'Ocurrió un error al asignar. Intenta de nuevo.', icono: 'error');
        }

        //session()->flash('mensaje', "Orden #{$ordenId} asignada correctamente.");
    }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
            ->where('estado', 'creada')
            ->orderBy('created_at')
            ->paginate(10);

        $tecnicos = User::role('Tecnico')->get();

        return view('livewire.conversiones.asignar-tecnico', compact('ordenes', 'tecnicos'));
    }
}
