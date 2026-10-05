<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\Producto;
use App\Models\Sede;

trait TrasladoCatalogo
{
    public function getSedesProperty()
    {
        return Sede::excepto($this->sedeOrigenId())->get();
    }

    public function getKitsDisponiblesProperty()
    {
        $kits = ItemSerializado::kitsDisponiblesEn($this->sedeOrigenId(), $this->buscar)->get();

        foreach ($kits as $kit) {
            // Receta e hijos ya vienen eager-loaded desde el scope.
            $receta = $kit->producto->componentes;
            $hijos = $kit->piezasEnKit;

            $kit->receta = $receta;
            $kit->totalEsperado = $receta->sum('cantidad_esperada');

            $kit->hijosCount = $hijos->count();
            $kit->hijos = $hijos;
            $kit->es_sellado = $kit->hijosCount >= $kit->totalEsperado;

            $hijosPorProducto = $hijos->pluck('producto_id')->countBy()->toArray();
            $kit->hijosPorProducto = $hijosPorProducto;

            $componentesCompletos = 0;
            foreach ($receta as $r) {
                if (($hijosPorProducto[$r->producto_componente_id] ?? 0) >= $r->cantidad_esperada) {
                    $componentesCompletos++;
                }
            }
            $kit->componentesCompletos = $componentesCompletos;
            $kit->totalComponentes = $receta->count();
        }

        return $kits;
    }

    public function getPiezasSueltasProperty()
    {
        return ItemSerializado::piezasSueltasEnSede($this->sedeOrigenId(), $this->buscar)->get();
    }

    public function getProductosCantidadProperty()
    {
        return Producto::conCantidadDisponibleEn($this->sedeOrigenId());
    }

    public function agregarPiezaCantidad()
    {
        $this->validate([
            'productoCantidadId' => 'required|exists:productos,id',
            'cantidadPieza' => 'required|integer|min:1',
        ]);

        $producto = Producto::with('categoria')->find($this->productoCantidadId);
        $disponible = $producto->stockSueltoEnSede($this->sedeOrigenId());
        $yaTiene = $this->cantidadSeleccionados[$this->productoCantidadId] ?? 0;

        if (($yaTiene + $this->cantidadPieza) > $disponible) {
            $this->addError('cantidadPieza', "Solo hay {$disponible} disponibles.");
            return;
        }

        $this->cantidadSeleccionados[$this->productoCantidadId] = $yaTiene + $this->cantidadPieza;
        $this->reset(['productoCantidadId', 'cantidadPieza']);
        $this->cantidadPieza = 1;
    }

    public function quitarPiezaCantidad(int $productoId)
    {
        unset($this->cantidadSeleccionados[$productoId]);
    }

    public function getPiezasCantidadCarritoProperty()
    {
        if (empty($this->cantidadSeleccionados)) {
            return collect();
        }

        return Producto::porIds(array_keys($this->cantidadSeleccionados))->get()
            ->map(fn ($p) => tap($p, fn ($p) => $p->cantidad_solicitada = $this->cantidadSeleccionados[$p->id]));
    }
}
