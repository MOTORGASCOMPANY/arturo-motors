<?php

namespace App\Livewire\Almacen\Stock;

use App\Models\ItemSerializado;
use App\Models\Sede;
use Livewire\Component;

class Ver extends Component
{
    public ?int $filtroSedeId = null;
    public ?string $filtroEstado = null;
    public string $busqueda = '';
    public ?int $detalleProductoId = null;

    public function mount()
    {
        $this->filtroSedeId = null;
        $this->filtroEstado = null;
    }

    public function getResumenProperty()
    {
        $sedeId = $this->filtroSedeId;
        $estado = $this->filtroEstado;

        // Kits en stock
        $kits = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($this->busqueda, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busqueda}%"));
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('producto_id');

        // Items sueltos (Vaporizador, Computadora, Tanque)
        $sueltos = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto', function ($q) {
                $q->where(function ($qx) {
                    $qx->where('nombre', 'LIKE', '%Vaporizador%')
                       ->orWhere('nombre', 'LIKE', '%Computadora%')
                       ->orWhere('nombre', 'LIKE', '%Tanque%');
                });
            })
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($this->busqueda, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busqueda}%"));
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('producto_id');

        return [
            'kits' => $kits,
            'sueltos' => $sueltos,
        ];
    }

    public function getDetallesProperty()
    {
        if (!$this->detalleProductoId) return collect();

        $sedeId = $this->filtroSedeId;
        $estado = $this->filtroEstado;

        return ItemSerializado::with('producto', 'sede')
            ->where('producto_id', $this->detalleProductoId)
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('created_at')
            ->get();
    }

    public function verDetalle(int $productoId)
    {
        $this->detalleProductoId = $this->detalleProductoId === $productoId ? null : $productoId;
    }

    public function cerrarDetalle()
    {
        $this->detalleProductoId = null;
    }

    public function render()
    {
        return view('livewire.almacen.stock.ver', [
            'resumen' => $this->resumen,
            'detalles' => $this->detalles,
            'sedes' => Sede::activas()->get(),
        ]);
    }
}
