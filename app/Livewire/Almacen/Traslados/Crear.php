<?php

namespace App\Livewire\Almacen\Traslados;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
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
    // ── Step 1: Config ──
    public ?int $sedeDestinoId = null;
    public string $observaciones = '';

    // ── Step 2: Selection ──
    public array $itemsSeleccionados = [];  // [item_serializado_id => true]
    public array $cantidadSeleccionados = []; // [producto_id => qty]

    // ── Search ──
    public string $buscar = '';

    // ── Quantity form ──
    public ?int $productoCantidadId = null;
    public int $cantidadPieza = 1;

    // ── Kit inspection ──
    public ?int $kitInspeccionId = null;

    // ── Checklist modal ──
    public bool $mostrarChecklist = false;
    public array $checklistData = [];

    // ── Tab ──
    public string $tabSeleccion = 'kits'; // 'kits' | 'piezas' | 'cantidad'

    protected function sedeOrigenId(): int
    {
        return Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function getSedesProperty()
    {
        return Sede::activas()
            ->where('id', '!=', $this->sedeOrigenId())
            ->orderBy('nombre')
            ->get();
    }

    // ══════════════════════════════════════
    //  COMPUTED: Kits
    // ══════════════════════════════════════

    public function getKitsDisponiblesProperty()
    {
        $sedeId = $this->sedeOrigenId();

        $kits = ItemSerializado::with(['producto.categoria', 'sede'])
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->where('estado', 'en_stock')
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
            // Receta desde KitComponente (dinámico)
            $receta = KitComponente::with('componente')
                ->where('producto_kit_id', $kit->producto_id)
                ->get();

            $kit->receta = $receta;
            $kit->totalEsperado = $receta->sum('cantidad_esperada');

            // Hijos presentes (piezas que siguen dentro del kit)
            $hijos = ItemSerializado::where('kit_padre_id', $kit->id)
                ->where('estado', 'en_stock')
                ->get();

            $kit->hijosCount = $hijos->count();
            $kit->hijos = $hijos;
            $kit->es_sellado = $kit->hijosCount >= $kit->totalEsperado;

            // Conteo por producto de hijos
            $hijosPorProducto = $hijos->pluck('producto_id')->countBy()->toArray();
            $kit->hijosPorProducto = $hijosPorProducto;

            // Cuántos componentes están completos
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

    // ══════════════════════════════════════
    //  COMPUTED: Piezas sueltas
    // ══════════════════════════════════════

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

    // ══════════════════════════════════════
    //  COMPUTED: Piezas por cantidad
    // ══════════════════════════════════════

    public function getProductosCantidadProperty()
    {
        $sedeId = $this->sedeOrigenId();

        return Producto::where('activo', true)
            ->whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->whereHas(
                'stockPorSede',
                fn ($q) => $q->where('sede_id', $sedeId)->where('cantidad', '>', 0)
            )
            ->get()
            ->map(fn ($p) => tap($p, fn ($p) => $p->disponible = $p->stockEnSede($sedeId)));
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

    // ══════════════════════════════════════
    //  ACTIONS: Toggle selection
    // ══════════════════════════════════════

    /**
     * Toggle a kit: adds/removes the kit item AND all its children.
     */
    public function toggleKit(int $kitId)
    {
        $item = ItemSerializado::find($kitId);
        if (! $item) {
            return;
        }

        $isCurrentlySelected = isset($this->itemsSeleccionados[$kitId]);

        if ($isCurrentlySelected) {
            // Deselect: remove kit + all children
            unset($this->itemsSeleccionados[$kitId]);
            $childrenIds = ItemSerializado::where('kit_padre_id', $kitId)
                ->where('estado', 'en_stock')
                ->pluck('id');
            foreach ($childrenIds as $childId) {
                unset($this->itemsSeleccionados[$childId]);
            }
        } else {
            // Select: add kit + all children
            $this->itemsSeleccionados[$kitId] = true;
            $childrenIds = ItemSerializado::where('kit_padre_id', $kitId)
                ->where('estado', 'en_stock')
                ->pluck('id');
            foreach ($childrenIds as $childId) {
                $this->itemsSeleccionados[$childId] = true;
            }
        }
    }

    /**
     * Toggle a single child inside a kit (for incomplete kits).
     */
    public function toggleHijoKit(int $hijoId)
    {
        if (isset($this->itemsSeleccionados[$hijoId])) {
            unset($this->itemsSeleccionados[$hijoId]);
        } else {
            $this->itemsSeleccionados[$hijoId] = true;
        }
    }

    /**
     * Toggle a single loose piece.
     */
    public function togglePieza(int $itemId)
    {
        if (isset($this->itemsSeleccionados[$itemId])) {
            unset($this->itemsSeleccionados[$itemId]);
        } else {
            $this->itemsSeleccionados[$itemId] = true;
        }
    }

    /**
     * Expand/collapse kit component inspection.
     */
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

        // Group by product and track individual selection
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

    // ══════════════════════════════════════
    //  COMPUTED: Resumen
    // ══════════════════════════════════════

    public function getResumenVacioProperty(): bool
    {
        return empty($this->itemsSeleccionados) && empty($this->cantidadSeleccionados);
    }

    public function getSeleccionCountProperty(): int
    {
        // Count only top-level: kits (not their children) + loose pieces + quantity items
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
            // children are part of their parent kit, don't count separately
        }

        return $kitsSeleccionados + $sueltosSeleccionados + count($this->cantidadSeleccionados);
    }

    // ══════════════════════════════════════
    //  CONFIRM: Checklist
    // ══════════════════════════════════════

    public function confirmarTraslado()
    {
        $this->validate(['sedeDestinoId' => 'required|exists:sedes,id']);

        if ($this->resumenVacio) {
            $this->addError('general', 'Selecciona al menos un item antes de confirmar.');
            return;
        }

        $this->buildChecklist();
        $this->mostrarChecklist = true;
    }

    protected function buildChecklist(): void
    {
        $data = [];
        $seleccionadosIds = array_keys($this->itemsSeleccionados);

        // Kits seleccionados (padres)
        $kitsPadres = ItemSerializado::with('producto')
            ->whereIn('id', $seleccionadosIds)
            ->whereNull('kit_padre_id')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->get();

        foreach ($kitsPadres as $kitItem) {
            $receta = KitComponente::with('componente')
                ->where('producto_kit_id', $kitItem->producto_id)
                ->get();

            // Hijos que están en la selección
            $hijosSeleccionados = ItemSerializado::whereIn('id', $seleccionadosIds)
                ->where('kit_padre_id', $kitItem->id)
                ->pluck('producto_id')
                ->countBy()
                ->toArray();

            $componentes = $receta->map(fn ($r) => [
                'nombre' => $r->componente?->nombre ?? '—',
                'esperada' => $r->cantidad_esperada,
                'presente' => $hijosSeleccionados[$r->producto_componente_id] ?? 0,
                'completo' => ($hijosSeleccionados[$r->producto_componente_id] ?? 0) >= $r->cantidad_esperada,
            ]);

            $totalEsperado = $receta->sum('cantidad_esperada');
            $totalPresente = array_sum($hijosSeleccionados);

            $data[] = [
                'nombre' => $kitItem->producto->nombre,
                'tipo' => 'kit',
                'es_completo' => $totalPresente >= $totalEsperado && $totalEsperado > 0,
                'detalle' => "#{$kitItem->id}",
                'totalEsperado' => $totalEsperado,
                'totalPresente' => $totalPresente,
                'componentes' => $componentes,
            ];
        }

        // Piezas sueltas (padres sin kit, no kit)
        $sueltos = ItemSerializado::with('producto')
            ->whereIn('id', $seleccionadosIds)
            ->whereNull('kit_padre_id')
            ->whereDoesntHave('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->get();

        foreach ($sueltos as $pieza) {
            $data[] = [
                'nombre' => $pieza->producto->nombre,
                'tipo' => 'pieza',
                'detalle' => $pieza->serie ?? "Item #{$pieza->id}",
            ];
        }

        // Piezas por cantidad
        foreach ($this->cantidadSeleccionados as $productoId => $cantidad) {
            $producto = Producto::find($productoId);
            if (! $producto) {
                continue;
            }

            $data[] = [
                'nombre' => $producto->nombre,
                'tipo' => 'cantidad',
                'detalle' => "×{$cantidad}",
            ];
        }

        $this->checklistData = $data;
    }

    // ══════════════════════════════════════
    //  CONFIRM: Envío final
    // ══════════════════════════════════════

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
                ]);

                // Items serializados (kits + children + sueltos)
                foreach (array_keys($this->itemsSeleccionados) as $itemId) {
                    $item = ItemSerializado::where('id', $itemId)
                        ->where('estado', 'en_stock')
                        ->where('sede_id', $sedeOrigenId)
                        ->lockForUpdate()
                        ->first();

                    if (! $item) {
                        throw new \RuntimeException('Uno de los items ya no está disponible.');
                    }

                    $item->update(['sede_id' => $this->sedeDestinoId]);

                    $producto = Producto::find($item->producto_id);
                    if ($producto) {
                        MovimientoStock::registrar(
                            $producto, 'salida', 1, null, Auth::id(),
                            "Traslado #{$traslado->id}", $sedeOrigenId
                        );
                        MovimientoStock::registrar(
                            $producto, 'entrada', 1, null, Auth::id(),
                            "Traslado #{$traslado->id}", $this->sedeDestinoId
                        );
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
                        throw new \RuntimeException("No hay stock suficiente de {$nombre}.");
                    }

                    foreach ($itemsPieza as $itemPieza) {
                        $itemPieza->update(['sede_id' => $this->sedeDestinoId]);
                    }

                    $producto = Producto::find($productoId);
                    if ($producto) {
                        MovimientoStock::registrar(
                            $producto, 'salida', $cantidad, null, Auth::id(),
                            "Traslado #{$traslado->id}", $sedeOrigenId
                        );
                        MovimientoStock::registrar(
                            $producto, 'entrada', $cantidad, null, Auth::id(),
                            "Traslado #{$traslado->id}", $this->sedeDestinoId
                        );
                    }

                    TrasladoDetalle::create([
                        'traslado_id' => $traslado->id,
                        'producto_id' => $productoId,
                        'cantidad' => $cantidad,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            $this->addError('general', $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al registrar el traslado.');
            return;
        }

        $this->dispatch('minAlert', titulo: 'Listo!', mensaje: 'Traslado registrado correctamente.', icono: 'success');

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
