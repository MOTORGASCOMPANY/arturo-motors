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
    
    public ?int $sedeDestinoId = null;
    public string $observaciones = '';

    
    public array $itemsSeleccionados = [];  
    public array $cantidadSeleccionados = []; 

    
    public string $buscar = '';

    
    public ?int $productoCantidadId = null;
    public int $cantidadPieza = 1;

    
    public ?int $kitInspeccionId = null;

    
    public bool $mostrarChecklist = false;
    public array $checklistData = [];

    
    public string $tabSeleccion = 'kits'; 

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

        
        $kitsPadres = ItemSerializado::with('producto')
            ->whereIn('id', $seleccionadosIds)
            ->whereNull('kit_padre_id')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->get();

        foreach ($kitsPadres as $kitItem) {
            $receta = KitComponente::with('componente')
                ->where('producto_kit_id', $kitItem->producto_id)
                ->get();

            
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

    
    
    

    public function confirmarEnvio()
    {
        $this->mostrarChecklist = false;
        $sedeOrigenId = $this->sedeOrigenId();

        $this->buildChecklist();
        $kitsEnChecklist = collect($this->checklistData)->where('tipo', 'kit');
        $esKitCompleto = $kitsEnChecklist->isNotEmpty()
            ? $kitsEnChecklist->every('es_completo', true)
            : null;

        try {
            DB::transaction(function () use ($sedeOrigenId, $esKitCompleto) {
                $traslado = Traslado::create([
                    'sede_destino_id' => $this->sedeDestinoId,
                    'enviado_por' => Auth::id(),
                    'observaciones' => $this->observaciones ?: null,
                    'es_kit_completo' => $esKitCompleto,
                ]);

                
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
