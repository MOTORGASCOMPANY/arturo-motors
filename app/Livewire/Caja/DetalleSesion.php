<?php

namespace App\Livewire\Caja;

use App\Models\SesionCaja;
use Livewire\Component;
use Livewire\WithPagination;

class DetalleSesion extends Component
{
    use WithPagination;

    public SesionCaja $sesion;
    public string $tipo = 'todos';

    public function mount(int $sesionId)
    {
        $this->sesion = SesionCaja::with(['abiertaPor', 'cerradaPor'])->findOrFail($sesionId);
    }

    public function render()
    {
        $movimientos = $this->sesion->movimientos()
            ->with(['usuario', 'serviceOrder.service', 'serviceOrder.comprobante'])
            ->when($this->tipo !== 'todos', fn ($q) => $q->where('tipo', $this->tipo))
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalIngresos = $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto');

        // Desglose por método de pago (solo ingresos)
        $efectivo = $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', fn ($q) => $q->where('metodo_pago', 'efectivo'))
            ->sum('monto');

        $tarjeta = $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', fn ($q) => $q->where('metodo_pago', 'tarjeta'))
            ->sum('monto');

        $transferencia = $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', fn ($q) => $q->where('metodo_pago', 'transferencia'))
            ->sum('monto');

        $otro = $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->whereHas('serviceOrder.comprobante', fn ($q) => $q->where('metodo_pago', 'otro'))
            ->sum('monto');

        return view('livewire.caja.detalle-sesion', compact(
            'movimientos', 'totalIngresos', 'totalEgresos',
            'efectivo', 'tarjeta', 'transferencia', 'otro'
        ));
    }
}
