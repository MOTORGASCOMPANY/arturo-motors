<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use Livewire\Component;
use Livewire\WithPagination;

class EntregaPendientes extends Component
{
    use WithPagination;

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service'])
            ->where('estado', 'conversion_completada')
            //->orderBy('fecha_fin_conversion')
            ->orderBy('fecha_fin_conversion', 'desc')
            ->paginate(10);

        return view('livewire.conversiones.entrega-pendientes', compact('ordenes'));
    }
}
