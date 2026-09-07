<?php

namespace App\Livewire\Almacen\Traslados;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\Producto;
use App\Models\ProductoStockSede;
use App\Models\Sede;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Crear extends Component
{
    public ?int $sedeDestinoId = null;
    public string $observaciones = '';

    public string $buscarItem = '';
    public array $itemsSeleccionados = [];

    public ?int $productoRepuestoId = null;
    public int $cantidadRepuesto = 1;
    public array $repuestosSeleccionados = [];

    public ?int $kitParaArmar = null;
    public array $faltantesKit = [];


    public function getSedesProperty()
    {
        return Sede::activas()->where('id', '!=', 1)->orderBy('nombre')->get();
    }

    /*public function getItemsDisponiblesProperty()
    {
        return ItemSerializado::with('producto.categoria')
            ->where('estado', 'en_stock')
            ->where('sede_id', 1)
            ->when($this->buscarItem, function ($q) {
                $termino = $this->buscarItem;
                $q->where('serie', 'like', "%{$termino}%")
                  ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$termino}%"));
            })
            ->limit(15)
            ->get();
    }*/

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
        return ItemSerializado::with('producto')->whereIn('id', array_keys($this->itemsSeleccionados))->get();
    }

    public function getProductosRepuestoProperty()
    {
        return Producto::whereHas('stockPorSede', fn ($q) => $q->where('sede_id', 1)->where('cantidad', '>', 0))->get();
    }

    public function agregarRepuesto()
    {
        $this->validate([
            'productoRepuestoId' => 'required|exists:productos,id',
            'cantidadRepuesto' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($this->productoRepuestoId);
        $disponible = $producto->stockEnSede(1);

        if ($this->cantidadRepuesto > $disponible) {
            $this->addError('cantidadRepuesto', "Solo hay {$disponible} disponibles en Arturo Motors.");
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

    public function confirmarTraslado()
    {
        $this->validate(['sedeDestinoId' => 'required|exists:sedes,id']);

        if (empty($this->itemsSeleccionados) && empty($this->repuestosSeleccionados)) {
            $this->addError('general', 'Selecciona al menos un equipo o repuesto antes de confirmar.');
            return;
        }

        try {
            DB::transaction(function () {
                $traslado = Traslado::create([
                    'sede_destino_id' => $this->sedeDestinoId,
                    'enviado_por' => Auth::id(),
                    'observaciones' => $this->observaciones ?: null,
                ]);

                foreach (array_keys($this->itemsSeleccionados) as $itemId) {
                    $item = ItemSerializado::where('id', $itemId)
                        ->where('estado', 'en_stock')
                        ->where('sede_id', 1)
                        ->lockForUpdate()
                        ->first();

                    if (!$item) {
                        throw new \RuntimeException('Uno de los equipos seleccionados ya no está disponible en Arturo Motors.');
                    }

                    $item->update(['sede_id' => $this->sedeDestinoId]);

                    // Si este ítem es un kit y ya tiene su reductor registrado (aunque siga sellado),
                    // el reductor se mueve de sede junto con la caja que lo contiene
                    ItemSerializado::where('kit_padre_id', $item->id)->update(['sede_id' => $this->sedeDestinoId]);

                    TrasladoDetalle::create([
                        'traslado_id' => $traslado->id,
                        'producto_id' => $item->producto_id,
                        'item_serializado_id' => $item->id,
                        'cantidad' => null,
                    ]);
                }

                foreach ($this->repuestosSeleccionados as $productoId => $cantidad) {
                    $stockOrigen = ProductoStockSede::where('producto_id', $productoId)
                        ->where('sede_id', 1)
                        ->lockForUpdate()
                        ->first();

                    if (!$stockOrigen || $stockOrigen->cantidad < $cantidad) {
                        $nombre = Producto::find($productoId)?->nombre;
                        throw new \RuntimeException("No hay stock suficiente de {$nombre} en Arturo Motors.");
                    }

                    $stockOrigen->decrement('cantidad', $cantidad);

                    $stockDestino = ProductoStockSede::firstOrCreate(
                        ['producto_id' => $productoId, 'sede_id' => $this->sedeDestinoId],
                        ['cantidad' => 0]
                    );
                    $stockDestino->increment('cantidad', $cantidad);

                    TrasladoDetalle::create([
                        'traslado_id' => $traslado->id,
                        'producto_id' => $productoId,
                        'item_serializado_id' => null,
                        'cantidad' => $cantidad,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            $this->addError('general', $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al registrar el traslado. Intenta de nuevo.');
            return;
        }

        $this->dispatch('minAlert', titulo: '¡Listo!', mensaje: 'Traslado registrado correctamente.', icono: 'success');
        $this->redirect(route('almacen.traslados.listado'), navigate: true);
    }

    public function getKitsDisponiblesProperty()
    {
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_kit', true))->get();
    }

    public function agregarKitCompleto()
    {
        $this->validate(['kitParaArmar' => 'required|exists:productos,id']);
        $this->faltantesKit = [];

        $haySelladosDeEsteTipo = ItemSerializado::where('producto_id', $this->kitParaArmar)
        ->where('estado', 'en_stock')
        ->where('sede_id', 1)
        ->exists();

        if ($haySelladosDeEsteTipo) {
            $this->dispatch('minAlert', titulo: 'Espera',
                mensaje: 'Este kit todavía tiene cajas selladas disponibles. Selecciónalo directo en "Kits sellados" arriba — es más simple y no requiere que esté abierto.',
                icono: 'info');
            return;
        }

        $componentes = KitComponente::with('componente.categoria')
            ->where('producto_kit_id', $this->kitParaArmar)
            ->get();

        if ($componentes->isEmpty()) {
            $this->addError('kitParaArmar', 'Este kit no tiene componentes definidos.');
            return;
        }

        foreach ($componentes as $kc) {
            $componente = $kc->componente;

            if ($componente->categoria->es_serializado) {
                // Busca una unidad disponible que aún no esté en el carrito
                $item = ItemSerializado::where('producto_id', $componente->id)
                    ->where('estado', 'en_stock')
                    ->where('sede_id', 1)
                    ->whereNotIn('id', array_keys($this->itemsSeleccionados))
                    ->first();

                if (!$item) {
                    $this->faltantesKit[] = $componente->nombre . ' (sin unidad disponible)';
                    continue;
                }

                $this->itemsSeleccionados[$item->id] = true;
            } else {
                $yaEnCarrito = $this->repuestosSeleccionados[$componente->id] ?? 0;
                $disponible = $componente->stockEnSede(1);
                $necesario = $yaEnCarrito + $kc->cantidad_esperada;

                if ($necesario > $disponible) {
                    $this->faltantesKit[] = "{$componente->nombre} (solo hay {$disponible}, se necesitan {$necesario})";
                    continue;
                }

                $this->repuestosSeleccionados[$componente->id] = $necesario;
            }
        }

        if (empty($this->faltantesKit)) {
            $this->dispatch('minToast', titulo: 'Kit agregado', mensaje: 'Se agregaron todos los componentes al traslado.', icono: 'success');
        } else {
            $this->dispatch('minAlert', titulo: 'Kit agregado parcialmente',
                mensaje: 'Faltó stock de: ' . implode(', ', $this->faltantesKit), icono: 'warning');
        }

        $this->kitParaArmar = null;
    }

    public function getKitsCerradosProperty()
    {
        return ItemSerializado::with('producto.categoria')
            ->where('estado', 'en_stock')
            ->where('sede_id', 1)
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($this->buscarItem, function ($q) {
                $termino = $this->buscarItem;
                $q->where('serie', 'like', "%{$termino}%")
                ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$termino}%"));
            })
            ->limit(15)
            ->get();
    }

    public function getEquiposIndividualesProperty()
    {
        return ItemSerializado::with('producto.categoria')
            ->where('estado', 'en_stock')
            ->where('sede_id', 1)
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false))
            ->when($this->buscarItem, function ($q) {
                $termino = $this->buscarItem;
                $q->where('serie', 'like', "%{$termino}%")
                ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$termino}%"));
            })
            ->limit(15)
            ->get();
    }
    
    public function render()
    {
        return view('livewire.almacen.traslados.crear');
    }
}
