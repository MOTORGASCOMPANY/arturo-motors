<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use App\Models\ReportePiezaNoEncajada;
use Illuminate\Support\Facades\Auth;

trait ConversionesDespachoPorCantidad
{
    public function confirmarCantidadAdicional()
    {
        $cantidad = (int) $this->cantidadAdicional;
        if ($cantidad <= 0) {
            $this->dispatch('minToast', titulo: 'Error', mensaje: 'Cantidad inválida.', icono: 'error');
            return;
        }

        try {
            $orden = $this->conversionSeleccionada;
            $item = ItemSerializado::find($this->piezaReemplazarId);
            if (!$orden || !$item) return;

            $sedeId = $this->sedeOperativa();

            // Valida stock en el servidor: la UI puede quedar desactualizada
            // (otra pestaña, doble click, movimiento de un tercero).
            $disponible = (int) $item->producto->stockSueltoEnSede($sedeId);
            if ($cantidad > $disponible) {
                $this->stockDisponible = $disponible;
                $this->dispatch('minToast', titulo: 'Sin stock', mensaje: "Disponible: {$disponible}. Solicitado: {$cantidad}.", icono: 'warning');
                return;
            }

            // Despacho almacén → orden: RESTA stock (salida), no suma
            MovimientoStock::registrar(
                $item->producto, 'salida', $cantidad, $orden->id, Auth::id(),
                "Cantidad adicional para orden #{$orden->id}", $sedeId
            );

            $nuevo = ItemSerializado::create([
                'producto_id' => $item->producto_id,
                'serie' => null,
                'estado' => 'asignado',
                'service_order_id' => $orden->id,
                'sede_id' => $sedeId,
                'atributos' => ['tipo' => 'cantidad', 'cantidad_solicitada' => $cantidad, 'creado_por' => 'almacen', 'creado_en' => now()->toDateTimeString()],
            ]);

            $motivo = !empty($this->observacion) ? $this->observacion : "Cantidad adicional x{$cantidad}";

            ReportePiezaNoEncajada::create([
                'service_order_id' => $orden->id,
                'item_no_encajado_id' => $nuevo->id,
                'tecnico_id' => $orden->tecnico_id,
                'motivo_no_encaja' => $motivo,
                'estado' => 'resuelto',
            ]);

            $this->dispatch('minToast', titulo: 'Agregado', mensaje: "x{$cantidad} piezas asignadas.", icono: 'success');
            $this->resetReemplazo();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minToast', titulo: 'Error', mensaje: $e->getMessage(), icono: 'error');
        }
    }

    public function cancelarReemplazo() { $this->resetReemplazo(); }
}
