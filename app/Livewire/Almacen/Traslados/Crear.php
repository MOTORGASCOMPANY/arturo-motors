<?php

namespace App\Livewire\Almacen\Traslados;

use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\Sede;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Crear extends Component
{
    // Paso 1: Configuración
    public ?int $sedeDestinoId = null;
    public string $observaciones = '';

    // Paso 2: Tipo de kit
    public ?string $tipoKit = null; // '3RA' o '5TA'

    // Paso 3: Modo de envío
    public ?string $modoEnvio = null; // 'completo', 'incompleto', 'cantidad'
    public string $buscarItem = '';

    // Piezas por cantidad
    public ?int $productoCantidadId = null;
    public int $cantidadPieza = 1;
    public array $cantidadSeleccionados = [];

    // Carrito
    public array $itemsSeleccionados = [];

    // Checklist
    public bool $mostrarChecklist = false;
    public array $checklistComponentes = [];
    public bool $esKitCompleto = false;

    // Mapa kit producto_id => generacion
    protected array $mapaKits = [
        1 => '3RA',
        2 => '5TA',
    ];

    protected function sedeOrigenId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function getSedesProperty()
    {
        return Sede::activas()->where('id', '!=', $this->sedeOrigenId())->orderBy('nombre')->get();
    }

    public function seleccionarTipo(string $tipo)
    {
        $this->tipoKit = $tipo;
        $this->modoEnvio = null;
        $this->itemsSeleccionados = [];
        $this->buscarItem = '';
        $this->cantidadSeleccionados = [];
        $this->productoCantidadId = null;
        $this->cantidadPieza = 1;
    }

    public function seleccionarModo(string $modo)
    {
        $this->modoEnvio = $modo;
        $this->itemsSeleccionados = [];
        $this->buscarItem = '';
        $this->cantidadSeleccionados = [];
        $this->productoCantidadId = null;
        $this->cantidadPieza = 1;
    }

    // ── Piezas por cantidad ──

    public function getProductosCantidadProperty()
    {
        return Producto::where('activo', true)
            ->whereHas('items', function ($q) {
                $q->where('estado', 'en_stock')->where('sede_id', $this->sedeOrigenId());
            })
            ->get()
            ->filter(fn ($p) => !$p->categoria?->es_kit)
            ->values();
    }

    public function agregarPiezaCantidad()
    {
        $this->validate([
            'productoCantidadId' => 'required|exists:productos,id',
            'cantidadPieza' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($this->productoCantidadId);
        $disponible = $producto->stockEnSede($this->sedeOrigenId());

        $yaTiene = $this->cantidadSeleccionados[$this->productoCantidadId] ?? 0;

        if (($yaTiene + $this->cantidadPieza) > $disponible) {
            $this->addError('cantidadPieza', "Solo hay {$disponible} disponibles en la sede de origen.");
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
        if (empty($this->cantidadSeleccionados)) return collect();

        return Producto::whereIn('id', array_keys($this->cantidadSeleccionados))->get()
            ->map(function ($p) {
                $p->cantidad_solicitada = $this->cantidadSeleccionados[$p->id];
                return $p;
            });
    }

    // ── Kits completos (sellados) ──

    public function getKitsCompletosProperty()
    {
        if (!$this->tipoKit) return collect();

        $productoKitId = $this->tipoKit === '3RA' ? 1 : 2;

        return ItemSerializado::with('producto')
            ->where('producto_id', $productoKitId)
            ->where('estado', 'en_stock')
            ->where('sede_id', $this->sedeOrigenId())
            ->whereNull('kit_padre_id')
            ->when($this->buscarItem, function ($q) {
                $termino = $this->buscarItem;
                $q->where('serie', 'like', "%{$termino}%");
            })
            ->orderBy('id', 'desc')
            ->get();
    }

    // ── Piezas sueltas (de kits abiertos de este tipo) ──

    public function getPiezasSueltasProperty()
    {
        if (!$this->tipoKit) return collect();

        $productoKitId = $this->tipoKit === '3RA' ? 1 : 2;

        return ItemSerializado::with('producto', 'kitPadre.producto')
            ->where('estado', 'en_stock')
            ->where('sede_id', $this->sedeOrigenId())
            ->whereNotNull('kit_padre_id')
            ->whereHas('kitPadre', function ($q) use ($productoKitId) {
                $q->where('producto_id', $productoKitId);
            })
            ->when($this->buscarItem, function ($q) {
                $termino = $this->buscarItem;
                $q->where('serie', 'like', "%{$termino}%")
                    ->orWhereHas('producto', function ($p) use ($termino) {
                        $p->where('nombre', 'like', "%{$termino}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->get();
    }

    // ── Repuestos por cantidad ──

    public function getProductosRepuestoProperty()
    {
        return Producto::where('activo', true)
            ->where('categoria_id', '!=', null)
            ->whereHas('items', function ($q) {
                $q->where('estado', 'en_stock')->where('sede_id', $this->sedeOrigenId());
            })
            ->get()
            ->filter(fn ($p) => !$p->categoria?->es_kit)
            ->values();
    }

    public function agregarRepuesto()
    {
        $this->validate([
            'productoRepuestoId' => 'required|exists:productos,id',
            'cantidadRepuesto' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($this->productoRepuestoId);
        $disponible = $producto->stockEnSede($this->sedeOrigenId());

        if ($this->cantidadRepuesto > $disponible) {
            $this->addError('cantidadRepuesto', "Solo hay {$disponible} disponibles en la sede de origen.");
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

    // ── Toggle items ──

    public function toggleItem(int $itemId)
    {
        if (isset($this->itemsSeleccionados[$itemId])) {
            unset($this->itemsSeleccionados[$itemId]);
        } else {
            $this->itemsSeleccionados[$itemId] = true;
        }
    }

    // ── Carrito ──

    public function getItemsCarritoProperty()
    {
        if (empty($this->itemsSeleccionados)) return collect();

        return ItemSerializado::with('producto')
            ->whereIn('id', array_keys($this->itemsSeleccionados))
            ->get();
    }

    public function getResumenVacioProperty(): bool
    {
        return empty($this->itemsSeleccionados) && empty($this->cantidadSeleccionados);
    }

    // ── Confirmar ──

    public function confirmarTraslado()
    {
        $this->validate(['sedeDestinoId' => 'required|exists:sedes,id']);

        if ($this->resumenVacio) {
            $this->addError('general', 'Selecciona al menos un item antes de confirmar.');
            return;
        }

        $this->generarChecklist();
        $this->mostrarChecklist = true;
    }

    public function generarChecklist()
    {
        $componentes = [];

        // Items del carrito (kits completos + piezas sueltas)
        foreach (array_keys($this->itemsSeleccionados) as $itemId) {
            $item = ItemSerializado::with('producto.categoria')->find($itemId);
            if (!$item) continue;

            $componentes[] = [
                'nombre' => $item->producto->nombre,
                'tipo' => $item->kit_padre_id ? 'pieza_suelta' : 'kit_sellado',
                'incluido' => true,
                'detalle' => $item->kit_padre_id ? ($item->serie ?? 'Sin serie') : ('Caja #' . $item->id),
                'kit' => $item->kit_padre_id
                    ? ($item->kitPadre->producto->nombre ?? 'Kit abierto')
                    : $item->producto->nombre,
            ];
        }

        // Piezas por cantidad
        foreach ($this->cantidadSeleccionados as $productoId => $cantidad) {
            $producto = Producto::find($productoId);
            if (!$producto) continue;

            $componentes[] = [
                'nombre' => $producto->nombre,
                'tipo' => 'cantidad',
                'incluido' => true,
                'detalle' => "x{$cantidad}",
                'kit' => 'Pieza suelta',
            ];
        }

        $this->checklistComponentes = $componentes;
        $this->esKitCompleto = $this->modoEnvio === 'completo';
    }

    public function confirmarEnvio()
    {
        $this->mostrarChecklist = false;
        $sedeOrigenId = $this->sedeOrigenId();

        try {
            DB::transaction(function () use ($sedeOrigenId) {
                $traslado = Traslado::create([
                    'sede_destino_id' => $this->sedeDestinoId,
                    'enviado_por' => Auth::id(),
                    'observaciones' => $this->observaciones ?: null,
                    'es_kit_completo' => $this->modoEnvio === 'completo',
                    'componentes_faltantes' => null,
                ]);

                // Items serializados (kits completos + piezas sueltas)
                foreach (array_keys($this->itemsSeleccionados) as $itemId) {
                    $item = ItemSerializado::where('id', $itemId)
                        ->where('estado', 'en_stock')
                        ->where('sede_id', $sedeOrigenId)
                        ->lockForUpdate()
                        ->first();

                    if (!$item) {
                        throw new \RuntimeException('Uno de los items seleccionados ya no está disponible.');
                    }

                    $producto = Producto::find($item->producto_id);

                    $item->update(['sede_id' => $this->sedeDestinoId]);

                    // Registrar movimiento: salida de origen, entrada en destino
                    if ($producto) {
                        MovimientoStock::registrar($producto, 'salida', 1, null, Auth::id(), "Traslado #{$traslado->id}", $sedeOrigenId);
                        MovimientoStock::registrar($producto, 'entrada', 1, null, Auth::id(), "Traslado #{$traslado->id}", $this->sedeDestinoId);
                    }

                    TrasladoDetalle::create([
                        'traslado_id' => $traslado->id,
                        'producto_id' => $item->producto_id,
                        'item_serializado_id' => $item->id,
                        'cantidad' => null,
                    ]);
                }

                // Piezas por cantidad
                foreach ($this->cantidadSeleccionados as $productoId => $cantidad) {
                    $itemsPieza = ItemSerializado::where('producto_id', $productoId)
                        ->where('estado', 'en_stock')
                        ->where('sede_id', $sedeOrigenId)
                        ->lockForUpdate()
                        ->limit($cantidad)
                        ->get();

                    if ($itemsPieza->count() < $cantidad) {
                        $nombre = Producto::find($productoId)?->nombre;
                        throw new \RuntimeException("No hay stock suficiente de {$nombre} en la sede de origen.");
                    }

                    foreach ($itemsPieza as $itemPieza) {
                        $itemPieza->update(['sede_id' => $this->sedeDestinoId]);
                    }

                    // Registrar movimiento: salida de origen, entrada en destino
                    $producto = Producto::find($productoId);
                    if ($producto) {
                        MovimientoStock::registrar($producto, 'salida', $cantidad, null, Auth::id(), "Traslado #{$traslado->id}", $sedeOrigenId);
                        MovimientoStock::registrar($producto, 'entrada', $cantidad, null, Auth::id(), "Traslado #{$traslado->id}", $this->sedeDestinoId);
                    }

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
        return $this->redirect(route('almacen.traslados.listado'), navigate: true);
    }

    public function cerrarChecklist()
    {
        $this->mostrarChecklist = false;
    }

    public function render()
    {
        return view('livewire.almacen.traslados.crear');
    }
}
