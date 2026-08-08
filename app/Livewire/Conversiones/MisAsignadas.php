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

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->where('tecnico_id', Auth::id()) // auth()->id()
            ->when($this->estado === 'pendientes', function ($q) {
                $q->whereIn('estado', ['en_evaluacion', 'aprobado_conversion', 'en_conversion']);
            })
            ->orderBy('created_at')
            ->paginate(10);

        return view('livewire.conversiones.mis-asignadas', compact('ordenes'));
    }
}
