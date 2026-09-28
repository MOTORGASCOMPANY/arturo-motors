<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\ProductoStockSede;
use App\Models\Sede;

trait ResumenInventario
{
    public function getResumenInventarioProperty()
    {
        $sedeId = $this->filtroSedeId;
        $estado = $this->filtroEstado;

        $kitsQuery = ItemSerializado::with(['producto.categoria', 'sede', 'serviceOrder.cliente', 'piezasEnKit.producto.categoria'])
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->orderByDesc('created_at');

        if ($estado) {
            $kitsQuery->where('estado', $estado);
        }

        $kitsAll = $kitsQuery->get();

        $kitsSellados = $kitsAll->where('estado', 'en_stock')->groupBy('producto_id');
        $kitsIncompletos = $kitsAll->where('estado', 'abierto')->groupBy('producto_id');
        $kitsConsumidos = $kitsAll->where('estado', 'consumido')->groupBy('producto_id');
        $kitsCompletados = $kitsAll->where('estado', 'completado')->groupBy('producto_id');

        $sueltosSerializados = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas(
                'producto.categoria',
                fn ($q) => $q->where('es_kit', false)->where('es_serializado', true)
            )
            ->whereNull('kit_padre_id')
            // Solo disponibles: instalado/defectuoso/consumido no es pieza suelta
            ->where('estado', 'en_stock')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('producto_id');

        $sueltosCantidad = ProductoStockSede::with('producto.categoria', 'sede')
            ->whereHas(
                'producto.categoria',
                fn ($q) => $q->where('es_serializado', false)->where('es_kit', false)
            )
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->where('cantidad', '>', 0)
            ->get()
            ->map(function ($stock) use ($sedeId) {
                // Restar solo kits de LA MISMA sede de la fila de stock.
                // Si el filtro es "todas", restar el total global de kits
                // descuentaba piezas de Ancón al stock de Callao (y podían
                // quedar en 0 → no aparecían los componentes).
                $enKits = ItemSerializado::where('producto_id', $stock->producto_id)
                    ->whereNotNull('kit_padre_id')
                    ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                    ->where('sede_id', $stock->sede_id)
                    ->count();
                $stock->cantidad_suelta_real = max(0, $stock->cantidad - $enKits);
                return $stock;
            })
            ->filter(fn ($s) => $s->cantidad_suelta_real > 0);

        $instaladosCount = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->where('estado', 'consumido')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $completadosCount = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->where('estado', 'completado')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        // Productos del catálogo SIN stock suelto en la sede filtrada (alerta roja).
        $sedesActivas = Sede::activas()->orderBy('id')->get();
        $sinStock = Producto::with('categoria')
            ->where('activo', true)
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->where('nombre', 'like', "%{$this->busquedaInventario}%")
            )
            ->get()
            ->filter(function ($p) use ($sedeId, $sedesActivas) {
                if ($sedeId) {
                    return $p->stockSueltoEnSede((int) $sedeId) <= 0;
                }
                // "Todas las sedes": sin stock en ninguna.
                return $sedesActivas->sum(fn ($s) => $p->stockSueltoEnSede($s->id)) <= 0;
            })
            ->values();

        return [
            'kitsSellados' => $kitsSellados,
            'kitsIncompletos' => $kitsIncompletos,
            'kitsConsumidos' => $kitsConsumidos,
            'kitsCompletados' => $kitsCompletados,
            'sueltosSerializados' => $sueltosSerializados,
            'sueltosCantidad' => $sueltosCantidad,
            'sinStock' => $sinStock,
            'conteos' => [
                'sellados' => $kitsSellados->flatten()->count(),
                'incompletos' => $kitsIncompletos->flatten()->count(),
                'consumidos' => $kitsConsumidos->flatten()->count(),
                'completados' => $kitsCompletados->flatten()->count(),
                'instalados' => $instaladosCount,
                'sueltosSerializados' => $sueltosSerializados->count(),
                'sueltosCantidadTipos' => $sueltosCantidad->count(),
                'sueltosCantidadTotal' => $sueltosCantidad->sum('cantidad_suelta_real'),
                'sinStock' => $sinStock->count(),
            ],
        ];
    }

    public function getKitsProperty()
    {
        $sedeId = $this->filtroSedeId;

        $kits = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->orderByDesc('created_at')
            ->get();

        return [
            'sellados' => $kits->where('estado', 'en_stock')->groupBy('producto_id'),
            'incompletos' => $kits->where('estado', 'abierto')->groupBy('producto_id'),
            'completados' => $kits->where('estado', 'completado')->groupBy('producto_id'),
            'consumidos' => $kits->where('estado', 'consumido')->groupBy('producto_id'),
        ];
    }
}