<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MisAsignadas extends Component
{
    use WithPagination;

    public string $estado = 'pendientes'; // pendientes | todas

    /**
     * Resetea la paginación al cambiar la propiedad $estado
     */
    public function updatedEstado()
    {
        $this->resetPage();
    }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->where('tecnico_id', Auth::id())
            ->when($this->estado === 'pendientes', function ($q) {
                $q->whereIn('estado', ['en_evaluacion', 'aprobado_conversion', 'en_conversion']);
            })
            //->orderBy('created_at')
            ->latest() // Ordenar por lo más reciente
            ->paginate(10);

        return view('livewire.conversiones.mis-asignadas', compact('ordenes'));
    }
}
