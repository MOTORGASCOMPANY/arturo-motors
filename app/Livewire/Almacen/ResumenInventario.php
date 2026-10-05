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
        $buscar = $this->busquedaInventario;

        $kitsAll = ItemSerializado::esKit()
            ->enSede($sedeId)
            ->enEstado($estado)
            ->buscarProducto($buscar)
            ->with(['producto.categoria', 'sede', 'serviceOrder.cliente', 'piezasEnKit.producto.categoria'])
            ->orderByDesc('created_at')
            ->get();

        $kitsSellados = $kitsAll->where('estado', 'en_stock')->groupBy('producto_id');
        $kitsIncompletos = $kitsAll->where('estado', 'abierto')->groupBy('producto_id');
        $kitsConsumidos = $kitsAll->where('estado', 'consumido')->groupBy('producto_id');
        $kitsCompletados = $kitsAll->where('estado', 'completado')->groupBy('producto_id');

        // Solo disponibles: instalado/defectuoso/consumido no es pieza suelta.
        $sueltosSerializados = ItemSerializado::piezasSueltasEnSede($sedeId, $buscar)
            ->get()
            ->groupBy('producto_id');

        $sueltosCantidad = ProductoStockSede::sueltosPorCantidadEn($sedeId, $buscar);

        $instaladosCount = ItemSerializado::esKit()
            ->enSede($sedeId)
            ->enEstado('consumido')
            ->count();

        $completadosCount = ItemSerializado::esKit()
            ->enSede($sedeId)
            ->enEstado('completado')
            ->count();

        // Productos del catálogo SIN stock suelto en la sede filtrada (alerta roja).
        $sedesActivas = Sede::activas()->orderBy('id')->get();
        $sinStock = Producto::with('categoria')
            ->activo()
            ->buscadoPorNombre($buscar)
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
        $kits = ItemSerializado::esKit()
            ->enSede($this->filtroSedeId)
            ->buscarProducto($this->busquedaInventario)
            ->with(['producto.categoria', 'sede'])
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