<?php

namespace App\Livewire\Almacen;

use App\Livewire\Almacen\StockSuelto;
use App\Models\ItemSerializado;
use App\Models\ProductoStockSede;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductoCompletarKit extends Component
{
    use StockSuelto;

    public bool $modalCompletarKitAbierto = false;
    public int $completarKitItemId = 0;
    public int $completarKitSedeId = 0;
    public string $completarKitNombre = '';
    public array $completarKitComponentes = [];
    public array $completarKitSeleccion = [];

    /** Limpia el modal de completar kit cuando x-modal lo cierra. */
    public function updatedModalCompletarKitAbierto(bool $value): void
    {
        if ($value) {
            return;
        }

        $this->completarKitItemId = 0;
        $this->completarKitSedeId = 0;
        $this->completarKitNombre = '';
        $this->completarKitComponentes = [];
        $this->completarKitSeleccion = [];
    }

    #[On('productos:completar-kit:abrir')]
    public function abrirCompletarKit(int $kitItemId): void
    {
        $kit = ItemSerializado::with('producto')->find($kitItemId);

        if (!$kit) {
            return;
        }

        $this->completarKitItemId = $kitItemId;
        $this->completarKitSedeId = $kit->sede_id;
        $this->completarKitNombre = $kit->producto->nombre . ' #' . $kit->id;
        $this->completarKitSeleccion = [];

        $sedeId = $kit->sede_id;

        $receta = \Illuminate\Support\Facades\DB::table('kit_componentes')
            ->join('productos', 'producto_componente_id', '=', 'productos.id')
            ->join('categorias_almacen', 'productos.categoria_id', '=', 'categorias_almacen.id')
            ->where('producto_kit_id', $kit->producto_id)
            ->select(
                'productos.id as producto_id',
                'productos.nombre',
                'categorias_almacen.es_serializado',
                'kit_componentes.cantidad_esperada as cantidad'
            )
            ->get();

        foreach ($receta as $comp) {
            $this->completarKitSeleccion[$comp->producto_id] = [];
        }

        $piezasActuales = ItemSerializado::where('kit_padre_id', $kitItemId)
            ->whereNotIn('estado', ['defectuoso', 'devuelta_por_no_calzar'])
            ->pluck('producto_id')
            ->countBy()
            ->toArray();

        // No descontar KitPiezaExtraida: si la pieza volvió al kit (completarKit
        // o reparación), el descuento histórico la hacía volver a "faltante".
        $this->completarKitComponentes = $receta->map(function ($comp) use ($piezasActuales, $sedeId) {
            $faltan = max(0, $comp->cantidad - ($piezasActuales[$comp->producto_id] ?? 0));

            $disponibles = collect();
            if ($faltan > 0) {
                if ($comp->es_serializado) {
                    $disponibles = ItemSerializado::with('producto')
                        ->where('producto_id', $comp->producto_id)
                        ->where('estado', 'en_stock')
                        ->where('sede_id', $sedeId)
                        ->whereNull('kit_padre_id')
                        ->whereNotNull('serie')
                        ->where('serie', '!=', '')
                        ->orderBy('id')
                        ->get();
                } else {
                    $suelto = $this->sueltoDisponible($comp->producto_id, $sedeId);
                    $disponibles = $suelto > 0 ? ['stock' => $suelto] : collect();
                }
            }

            return [
                'producto_id' => $comp->producto_id,
                'nombre' => $comp->nombre,
                'es_serializado' => (bool) $comp->es_serializado,
                'cantidad_esperada' => $comp->cantidad,
                'faltan' => $faltan,
                'disponibles' => $disponibles,
            ];
        })->toArray();

        $this->modalCompletarKitAbierto = true;
    }

    public function cerrarCompletarKit(): void
    {
        $this->modalCompletarKitAbierto = false;
        // El resto de la limpieza lo hace updatedModalCompletarKitAbierto
    }

    public function toggleSeleccion(int $productoId, int $itemId): void
    {
        $current = $this->completarKitSeleccion[$productoId] ?? [];
        $idx = array_search($itemId, $current);

        if ($idx !== false) {
            unset($current[$idx]);
            $this->completarKitSeleccion[$productoId] = array_values($current);
            return;
        }

        $comp = null;
        foreach ($this->completarKitComponentes as $c) {
            if ($c['producto_id'] === $productoId) { $comp = $c; break; }
        }
        if (!$comp) return;

        $disponibles = $comp['disponibles'] instanceof \Illuminate\Support\Collection
            ? $comp['disponibles']
            : collect($comp['disponibles']);

        $item = $disponibles->firstWhere('id', $itemId);
        if (!$item) {
            $this->dispatch('swal', tipo: 'error', titulo: 'No disponible', mensaje: 'Ese item ya no está en stock.');
            return;
        }

        $current[] = $itemId;
        $this->completarKitSeleccion[$productoId] = $current;
    }

    public function completarKit(): void
    {
        $this->refreshStockCompletarKit();

        $tieneSeleccion = false;
        foreach ($this->completarKitComponentes as $comp) {
            if ($comp['faltan'] > 0) {
                if ($comp['es_serializado']) {
                    // Serializados: deben seleccionar exactamente los items
                    $ids = $this->completarKitSeleccion[$comp['producto_id']] ?? [];
                    if (count($ids) !== $comp['faltan']) {
                        $this->addError('general', "Para {$comp['nombre']} debés seleccionar exactamente {$comp['faltan']} item(s) (seleccionaste " . count($ids) . ").");
                        return;
                    }
                    $tieneSeleccion = true;
                } else {
                    // Cantidad (no serializado): solo verificar stock disponible >= faltan
                    $disponibles = $comp['disponibles'] ?? [];
                    $stockDisponible = is_array($disponibles) && isset($disponibles['stock'])
                        ? $disponibles['stock']
                        : ($disponibles instanceof \Illuminate\Support\Collection ? $disponibles->count() : 0);
                    if ($stockDisponible < $comp['faltan']) {
                        $this->addError('general', "Stock insuficiente para {$comp['nombre']}: necesitás {$comp['faltan']}, hay {$stockDisponible}.");
                        return;
                    }
                    // No requiere selección de items, pero cuenta como "tiene selección" para pasar la validación
                    $tieneSeleccion = true;
                }
            }
        }

        if (!$tieneSeleccion) {
            $this->addError('general', 'No hay componentes faltantes para completar.');
            return;
        }

        $sedeId = $this->completarKitSedeId;

        try {
            DB::transaction(function () use ($sedeId) {
                $kit = ItemSerializado::where('id', $this->completarKitItemId)
                    ->where('estado', 'abierto')
                    ->where('sede_id', $sedeId)
                    ->lockForUpdate()
                    ->first();

                if (!$kit) {
                    throw new \RuntimeException('El kit ya no está disponible.');
                }

                $kit->update(['estado' => 'abierto']);

                foreach ($this->completarKitComponentes as $comp) {
                    $faltan = $comp['faltan'] ?? 0;
                    if ($faltan <= 0) continue;

                    $productoId = $comp['producto_id'];
                    $esSerializado = $comp['es_serializado'] ?? false;

                    if ($esSerializado) {
                        // Serializados: vincular items seleccionados
                        $itemIds = $this->completarKitSeleccion[$productoId] ?? [];
                        foreach ($itemIds as $itemId) {
                            $item = ItemSerializado::where('id', $itemId)
                                ->where('estado', 'en_stock')
                                ->where('sede_id', $sedeId)
                                ->lockForUpdate()
                                ->first();

                            if (!$item) {
                                throw new \RuntimeException('Uno de los items seleccionados ya no está disponible. Refrescá la lista e intentá de nuevo.');
                            }

                            $item->update([
                                'kit_padre_id' => $kit->id,
                                'estado' => 'en_stock',
                            ]);
                        }
                    } else {
                        // Cantidad: crear hijos vinculados al kit SIN tocar el ledger.
                        // El descuento real (salida) recién ocurre en finalizar() de la conversión.
                        $stock = ProductoStockSede::where('producto_id', $productoId)
                            ->where('sede_id', $sedeId)
                            ->lockForUpdate()
                            ->first();

                        $cantidad = $stock ? (int) $stock->cantidad : 0;
                        $enKits = ItemSerializado::where('producto_id', $productoId)
                            ->whereNotNull('kit_padre_id')
                            ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                            ->where('sede_id', $sedeId)
                            ->count();
                        $suelto = max(0, $cantidad - $enKits);

                        if ($suelto < $faltan) {
                            throw new \RuntimeException("Stock insuficiente para {$comp['nombre']} al confirmar.");
                        }

                        for ($i = 0; $i < $faltan; $i++) {
                            ItemSerializado::create([
                                'producto_id' => $productoId,
                                'kit_padre_id' => $kit->id,
                                'serie' => null,
                                'atributos' => [
                                    'tipo' => 'cantidad',
                                    'agregado_a_kit' => true,
                                    'fecha' => now()->toDateString(),
                                ],
                                'estado' => 'en_stock',
                                'sede_id' => $sedeId,
                            ]);
                        }
                    }
                }

                $kit->update(['estado' => 'completado']);
            });
        } catch (\RuntimeException $e) {
            $this->addError('general', $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al completar el kit.');
            return;
        }

        $this->modalCompletarKitAbierto = false;
        $this->completarKitItemId = 0;
        $this->completarKitSedeId = 0;
        $this->completarKitNombre = '';
        $this->completarKitComponentes = [];
        $this->completarKitSeleccion = [];

        $this->dispatch('swal', tipo: 'success', titulo: '¡Kit completado!', mensaje: 'El kit se completó y movió a completados.');
    }



    private function refreshStockCompletarKit(): void
    {
        $sedeId = $this->completarKitSedeId;

        foreach ($this->completarKitComponentes as &$comp) {
            if ($comp['faltan'] <= 0) continue;

            if ($comp['es_serializado']) {
                $comp['disponibles'] = ItemSerializado::with('producto')
                    ->where('producto_id', $comp['producto_id'])
                    ->where('estado', 'en_stock')
                    ->where('sede_id', $sedeId)
                    ->whereNull('kit_padre_id')
                    ->whereNotNull('serie')
                    ->where('serie', '!=', '')
                    ->orderBy('id')
                    ->get()
                    ->toArray();
            } else {
                $suelto = $this->sueltoDisponible($comp['producto_id'], $sedeId);
                $comp['disponibles'] = $suelto > 0 ? ['stock' => $suelto] : collect();
            }

            $currentIds = $this->completarKitSeleccion[$comp['producto_id']] ?? [];
            if ($comp['es_serializado'] && !empty($currentIds)) {
                $availableIds = array_column($comp['disponibles'], 'id');
                $this->completarKitSeleccion[$comp['producto_id']] = array_values(
                    array_intersect($currentIds, $availableIds)
                );
            }
        }
        unset($comp);
    }

    public function render()
    {
        return view('livewire.almacen.producto-completar-kit');
    }
}