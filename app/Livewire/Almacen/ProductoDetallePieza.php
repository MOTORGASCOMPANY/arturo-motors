<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductoDetallePieza extends Component
{
    // Detalle de pieza serializada suelta (clic en fila del listado).
    public bool $modalDetallePiezaAbierto = false;
    public ?int $piezaSeleccionadaId = null;

    /** Abre el detalle de una pieza serializada suelta (origen de ingreso, registro y datos). */
    #[On('productos:detalle-pieza:abrir')]
    public function verDetallePieza(int $itemId): void
    {
        $this->piezaSeleccionadaId = $itemId;
        $this->modalDetallePiezaAbierto = true;
    }

    public function cerrarDetallePieza(): void
    {
        $this->modalDetallePiezaAbierto = false;
        $this->piezaSeleccionadaId = null;
    }

    /** Limpia el detalle cuando x-modal lo cierra (click fuera / Esc). */
    public function updatedModalDetallePiezaAbierto(bool $value): void
    {
        if (!$value) {
            $this->piezaSeleccionadaId = null;
        }
    }

    /**
     * Detalle de pieza serializada suelta.
     * Mapea en BD: origen de ingreso y quién registró desde
     * items_serializados.atributos (recepcion_fecha, registrado_por,
     * serie_registrada_por/en) + ledger movimientos_stock
     * ("Entrada de serie {serie}" → usuario_id).
     */
    public function getPiezaDetalleProperty()
    {
        if (!$this->piezaSeleccionadaId) return null;

        $item = ItemSerializado::with([
            'producto.categoria',
            'sede',
            'serviceOrder.cliente',
            'serviceOrder.vehiculo',
            'serviceOrder.tecnico',
            'kitPadre.producto',
        ])->find($this->piezaSeleccionadaId);

        if (!$item) return null;

        $atr = $item->atributos ?? [];

        // Ledger de entrada de ESTA serie (RegistrarEntrada).
        $movEntrada = null;
        if ($item->serie) {
            $movEntrada = \App\Models\MovimientoStock::where('tipo', 'entrada')
                ->where('motivo', "Entrada de serie {$item->serie}")
                ->orderBy('id')
                ->first();
        }

        $origen = match (true) {
            !empty($atr['recepcion_fecha'])   => 'Recepción',
            !empty($atr['agregado_a_kit'])    => 'Agregada a kit',
            $movEntrada !== null              => 'Entrada manual',
            !empty($atr['serie_registrada_en']) => 'Registro de serie',
            default                           => 'Alta en almacén',
        };

        $registradoPorId = $atr['registrado_por']
            ?? $atr['serie_registrada_por']
            ?? $movEntrada?->usuario_id;

        $registradoPor = $registradoPorId
            ? \App\Models\User::find($registradoPorId)
            : null;

        $fechaRegistro = $atr['serie_registrada_en']
            ?? $atr['recepcion_fecha']
            ?? $movEntrada?->created_at
            ?? $item->created_at;

        // Normaliza a string legible (fecha corta o fecha+hora según venga).
        try {
            $f = \Carbon\Carbon::parse($fechaRegistro);
            $fechaFmt = ($f->format('H:i:s') === '00:00:00')
                ? $f->format('d/m/Y')
                : $f->format('d/m/Y H:i');
        } catch (\Throwable) {
            $fechaFmt = (string) $fechaRegistro;
        }

        $item->setAttribute('_origen', $origen);
        $item->setAttribute('_registradoPor', $registradoPor?->name ?? '—');
        $item->setAttribute('_fechaRegistro', $fechaFmt);

        return $item;
    }

    public function render()
    {
        return view('livewire.almacen.producto-detalle-pieza', [
            'piezaDetalle' => $this->modalDetallePiezaAbierto ? $this->piezaDetalle : null,
        ]);
    }
}