<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use Livewire\Attributes\On;

trait TrasladoSeleccion
{
    /**
     * Alterna un kit en la selección.
     *
     * Los kits COMPLETOS se marcan/desmarcan directo. Los INCOMpletos no se
     * seleccionan todavía: se dispara `traslado-kit-incompleto`, app.blade abre
     * el SweetAlert2 de confirmación y, sólo si el usuario acepta, vuelve por
     * `traslado-kit-incompleto-ok` → confirmarKitIncompleto().
     *
     * Deseleccionar siempre pasa directo: no hay nada que confirmar.
     */
    public function toggleKit(int $kitId)
    {
        $kit = ItemSerializado::find($kitId);

        if (! $kit) {
            return;
        }

        if (isset($this->itemsSeleccionados[$kitId])) {
            $this->alternarSeleccionKit($kitId);

            return;
        }

        $receta = KitComponente::where('producto_kit_id', $kit->producto_id)->sum('cantidad_esperada');
        $presentes = ItemSerializado::where('kit_padre_id', $kit->id)
            ->where('estado', 'en_stock')
            ->count();

        // Sin receta no hay nada que comparar: se trata como completo
        // (mismo criterio que es_sellado en TrasladoCatalogo).
        if ($receta <= 0 || $presentes >= $receta) {
            $this->alternarSeleccionKit($kitId);

            return;
        }

        $this->dispatch(
            'traslado-kit-incompleto',
            kitId: $kitId,
            nombre: $kit->producto?->nombre ?? "Kit #{$kitId}",
            presentes: $presentes,
            esperadas: (int) $receta,
            faltantes: (int) $receta - $presentes,
        );
    }

    /**
     * Callback del Swal de app.blade. Sólo llega acá si el usuario confirmó.
     */
    #[On('traslado-kit-incompleto-ok')]
    public function confirmarKitIncompleto(int $kitId)
    {
        $kit = ItemSerializado::find($kitId);

        if (! $kit || isset($this->itemsSeleccionados[$kitId])) {
            return;
        }

        $this->alternarSeleccionKit($kitId);
    }

    /**
     * Selecciona el kit (y sus hijos en stock) o lo quita junto con sus hijos.
     */
    protected function alternarSeleccionKit(int $kitId): void
    {
        $hijosIds = ItemSerializado::where('kit_padre_id', $kitId)
            ->where('estado', 'en_stock')
            ->pluck('id');

        if (isset($this->itemsSeleccionados[$kitId])) {
            unset($this->itemsSeleccionados[$kitId]);

            foreach ($hijosIds as $hijoId) {
                unset($this->itemsSeleccionados[$hijoId]);
            }

            return;
        }

        $this->itemsSeleccionados[$kitId] = true;

        foreach ($hijosIds as $hijoId) {
            $this->itemsSeleccionados[$hijoId] = true;
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
        $hijosSeleccionados = $hijos->filter(fn ($h) => isset($this->itemsSeleccionados[$h->id]));

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
