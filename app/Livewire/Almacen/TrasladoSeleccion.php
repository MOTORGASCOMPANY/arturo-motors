<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;

trait TrasladoSeleccion
{
    public function toggleKit(int $kitId)
    {
        $item = ItemSerializado::find($kitId);
        if (! $item) {
            return;
        }

        $isCurrentlySelected = isset($this->itemsSeleccionados[$kitId]);

        if ($isCurrentlySelected) {
            
            unset($this->itemsSeleccionados[$kitId]);
            $childrenIds = ItemSerializado::where('kit_padre_id', $kitId)
                ->where('estado', 'en_stock')
                ->pluck('id');
            foreach ($childrenIds as $childId) {
                unset($this->itemsSeleccionados[$childId]);
            }
        } else {
            
            $this->itemsSeleccionados[$kitId] = true;
            $childrenIds = ItemSerializado::where('kit_padre_id', $kitId)
                ->where('estado', 'en_stock')
                ->pluck('id');
            foreach ($childrenIds as $childId) {
                $this->itemsSeleccionados[$childId] = true;
            }
        }
    }

    
    public function toggleHijoKit(int $hijoId)
    {
        if (isset($this->itemsSeleccionados[$hijoId])) {
            unset($this->itemsSeleccionados[$hijoId]);
        } else {
            $this->itemsSeleccionados[$hijoId] = true;
        }
    }

    
    public function togglePieza(int $itemId)
    {
        if (isset($this->itemsSeleccionados[$itemId])) {
            unset($this->itemsSeleccionados[$itemId]);
        } else {
            $this->itemsSeleccionados[$itemId] = true;
        }
    }

    
    public function toggleInspeccion(int $kitId)
    {
        $this->kitInspeccionId = $this->kitInspeccionId === $kitId ? null : $kitId;
    }

    public function getKitInspeccionProperty()
    {
        if (! $this->kitInspeccionId) {
            return null;
        }

        $kit = ItemSerializado::with('producto')->find($this->kitInspeccionId);
        if (! $kit) {
            return null;
        }

        $receta = KitComponente::with('componente')
            ->where('producto_kit_id', $kit->producto_id)
            ->get();

        $hijos = ItemSerializado::where('kit_padre_id', $kit->id)
            ->where('estado', 'en_stock')
            ->get();

        
        $hijosPorProducto = $hijos->pluck('producto_id')->countBy()->toArray();
        $hijosSeleccionados = $hijos->filter(fn($h) => isset($this->itemsSeleccionados[$h->id]));

        return [
            'kit' => $kit,
            'receta' => $receta,
            'hijos' => $hijosPorProducto,
            'hijosItems' => $hijos,
            'hijosSeleccionadosCount' => $hijosSeleccionados->count(),
            'totalEsperado' => $receta->sum('cantidad_esperada'),
            'totalPresente' => array_sum($hijosPorProducto),
        ];
    }

    
    
    

    public function getResumenVacioProperty(): bool
    {
        return empty($this->itemsSeleccionados) && empty($this->cantidadSeleccionados);
    }

    public function getSeleccionCountProperty(): int
    {
        
        $kitsSeleccionados = 0;
        $sueltosSeleccionados = 0;

        foreach (array_keys($this->itemsSeleccionados) as $itemId) {
            $item = ItemSerializado::find($itemId);
            if (! $item) {
                continue;
            }
            if (is_null($item->kit_padre_id) && $item->producto?->categoria?->es_kit) {
                $kitsSeleccionados++;
            } elseif (is_null($item->kit_padre_id)) {
                $sueltosSeleccionados++;
            }
            
        }

        return $kitsSeleccionados + $sueltosSeleccionados + count($this->cantidadSeleccionados);
    }
}
