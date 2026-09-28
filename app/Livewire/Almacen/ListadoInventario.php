<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\ProductoStockSede;

trait ListadoInventario
{
    public function getListadoInventarioProperty()
    {
        $sedeId = $this->filtroSedeId;
        $tipo = $this->filtroTipoInventario;

        if (!$tipo) return collect();

        $estadoMap = [
            'sellado' => 'en_stock',
            'incompleto' => 'abierto',
            'completado' => 'completado',
            'consumido' => 'consumido',
        ];

        if (isset($estadoMap[$tipo])) {
            return ItemSerializado::with(['producto.categoria', 'sede', 'serviceOrder.cliente', 'serviceOrder.tecnico', 'piezasEnKit.producto'])
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
                ->where('estado', $estadoMap[$tipo])
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->when(
                    $this->busquedaInventario,
                    fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"))
                )
                ->orderByDesc('created_at')
                ->get();
        }

        if ($tipo === 'sueltosSerializados') {
            return ItemSerializado::with(['producto.categoria', 'sede'])
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false)->where('es_serializado', true))
                ->whereNull('kit_padre_id')
                ->where('estado', 'en_stock')
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->when(
                    $this->busquedaInventario,
                    fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"))
                )
                ->orderByDesc('created_at')
                ->get();
        }

        if ($tipo === 'sueltosCantidad') {
            $sedeId = $this->filtroSedeId;

            // Stock suelto REAL = ProductoStockSede − kits de ESA MISMA sede.
            // Agrupar solo por producto_id con filtro "todas" restaba kits de
            // cualquier sede a cada fila → componentes desaparecían.
            $itemsEnKits = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
                ->whereNotNull('kit_padre_id')
                ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->get()
                ->groupBy(fn ($i) => $i->producto_id . ':' . $i->sede_id)
                ->map->count();

            return ProductoStockSede::with(['producto.categoria', 'sede'])
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->when(
                    $this->busquedaInventario,
                    fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"))
                )
                ->where('cantidad', '>', 0)
                ->get()
                ->map(function ($stock) use ($itemsEnKits) {
                    $enKits = $itemsEnKits[$stock->producto_id . ':' . $stock->sede_id] ?? 0;
                    $stock->cantidad_suelta_real = max(0, $stock->cantidad - $enKits);
                    return $stock;
                })
                ->filter(fn ($s) => $s->cantidad_suelta_real > 0)
                ->values();
        }

        return collect();
    }

    public function getListadoInventarioTituloProperty(): string
    {
        return match($this->filtroTipoInventario) {
            'sellado' => 'Kits sellados',
            'incompleto' => 'Kits incompletos',
            'completado' => 'Kits completados',
            'consumido' => 'Kits consumidos',
            'sueltosSerializados' => 'Piezas sueltas con serie',
            'sueltosCantidad' => 'Piezas sueltas por cantidad',
            default => 'Inventario',
        };
    }

    public function getListadoInventarioIconoProperty(): string
    {
        return match($this->filtroTipoInventario) {
            'sellado' => 'fa-box',
            'incompleto' => 'fa-box-open',
            'completado' => 'fa-check-circle',
            'consumido' => 'fa-fire',
            'sueltosSerializados' => 'fa-barcode',
            'sueltosCantidad' => 'fa-cubes',
            default => 'fa-boxes-stacked',
        };
    }

    public function getListadoInventarioColorProperty(): string
    {
        return match($this->filtroTipoInventario) {
            'sellado' => 'amber',
            'incompleto' => 'orange',
            'completado' => 'purple',
            'consumido' => 'red',
            'sueltosSerializados' => 'green',
            'sueltosCantidad' => 'indigo',
            default => 'gray',
        };
    }
}