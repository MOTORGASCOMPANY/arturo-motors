<?php

namespace App\Livewire\ServiceOrders;

use App\Models\ServiceOrder;
use App\Support\ChecklistEvaluacion;
use Livewire\Component;

class Detalle extends Component
{
    public ServiceOrder $orden;

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with([
            'cliente', 'vehiculo', 'service', 'tecnico', 'evaluadoPor', 'creadoPor',
            'items.producto.categoria',
            'movimientosStock.producto',
            'movimientosStock.usuario',
            'comprobante',
            'historialEstados.usuario',
        ])->findOrFail($ordenId);
    }

    public function render()
    {
        return view('livewire.service-orders.detalle', [
            'checklistGrupos' => ChecklistEvaluacion::grupos(),
        ]);
    }
}
