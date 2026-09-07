<?php

namespace App\Livewire\Caja;

use App\Models\FisePago;
use App\Models\ServiceOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FiseAuditoria extends Component
{
    use WithPagination;

    public string $buscar = '';
    public string $estado = 'todos';
    public int $cant = 15;
    public string $fechaDesde = '';
    public string $fechaHasta = '';
    public float $montoMin = 0;
    public float $montoMax = 0;
    public ?int $tecnicoId = null;
    public int $diasPendientes = 0;
    public bool $soloSaldo = false;

    public bool $modalAbierto = false;
    public ?int $fisePagoId = null;
    public ?int $serviceOrderId = null;
    public float $montoTotal = 0;
    public float $montoPagado = 0;
    public float $montoPago = 0;
    public string $fechaPago = '';
    public string $observaciones = '';

    public function updating($property)
    {
        if (in_array($property, ['buscar', 'estado', 'cant', 'fechaDesde', 'fechaHasta', 'montoMin', 'montoMax', 'tecnicoId', 'diasPendientes', 'soloSaldo'])) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['buscar', 'estado', 'cant', 'fechaDesde', 'fechaHasta', 'montoMin', 'montoMax', 'tecnicoId', 'diasPendientes', 'soloSaldo']);
        $this->resetPage();
    }

    public function abrirModalPago(int $fisePagoId)
    {
        $fp = FisePago::with('serviceOrder.vehiculo', 'serviceOrder.cliente')->findOrFail($fisePagoId);

        $this->fisePagoId = $fp->id;
        $this->serviceOrderId = $fp->service_order_id;
        $this->montoTotal = (float) $fp->monto_total;
        $this->montoPagado = (float) $fp->monto_pagado;
        $this->montoPago = 0;
        $this->fechaPago = now()->format('Y-m-d');
        $this->observaciones = '';
        $this->modalAbierto = true;
    }

    public function registrarPago()
    {
        $this->validate([
            'montoPago'  => 'required|numeric|min:0.01|max:' . ($this->montoTotal - $this->montoPagado),
            'fechaPago'  => 'required|date',
        ], [
            'montoPago.required' => 'Ingresa el monto pagado.',
            'montoPago.min'      => 'El monto debe ser mayor a 0.',
            'montoPago.max'      => 'El monto excede el saldo pendiente.',
            'fechaPago.required' => 'Selecciona la fecha de pago.',
        ]);

        $fp = FisePago::findOrFail($this->fisePagoId);
        $nuevoTotal = $this->montoPagado + $this->montoPago;

        $fp->update([
            'monto_pagado'   => $nuevoTotal,
            'fecha_pago'     => $this->fechaPago,
            'pagado_por'     => Auth::id(),
            'estado'         => $nuevoTotal >= $this->montoTotal ? 'pagado' : 'parcial',
            'observaciones'  => $this->observaciones ?: $fp->observaciones,
        ]);

        $this->modalAbierto = false;
        $this->dispatch('minToast', titulo: '¡Pago registrado!', mensaje: 'Monto: S/ ' . number_format($this->montoPago, 2), icono: 'success');
    }

    public function marcarPagado(int $fisePagoId)
    {
        $fp = FisePago::findOrFail($fisePagoId);

        $fp->update([
            'monto_pagado' => $fp->monto_total,
            'fecha_pago'   => now()->format('Y-m-d'),
            'pagado_por'   => Auth::id(),
            'estado'       => 'pagado',
        ]);

        $this->dispatch('minToast', titulo: '¡Marcado como pagado!', mensaje: 'Placa: ' . ($fp->serviceOrder->vehiculo->placa ?? 'N/A'), icono: 'success');
    }

    public function render()
    {
        $pagos = FisePago::with(['serviceOrder.vehiculo', 'serviceOrder.cliente', 'serviceOrder.tecnico', 'pagadoPor'])
            ->when($this->buscar, function ($q) {
                $q->whereHas('serviceOrder.vehiculo', fn ($vq) => $vq->where('placa', 'like', "%{$this->buscar}%"))
                  ->orWhereHas('serviceOrder.cliente', function ($cq) {
                      $cq->where('nombre', 'like', "%{$this->buscar}%")
                         ->orWhere('apellido', 'like', "%{$this->buscar}%");
                  });
            })
            ->when($this->estado !== 'todos', fn ($q) => $q->where('estado', $this->estado))
            ->when($this->fechaDesde, fn ($q) => $q->where('created_at', '>=', $this->fechaDesde))
            ->when($this->fechaHasta, fn ($q) => $q->where('created_at', '<=', $this->fechaHasta . ' 23:59:59'))
            ->when($this->montoMin > 0, fn ($q) => $q->where('monto_total', '>=', $this->montoMin))
            ->when($this->montoMax > 0, fn ($q) => $q->where('monto_total', '<=', $this->montoMax))
            ->when($this->tecnicoId, fn ($q) => $q->whereHas('serviceOrder', fn ($sq) => $sq->where('tecnico_id', $this->tecnicoId)))
            ->when($this->diasPendientes > 0, function ($q) {
                $q->where('estado', '!=', 'pagado')
                  ->where('created_at', '<=', now()->subDays($this->diasPendientes));
            })
            ->when($this->soloSaldo, fn ($q) => $q->whereColumn('monto_total', '>', 'monto_pagado'))
            ->orderByDesc('created_at')
            ->paginate($this->cant);

        $totales = FisePago::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = "parcial" THEN 1 ELSE 0 END) as parciales,
            SUM(CASE WHEN estado = "pagado" THEN 1 ELSE 0 END) as pagados,
            SUM(monto_total) as total_monto,
            SUM(monto_pagado) as total_pagado
        ')->first();

        $tecnicos = \App\Models\User::role('Tecnico')->get();

        return view('livewire.caja.fise-auditoria', compact('pagos', 'totales', 'tecnicos'));
    }
}
