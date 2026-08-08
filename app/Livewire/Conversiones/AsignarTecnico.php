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

        $orden = ServiceOrder::findOrFail($ordenId);

        if ($orden->estado !== 'creada') {
            $this->addError("tecnico.$ordenId", 'Esta orden ya fue procesada por otro usuario.');
            return;
        }

        try {
            $orden->update([
                'tecnico_id' => $tecnicoId,
                'estado' => 'en_evaluacion',
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->addError("tecnico.$ordenId", 'Ocurrió un error al asignar. Intenta de nuevo.');
            return;
        }

        session()->flash('mensaje', "Orden #{$ordenId} asignada correctamente.");
    }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->whereHas('service', fn ($q) => $q->where('tipo', 'conversion'))
            ->where('estado', 'creada')
            ->orderBy('created_at')
            ->paginate(10);

        // Ajusta el nombre del rol exactamente como lo creaste con Spatie
        $tecnicos = User::role('Técnico')->get();

        return view('livewire.conversiones.asignar-tecnico', compact('ordenes', 'tecnicos'));
    }
}
