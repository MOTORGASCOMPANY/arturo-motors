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
    public string $metodoPago = 'todos';

    public function updating($property)
    {
        if (in_array($property, ['tipo', 'metodoPago'])) {
            $this->resetPage();
        }
    }

    public function mount(int $sesionId)
    {
        $this->sesion = SesionCaja::with(['abiertaPor', 'cerradaPor'])->findOrFail($sesionId);
    }

    private function ingresosPorMetodo(string $metodo): float
    {
        return $this->sesion->movimientos()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', $metodo)
            ->sum('monto');
    }

    public function render()
    {
        $movimientos = $this->sesion->movimientos()
            ->with(['usuario', 'serviceOrder.service'])
            ->where('metodo_pago', '!=', 'fise')
            ->when($this->tipo !== 'todos', fn ($q) => $q->where('tipo', $this->tipo))
            ->when($this->metodoPago !== 'todos', fn ($q) => $q->where('metodo_pago', $this->metodoPago))
            ->orderByDesc('created_at')
            ->paginate(20);

        // Totales INCLUYEN FISE (es ingreso del taller)
        $totalIngresos = $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto');

        $efectivo     = $this->ingresosPorMetodo('efectivo');
        $tarjeta      = $this->ingresosPorMetodo('tarjeta');
        $transferencia = $this->ingresosPorMetodo('transferencia');
        $otro         = $this->ingresosPorMetodo('otro');

        return view('livewire.caja.detalle-sesion', compact(
            'movimientos', 'totalIngresos', 'totalEgresos',
            'efectivo', 'tarjeta', 'transferencia', 'otro'
        ));
    }
}
