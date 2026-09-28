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
        return Sede::activas()
            ->where('id', '!=', $this->sedeOrigenId())
            ->orderBy('nombre')
            ->get();
    }

    
    
    

    public function getKitsDisponiblesProperty()
    {
        $sedeId = $this->sedeOrigenId();

        $kits = ItemSerializado::with(['producto.categoria', 'sede'])
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->kitDisponible()
            ->where('sede_id', $sedeId)
            ->whereNull('kit_padre_id')
            ->when(
                $this->buscar,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->buscar}%")
                )
            )
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($kits as $kit) {
            
            $receta = KitComponente::with('componente')
                ->where('producto_kit_id', $kit->producto_id)
                ->get();

            $kit->receta = $receta;
            $kit->totalEsperado = $receta->sum('cantidad_esperada');

            
            $hijos = ItemSerializado::where('kit_padre_id', $kit->id)
                ->where('estado', 'en_stock')
                ->get();

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
        $sedeId = $this->sedeOrigenId();

        return ItemSerializado::with(['producto.categoria', 'sede'])
            ->where('estado', 'en_stock')
            ->where('sede_id', $sedeId)
            ->whereNull('kit_padre_id')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false)->where('es_serializado', true))
            ->when(
                $this->buscar,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->buscar}%")
                )
            )
            ->orderBy('created_at', 'desc')
            ->get();
    }

    
    
    

    public function getProductosCantidadProperty()
    {
        $sedeId = $this->sedeOrigenId();

        return Producto::with('categoria')
            ->where('activo', true)
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false)->where('es_serializado', false))
            ->whereHas(
                'stockPorSede',
                fn ($q) => $q->where('sede_id', $sedeId)->where('cantidad', '>', 0)
            )
            ->get()
            ->map(fn ($p) => tap($p, fn ($p) => $p->disponible = $p->stockSueltoEnSede($sedeId)))
            ->filter(fn ($p) => $p->disponible > 0)
            ->values();
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

        return Producto::whereIn('id', array_keys($this->cantidadSeleccionados))->get()
            ->map(fn ($p) => tap($p, fn ($p) => $p->cantidad_solicitada = $this->cantidadSeleccionados[$p->id]));
    }
}
