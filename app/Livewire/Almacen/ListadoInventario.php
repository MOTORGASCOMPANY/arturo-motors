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
        $buscar = $this->busquedaInventario;

        if (!$tipo) return collect();

        $estadoMap = [
            'sellado' => 'en_stock',
            'incompleto' => 'abierto',
            'completado' => 'completado',
            'consumido' => 'consumido',
        ];

        if (isset($estadoMap[$tipo])) {
            return ItemSerializado::esKit()
                ->enEstado($estadoMap[$tipo])
                ->enSede($sedeId)
                ->buscarProducto($buscar)
                ->with([
                    'producto.categoria',
                    'sede',
                    'serviceOrder.cliente',
                    'serviceOrder.tecnico',
                    'piezasEnKit.producto',
                ])
                ->orderByDesc('created_at')
                ->get();
        }

        if ($tipo === 'sueltosSerializados') {
            return ItemSerializado::piezasSueltasEnSede($sedeId, $buscar)->get();
        }

        if ($tipo === 'sueltosCantidad') {
            return ProductoStockSede::sueltosPorCantidadEn($sedeId, $buscar);
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