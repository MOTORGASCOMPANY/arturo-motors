<?php

namespace App\Livewire\ServiceOrders;

use App\Models\Comprobante;
use App\Models\MovimientoCaja;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $buscar = '';
    public ?string $desde = null;
    public ?string $hasta = null;
    public string $tipo = 'todos';

    public function limpiarFiltros(): void
    {
        $this->reset(['buscar', 'desde', 'hasta', 'tipo']);
        $this->resetPage();
    }

    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    public function updatedDesde(): void
    {
        $this->resetPage();
    }

    public function updatedHasta(): void
    {
        $this->resetPage();
    }

    public function updatedTipo(): void
    {
        $this->resetPage();
    }

    public function cancelar(int $ordenId): void
    {
        $orden = ServiceOrder::with(['service', 'comprobante'])->findOrFail($ordenId);

        // Solo cancelar servicios simples, no conversiones
        if ($orden->service->tipo === 'conversion') {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Las conversiones no se pueden cancelar desde aquí.', icono: 'error');
            return;
        }

        if ($orden->estado === 'cancelada') {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Esta orden ya fue cancelada.', icono: 'error');
            return;
        }

        try {
            DB::transaction(function () use ($orden) {
                // Eliminar comprobante si existe
                if ($orden->comprobante) {
                    Comprobante::where('service_order_id', $orden->id)->delete();
                }

                // Revertir movimiento de caja (eliminar el ingreso)
                MovimientoCaja::where('service_order_id', $orden->id)
                    ->where('tipo', 'ingreso')
                    ->delete();

                // Cambiar estado a cancelada (el Observer registra el historial)
                $orden->update(['estado' => 'cancelada']);
            });

            $this->dispatch('minToast', titulo: '¡Cancelada!', mensaje: "La orden #{$orden->id} fue cancelada. Se revertió el cobro en caja.", icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'No se pudo cancelar la orden. Intenta de nuevo.', icono: 'error');
        }
    }

    public function render()
    {
        $ordenes = ServiceOrder::with(['cliente', 'vehiculo', 'service', 'comprobante'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('cliente', function ($cq) {
                        $cq->where('nombre', 'like', "%{$this->buscar}%")
                            ->orWhere('apellido', 'like', "%{$this->buscar}%")
                            ->orWhere('documento', 'like', "%{$this->buscar}%");
                    })->orWhereHas('vehiculo', function ($vq) {
                        $vq->where('placa', 'like', "%{$this->buscar}%");
                    });
                });
            })
            ->when($this->desde, fn ($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->when($this->tipo !== 'todos', function ($q) {
                if ($this->tipo === 'conversion') {
                    $q->tipoConversion();
                } else {
                    $q->whereHas('service', fn ($sq) => $sq->where('tipo', 'simple'));
                }
            })
            ->latest()
            ->paginate(15);

        return view('livewire.service-orders.listado', compact('ordenes'));
    }
}
