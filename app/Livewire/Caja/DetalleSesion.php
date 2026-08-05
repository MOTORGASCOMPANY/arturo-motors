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
            ->with(['usuario', 'serviceOrder.service'])
            ->when($this->tipo !== 'todos', fn ($q) => $q->where('tipo', $this->tipo))
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalIngresos = $this->sesion->movimientos()->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos = $this->sesion->movimientos()->where('tipo', 'egreso')->sum('monto');

        return view('livewire.caja.detalle-sesion', compact('movimientos', 'totalIngresos', 'totalEgresos'));
    }
}
