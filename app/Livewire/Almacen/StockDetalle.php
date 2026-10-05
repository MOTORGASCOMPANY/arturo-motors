<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\Sede;
use Livewire\Component;

class StockDetalle extends Component
{
    /** Productos que agrupan la sección "piezas sueltas" del resumen. */
    private const PRODUCTOS_SUELTOS = ['Vaporizador', 'Computadora', 'Tanque'];

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

        $kits = ItemSerializado::esKit()
            ->enSede($sedeId)
            ->enEstado($estado)
            ->buscarProducto($this->busqueda)
            ->with(['producto.categoria', 'sede'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('producto_id');

        $sueltos = ItemSerializado::conProductoEn(self::PRODUCTOS_SUELTOS)
            ->enSede($sedeId)
            ->enEstado($estado)
            ->buscarProducto($this->busqueda)
            ->with(['producto.categoria', 'sede'])
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

        return ItemSerializado::deProducto($this->detalleProductoId)
            ->enSede($this->filtroSedeId)
            ->enEstado($this->filtroEstado)
            ->with('producto', 'sede')
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
        return view('livewire.almacen.stock-detalle', [
            'resumen' => $this->resumen,
            'detalles' => $this->detalles,
            'sedes' => Sede::activas()->get(),
        ]);
    }
}
