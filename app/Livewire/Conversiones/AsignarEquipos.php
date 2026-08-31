<?php

namespace App\Livewire\Conversiones;

use App\Models\ServiceOrder;
use App\Models\ItemSerializado;
use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use App\Models\MovimientoStock;
use App\Models\ProductoStockSede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AsignarEquipos extends Component
{
    public ServiceOrder $orden;

    public string $buscarItem = '';
    public array $itemsSeleccionados = []; // [itemId => true]

    public ?int $productoRepuestoId = null;
    public int $cantidadRepuesto = 1;
    public array $repuestosSeleccionados = []; // [productoId => cantidad]

    public function mount(int $ordenId)
    {
        $this->orden = ServiceOrder::with(['cliente', 'vehiculo', 'service'])->findOrFail($ordenId);

        abort_unless($this->orden->estado === 'aprobado_conversion', 403, 'Esta orden no está en etapa de asignación de equipos.');
    }

    public function getItemsDisponiblesProperty()
    {
        return ItemSerializado::with('producto.categoria')
            ->where('estado', 'en_stock')
            ->where('sede_id', 1)
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', true))
            ->when($this->buscarItem, function ($q) {
                $termino = $this->buscarItem;
                $q->where('serie', 'like', "%{$termino}%")
                  ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$termino}%")
                                                         ->orWhere('marca', 'like', "%{$termino}%"));
            })
            ->limit(15)
            ->get();
    }

    public function toggleItem(int $itemId)
    {
        if (isset($this->itemsSeleccionados[$itemId])) {
            unset($this->itemsSeleccionados[$itemId]);
        } else {
            $this->itemsSeleccionados[$itemId] = true;
        }
    }

    public function getItemsCarritoProperty()
    {
        return ItemSerializado::with('producto')
            ->whereIn('id', array_keys($this->itemsSeleccionados))
            ->get();
    }

    public function getProductosRepuestoProperty()
    {
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_serializado', false))
            //->where('stock', '>', 0)
            ->whereHas('stockPorSede', fn ($q) => $q->where('sede_id', 1)->where('cantidad', '>', 0))
            ->get();
    }

    public function agregarRepuesto()
    {
        $this->validate([
            'productoRepuestoId' => 'required|exists:productos,id',
            'cantidadRepuesto' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($this->productoRepuestoId);
        $disponible = $producto->stockEnSede(1);

        /*if ($this->cantidadRepuesto > $producto->stock) {
            $this->addError('cantidadRepuesto', "Solo hay {$producto->stock} unidades en stock.");
            return;
        }*/
        if ($this->cantidadRepuesto > $disponible) {
            $this->addError('cantidadRepuesto', "Solo hay {$disponible} en stock en Arturo Motors.");
            return;
        }

        $this->repuestosSeleccionados[$this->productoRepuestoId] = $this->cantidadRepuesto;
        $this->reset(['productoRepuestoId', 'cantidadRepuesto']);
        $this->cantidadRepuesto = 1;
    }

    public function quitarRepuesto(int $productoId)
    {
        unset($this->repuestosSeleccionados[$productoId]);
    }

    public function getRepuestosCarritoProperty()
    {
        if (empty($this->repuestosSeleccionados)) return collect();

        return Producto::whereIn('id', array_keys($this->repuestosSeleccionados))->get()
            ->map(function ($p) {
                $p->cantidad_solicitada = $this->repuestosSeleccionados[$p->id];
                return $p;
            });
    }

    public function confirmarEntrega()
    {
        if (empty($this->itemsSeleccionados) && empty($this->repuestosSeleccionados)) {
            $this->addError('general', 'Selecciona al menos un equipo o repuesto antes de confirmar.');
            return;
        }

        try {
            DB::transaction(function () {
                // Bloqueo y validación de items serializados
                // justo antes de asignarlos, por si otro almacenero los tomó primero
                foreach (array_keys($this->itemsSeleccionados) as $itemId) {
                    $item = ItemSerializado::where('id', $itemId)
                        ->where('estado', 'en_stock')
                        ->where('sede_id', 1)
                        ->lockForUpdate()
                        ->first();

                    if (!$item) {
                        throw new \RuntimeException("Uno de los equipos seleccionados ya no está disponible. Actualiza la lista e intenta de nuevo.");
                    }

                    $item->asignarA($this->orden);
                }

                // Bloqueo y descontado de stock de repuestos por cantidad
                /*foreach ($this->repuestosSeleccionados as $productoId => $cantidad) {
                    $producto = Producto::where('id', $productoId)->lockForUpdate()->first();

                    if (!$producto || $producto->stock < $cantidad) {
                        throw new \RuntimeException("No hay stock suficiente de {$producto?->nombre}. Actualiza la lista e intenta de nuevo.");
                    }

                    MovimientoStock::registrar(
                        $producto, 'salida', $cantidad, $this->orden->id, Auth::id(),
                        'Entrega para conversión #' . $this->orden->id
                    );
                }*/

                foreach ($this->repuestosSeleccionados as $productoId => $cantidad) {
                    $stockSede = ProductoStockSede::where('producto_id', $productoId)
                        ->where('sede_id', 1)
                        ->lockForUpdate()
                        ->first();

                    if (!$stockSede || $stockSede->cantidad < $cantidad) {
                        $nombre = Producto::find($productoId)?->nombre;
                        throw new \RuntimeException("No hay stock suficiente de {$nombre}. Actualiza la lista e intenta de nuevo.");
                    }

                    MovimientoStock::registrar(
                        Producto::find($productoId), 'salida', $cantidad, $this->orden->id, Auth::id(),
                        'Entrega para conversión #' . $this->orden->id, 1
                    );
                }

                $this->orden->update(['estado' => 'en_conversion']);
            });
        } catch (\RuntimeException $e) {
            $this->addError('general', $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al confirmar la entrega. Intenta de nuevo.');
            return;
        }

        //session()->flash('mensaje', 'Equipos entregados. La orden pasó a conversión.');
        //$this->redirect(route('conversiones.almacen-pendientes'), navigate: true);

        // Usamos session()->flash para que el mensaje sobreviva a la redirección estándar HTTP
        session()->flash('swal', [
            'icono' => 'success',
            'titulo' => '¡ENTREGA CONFIRMADA!',
            'mensaje' => 'Equipos entregados. La orden pasó a conversión.',
        ]);

        $this->redirect(route('conversiones.almacen-pendientes'));
    }

    public function render()
    {
        return view('livewire.conversiones.asignar-equipos');
    }
}
