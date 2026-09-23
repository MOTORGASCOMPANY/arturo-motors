<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use App\Models\ItemSerializado;
use App\Models\ProductoStockSede;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $vistaActual = 'inventario';

    public string $buscar = '';
    public string $filterStock = 'todos';
    public string $filterProveedor = '';

    // Sin tipo int: el <select> de "Todas las sedes" envía '' y rompía ?int
    // (el filtro se quedaba en la primera sede y no mostraba el resto).
    public $filtroSedeId = 1;
    public ?string $filtroEstado = null;
    public string $busquedaInventario = '';



    public string $nivelInventario = 'dashboard';
    public ?string $filtroTipoInventario = null;
    public ?int $kitSeleccionadoId = null;
    public bool $mostrarDetalleKit = false;

    public bool $modalCompletarKitAbierto = false;
    public int $completarKitItemId = 0;
    public int $completarKitSedeId = 0;
    public string $completarKitNombre = '';
    public array $completarKitComponentes = [];
    public array $completarKitSeleccion = [];

    public bool $modalListadoAbierto = false; 

    
    public bool $modalEditarItemAbierto = false;
    public ?int $editarItemId = null;
    public array $editarItemData = [];
    public ?array $editarItemKitInfo = null;

    public function mount()
    {
        $this->filtroSedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;
    }

    public function updatedFiltroSedeId($value): void
    {
        $this->filtroSedeId = ($value === '' || $value === null) ? null : (int) $value;
        $this->resetPage();
    }

    public function updating($property)
    {
        if (in_array($property, ['buscar', 'filterStock', 'filterProveedor'])) {
            $this->resetPage();
        }
    }

    public function updatedFilterStock(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProveedor(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->buscar = '';
        $this->filterStock = 'todos';
        $this->filterProveedor = '';
        $this->resetPage();
    }

    #[On('producto-creado')]
    #[On('entrada-registrada')]
    public function refrescar()
    {
    }

    public function verListadoInventario(string $tipo): void
    {
        $this->nivelInventario = 'listado';
        $this->filtroTipoInventario = $tipo;
        $this->kitSeleccionadoId = null;
        $this->modalListadoAbierto = true;
    }

    public function verDetalleKit(int $kitId): void
    {
        $this->kitSeleccionadoId = $kitId;
        $this->mostrarDetalleKit = true;
    }

    public function cerrarDetalleKit(): void
    {
        $this->mostrarDetalleKit = false;
        $this->kitSeleccionadoId = null;
    }

    /** Vuelve del detalle del kit al listado intermedio (sin cerrar el modal de tipo). */
    public function volverListado(): void
    {
        $this->mostrarDetalleKit = false;
        $this->kitSeleccionadoId = null;
    }

    public function volverDashboard(): void
    {
        $this->nivelInventario = 'dashboard';
        $this->filtroTipoInventario = null;
        $this->kitSeleccionadoId = null;
        $this->mostrarDetalleKit = false;
        $this->modalListadoAbierto = false;
    }

    /** Sincroniza el cierre del listado cuando x-modal lo cierra (click fuera / Esc). */
    public function updatedModalListadoAbierto(bool $value): void
    {
        if ($value) {
            return;
        }

        $this->nivelInventario = 'dashboard';
        $this->filtroTipoInventario = null;
        $this->kitSeleccionadoId = null;
        $this->mostrarDetalleKit = false;
    }

    /** Limpia el detalle cuando x-modal lo cierra. */
    public function updatedMostrarDetalleKit(bool $value): void
    {
        if (!$value) {
            $this->kitSeleccionadoId = null;
        }
    }

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

    /** Stock suelto REAL (cantidad en sede − componentes lockeados dentro de kits). */
    private function sueltoDisponible(int $productoId, int $sedeId): int
    {
        $cantidad = ProductoStockSede::where('producto_id', $productoId)
            ->where('sede_id', $sedeId)
            ->sum('cantidad');

        $enKits = ItemSerializado::where('producto_id', $productoId)
            ->whereNotNull('kit_padre_id')
            ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
            ->where('sede_id', $sedeId)
            ->count();

        return max(0, (int) $cantidad - $enKits);
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

    public function getListadoInventarioProperty()
    {
        $sedeId = $this->filtroSedeId;
        $tipo = $this->filtroTipoInventario;

        if (!$tipo) return collect();

        $estadoMap = [
            'sellado' => 'en_stock',
            'incompleto' => 'abierto',
            'completado' => 'completado',
            'consumido' => 'consumido',
        ];

        if (isset($estadoMap[$tipo])) {
            return ItemSerializado::with(['producto.categoria', 'sede', 'serviceOrder.cliente', 'serviceOrder.tecnico', 'piezasEnKit.producto'])
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
                ->where('estado', $estadoMap[$tipo])
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->when(
                    $this->busquedaInventario,
                    fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"))
                )
                ->orderByDesc('created_at')
                ->get();
        }

        if ($tipo === 'sueltosSerializados') {
            return ItemSerializado::with(['producto.categoria', 'sede'])
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false)->where('es_serializado', true))
                ->whereNull('kit_padre_id')
                ->where('estado', 'en_stock')
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->when(
                    $this->busquedaInventario,
                    fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"))
                )
                ->orderByDesc('created_at')
                ->get();
        }

        if ($tipo === 'sueltosCantidad') {
            $sedeId = $this->filtroSedeId;

            // Stock suelto REAL = ProductoStockSede − kits de ESA MISMA sede.
            // Agrupar solo por producto_id con filtro "todas" restaba kits de
            // cualquier sede a cada fila → componentes desaparecían.
            $itemsEnKits = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
                ->whereNotNull('kit_padre_id')
                ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->get()
                ->groupBy(fn ($i) => $i->producto_id . ':' . $i->sede_id)
                ->map->count();

            return ProductoStockSede::with(['producto.categoria', 'sede'])
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->when(
                    $this->busquedaInventario,
                    fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"))
                )
                ->where('cantidad', '>', 0)
                ->get()
                ->map(function ($stock) use ($itemsEnKits) {
                    $enKits = $itemsEnKits[$stock->producto_id . ':' . $stock->sede_id] ?? 0;
                    $stock->cantidad_suelta_real = max(0, $stock->cantidad - $enKits);
                    return $stock;
                })
                ->filter(fn ($s) => $s->cantidad_suelta_real > 0)
                ->values();
        }

        return collect();
    }

    public function getListadoInventarioTituloProperty(): string
    {
        return match($this->filtroTipoInventario) {
            'sellado' => 'Kits sellados',
            'incompleto' => 'Kits incompletos',
            'completado' => 'Kits completados',
            'consumido' => 'Kits consumidos',
            'sueltosSerializados' => 'Piezas sueltas con serie',
            'sueltosCantidad' => 'Piezas sueltas por cantidad',
            default => 'Inventario',
        };
    }

    public function getListadoInventarioIconoProperty(): string
    {
        return match($this->filtroTipoInventario) {
            'sellado' => 'fa-box',
            'incompleto' => 'fa-box-open',
            'completado' => 'fa-check-circle',
            'consumido' => 'fa-fire',
            'sueltosSerializados' => 'fa-barcode',
            'sueltosCantidad' => 'fa-cubes',
            default => 'fa-boxes-stacked',
        };
    }

    public function getListadoInventarioColorProperty(): string
    {
        return match($this->filtroTipoInventario) {
            'sellado' => 'amber',
            'incompleto' => 'orange',
            'completado' => 'purple',
            'consumido' => 'red',
            'sueltosSerializados' => 'green',
            'sueltosCantidad' => 'indigo',
            default => 'gray',
        };
    }

    public function getKitDetalleProperty()
    {
        if (!$this->kitSeleccionadoId) return null;

        $kit = ItemSerializado::with([
            'producto.categoria',
            'sede',
            'serviceOrder.cliente',
            'serviceOrder.vehiculo',
            'serviceOrder.tecnico',
            'vehiculoInstalado',
            'piezasEnKit.producto.categoria',
            'piezasEnKit.sede',
        ])->find($this->kitSeleccionadoId);

        if (!$kit) return null;

        $receta = DB::table('kit_componentes')
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

        $piezasTodas = $kit->piezasEnKit;

        // Piezas ACTIVAS del kit (las que cuentan para la receta).
        // Excluye solo defectuosas/devueltas.
        // NO descuenta KitPiezaExtraida: ese registro es histórico ("salió una pieza").
        // Si la computadora volvió a linkearse (completarKit / reparación de fantasma),
        // descontarla de nuevo la borraba del detalle aunque esté físicamente en el kit.
        // El caso fantasma se resuelve desvinculando el hijo (abrirKitYExtraerPieza),
        // no restándolo de la receta.
        $activas = $piezasTodas
            ->reject(fn ($p) => in_array($p->estado, ['defectuoso', 'devuelta_por_no_calzar'], true));

        $piezasActuales = $activas->pluck('producto_id')->countBy()->toArray();

        $componentes = $receta->map(function ($r) use ($piezasActuales) {
            $presente = $piezasActuales[$r->producto_id] ?? 0;
            return [
                'nombre' => $r->nombre,
                'es_serializado' => (bool) $r->es_serializado,
                'cantidad_esperada' => $r->cantidad,
                'presente' => $presente,
                'completo' => $presente >= $r->cantidad,
            ];
        });

        $kit->recetaDetalles = $componentes;
        $kit->totalEsperado = $receta->sum('cantidad');
        $kit->totalPresente = $activas->count();

        // Resumen de piezas ACTIVAS del kit agrupadas por producto.
        // Excluye solo defectuosas/devueltas (van en reemplazados).
        $kit->piezasResumidas = $activas
            ->groupBy('producto_id')
            ->map(function ($grupo) {
                return [
                    'nombre'         => $grupo->first()->producto?->nombre ?? '—',
                    'cantidad'       => $grupo->count(),
                    'cantidadCruda'  => $grupo->count(),
                    'cantidadActiva' => $grupo->count(),
                    'series'         => $grupo->pluck('serie')->filter()->values(),
                    'instalado'      => $grupo->contains('estado', 'instalado'),
                    'defectuosas'    => collect(),
                    'estados'        => $grupo->pluck('estado')->unique()->values(),
                ];
            })
            ->filter(fn ($g) => $g['cantidad'] > 0)
            ->values();

        // Reemplazados/defectuosos que ya NO están activos en el kit
        // (histórico de la conversión — so=NULL tras devolución al almacén).
        $kit->reemplazados = $piezasTodas->filter(
            fn ($p) => in_array($p->estado, ['defectuoso', 'devuelta_por_no_calzar'], true)
        )->values();

        // Items de cantidad extra / repuestos asignados a la misma orden
        // (sueltos, sin kit_padre_id) — se muestran resaltados en azul.
        $kit->itemsExtraOrden = $kit->service_order_id
            ? ItemSerializado::where('service_order_id', $kit->service_order_id)
                ->whereNull('kit_padre_id')
                ->whereDoesntHave('piezasEnKit')
                ->where('id', '!=', $kit->id)
                ->whereNotIn('estado', ['defectuoso', 'devuelta_por_no_calzar'])
                ->with('producto.categoria')
                ->get()
            : collect();

        // Movimientos de stock de la conversión (salidas/entradas reales)
        $kit->movimientosConversion = $kit->service_order_id
            ? \App\Models\MovimientoStock::with('producto')
                ->where('service_order_id', $kit->service_order_id)
                ->orderBy('id')
                ->get()
            : collect();

        // Verificar si todos los componentes faltantes tienen stock disponible
        $tieneFaltantes = $componentes->contains(fn($r) => !$r['completo']);
        $todosConStock = true;
        
        if ($tieneFaltantes) {
            foreach ($componentes as $r) {
                if (!$r['completo'] && $r['presente'] < $r['cantidad_esperada']) {
                    $faltan = $r['cantidad_esperada'] - $r['presente'];
                    $productoId = \App\Models\Producto::where('nombre', $r['nombre'])->first()?->id ?? 0;
                    
                    if ($r['es_serializado']) {
                        $disponibles = \App\Models\ItemSerializado::where('producto_id', $productoId)
                            ->where('estado', 'en_stock')
                            ->where('sede_id', $kit->sede_id)
                            ->whereNull('kit_padre_id')
                            ->count();
                        if ($disponibles < $faltan) {
                            $todosConStock = false;
                            break;
                        }
                    } else {
                        $suelto = $this->sueltoDisponible($productoId, (int) $kit->sede_id);
                        if ($suelto < $faltan) {
                            $todosConStock = false;
                            break;
                        }
                    }
                }
            }
        }
        
        $kit->tieneFaltantes = $tieneFaltantes;
        $kit->todosConStock = $todosConStock;

        return $kit;
    }

    public function getResumenInventarioProperty()
    {
        $sedeId = $this->filtroSedeId;
        $estado = $this->filtroEstado;

        $kitsQuery = ItemSerializado::with(['producto.categoria', 'sede', 'serviceOrder.cliente', 'piezasEnKit.producto.categoria'])
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->orderByDesc('created_at');

        if ($estado) {
            $kitsQuery->where('estado', $estado);
        }

        $kitsAll = $kitsQuery->get();

        $kitsSellados = $kitsAll->where('estado', 'en_stock')->groupBy('producto_id');
        $kitsIncompletos = $kitsAll->where('estado', 'abierto')->groupBy('producto_id');
        $kitsConsumidos = $kitsAll->where('estado', 'consumido')->groupBy('producto_id');
        $kitsCompletados = $kitsAll->where('estado', 'completado')->groupBy('producto_id');

        $sueltosSerializados = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas(
                'producto.categoria',
                fn ($q) => $q->where('es_kit', false)->where('es_serializado', true)
            )
            ->whereNull('kit_padre_id')
            // Solo disponibles: instalado/defectuoso/consumido no es pieza suelta
            ->where('estado', 'en_stock')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('producto_id');

        $sueltosCantidad = ProductoStockSede::with('producto.categoria', 'sede')
            ->whereHas(
                'producto.categoria',
                fn ($q) => $q->where('es_serializado', false)->where('es_kit', false)
            )
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->where('cantidad', '>', 0)
            ->get()
            ->map(function ($stock) use ($sedeId) {
                // Restar solo kits de LA MISMA sede de la fila de stock.
                // Si el filtro es "todas", restar el total global de kits
                // descuentaba piezas de Ancón al stock de Callao (y podían
                // quedar en 0 → no aparecían los componentes).
                $enKits = ItemSerializado::where('producto_id', $stock->producto_id)
                    ->whereNotNull('kit_padre_id')
                    ->whereIn('estado', ['en_stock', 'abierto', 'completado', 'asignado'])
                    ->where('sede_id', $stock->sede_id)
                    ->count();
                $stock->cantidad_suelta_real = max(0, $stock->cantidad - $enKits);
                return $stock;
            })
            ->filter(fn ($s) => $s->cantidad_suelta_real > 0);

        $instaladosCount = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->where('estado', 'consumido')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        $completadosCount = ItemSerializado::whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->where('estado', 'completado')
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->count();

        return [
            'kitsSellados' => $kitsSellados,
            'kitsIncompletos' => $kitsIncompletos,
            'kitsConsumidos' => $kitsConsumidos,
            'kitsCompletados' => $kitsCompletados,
            'sueltosSerializados' => $sueltosSerializados,
            'sueltosCantidad' => $sueltosCantidad,
            'conteos' => [
                'sellados' => $kitsSellados->flatten()->count(),
                'incompletos' => $kitsIncompletos->flatten()->count(),
                'consumidos' => $kitsConsumidos->flatten()->count(),
                'completados' => $kitsCompletados->flatten()->count(),
                'instalados' => $instaladosCount,
                'sueltosSerializados' => $sueltosSerializados->count(),
                'sueltosCantidadTipos' => $sueltosCantidad->count(),
                'sueltosCantidadTotal' => $sueltosCantidad->sum('cantidad_suelta_real'),
            ],
        ];
    }

    public function getKitsProperty()
    {
        $sedeId = $this->filtroSedeId;

        $kits = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->orderByDesc('created_at')
            ->get();

        return [
            'sellados' => $kits->where('estado', 'en_stock')->groupBy('producto_id'),
            'incompletos' => $kits->where('estado', 'abierto')->groupBy('producto_id'),
            'completados' => $kits->where('estado', 'completado')->groupBy('producto_id'),
            'consumidos' => $kits->where('estado', 'consumido')->groupBy('producto_id'),
        ];
    }

    public function abrirEditarItem(int $itemId): void
    {
        $item = ItemSerializado::with([
            'producto.categoria',
            'kitPadre.producto.categoria',
            'kitPadre.piezasEnKit.producto.categoria',
        ])->find($itemId);
        
        if (!$item) return;

        $this->editarItemId = $itemId;
        $this->editarItemData = [];

        $esquema = $item->producto->categoria->esquema_atributos ?? ['serie'];
        $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
        $attrs = $item->atributos ?? [];

        foreach ($campos as $campo) {
            $this->editarItemData[$campo] = $attrs[$campo] ?? '';
        }

        // Determinar a qué componente de la receta del kit padre pertenece
        $this->editarItemKitInfo = null;
        if ($item->kit_padre_id) {
            $kitPadre = $item->kitPadre;
            if ($kitPadre) {
                $receta = \Illuminate\Support\Facades\DB::table('kit_componentes')
                    ->join('productos', 'producto_componente_id', '=', 'productos.id')
                    ->join('categorias_almacen', 'productos.categoria_id', '=', 'categorias_almacen.id')
                    ->where('producto_kit_id', $kitPadre->producto_id)
                    ->select(
                        'productos.id as producto_id',
                        'productos.nombre',
                        'kit_componentes.cantidad_esperada as cantidad'
                    )
                    ->get();

                $piezasActuales = $kitPadre->piezasEnKit->pluck('producto_id')->countBy()->toArray();
                
                // Buscar qué componente corresponde a este item
                foreach ($receta as $r) {
                    $esperados = $r->cantidad;
                    $presentes = $piezasActuales[$r->producto_id] ?? 0;
                    // Si este item es de este producto y aún no se completó la cuota
                    if ($r->producto_id === $item->producto_id && $presentes <= $esperados) {
                        $this->editarItemKitInfo = [
                            'kit_nombre' => $kitPadre->producto?->nombre ?? 'Kit',
                            'kit_id' => $kitPadre->id,
                            'kit_serie' => $kitPadre->serie,
                            'componente_nombre' => $r->nombre,
                            'componente_esperados' => $esperados,
                            'componente_presentes' => $presentes,
                        ];
                        break;
                    }
                }
            }
        }

        $this->modalEditarItemAbierto = true;
    }

    public function guardarEditarItem(): void
    {
        $item = ItemSerializado::find($this->editarItemId);
        if (!$item) return;

        $attrs = $item->atributos ?? [];
        foreach ($this->editarItemData as $campo => $valor) {
            if ($valor !== '' && $valor !== null) {
                $attrs[$campo] = $valor;
            }
        }

        if ($this->editarItemData['serie'] ?? null) {
            $item->serie = $this->editarItemData['serie'];
        }

        $item->atributos = $attrs;
        $item->save();

        $this->modalEditarItemAbierto = false;
        $this->editarItemId = null;
        $this->editarItemData = [];

        $this->dispatch('minAlert', titulo: 'Listo!', mensaje: 'Item actualizado correctamente.', icono: 'success');
    }

    public function cerrarEditarItem(): void
    {
        $this->modalEditarItemAbierto = false;
        $this->editarItemId = null;
        $this->editarItemData = [];
        $this->editarItemKitInfo = null;
    }

    public function render()
    {
        $query = Producto::with('categoria')
            ->when(
                $this->buscar,
                fn ($q) => $q->buscar($this->buscar)
            )
            ->when(in_array($this->filterStock, ['bajo', 'sin'], true), function ($q) {
                $sedeId = (int) (Sede::activas()->orderBy('id')->first()?->id ?? 1);

                // Disponible REAL según modelo dual (mismo criterio que Producto::stockSueltoEnSede):
                // kit = unidades disponibles (en_stock o completado); serializado = items sueltos;
                // cantidad = stock_sede − enKits.
                $estadosKit = implode(
                    ', ',
                    array_map(fn ($e) => "'{$e}'", ItemSerializado::ESTADOS_KIT_DISPONIBLE)
                );
                $suelto = "(CASE
                    WHEN (SELECT ca.es_kit FROM categorias_almacen ca WHERE ca.id = productos.categoria_id) = 1 THEN (
                        SELECT COUNT(*) FROM items_serializados i
                        WHERE i.producto_id = productos.id AND i.estado IN ({$estadosKit}) AND i.sede_id = ?
                    )
                    WHEN (SELECT ca.es_serializado FROM categorias_almacen ca WHERE ca.id = productos.categoria_id) = 1 THEN (
                        SELECT COUNT(*) FROM items_serializados i
                        WHERE i.producto_id = productos.id AND i.estado = 'en_stock' AND i.sede_id = ?
                          AND i.kit_padre_id IS NULL
                    )
                    ELSE GREATEST(0,
                        COALESCE((SELECT SUM(ps.cantidad) FROM producto_stock_sede ps
                                  WHERE ps.producto_id = productos.id AND ps.sede_id = ?), 0)
                        - (SELECT COUNT(*) FROM items_serializados i
                           WHERE i.producto_id = productos.id AND i.kit_padre_id IS NOT NULL
                             AND i.estado IN ('en_stock', 'abierto', 'completado', 'asignado')
                             AND i.sede_id = ?)
                    )
                END)";

                $params = [$sedeId, $sedeId, $sedeId, $sedeId];

                if ($this->filterStock === 'bajo') {
                    $q->whereRaw("{$suelto} <= productos.stock_minimo", $params);
                } else {
                    $q->whereRaw("{$suelto} = 0", $params);
                }
            })
            ->when(
                $this->filterProveedor !== '',
                fn ($q) => $q->where('proveedor', 'like', "%{$this->filterProveedor}%")
            )
            ->orderBy('nombre')
            ->paginate(15);

        // Solo calcula datos pesados cuando la vista actual los necesita.
        // Antes se recalculaban en cada render (abrir/cerrar modal = segundos de espera).
        return view('livewire.almacen.productos.listado', [
            'productos' => $query,
            'categorias' => CategoriaAlmacen::orderBy('nombre')->get(),
            'sedes' => Sede::activas()->get(),
            'resumenInventario' => ($this->vistaActual === 'inventario' && $this->nivelInventario === 'dashboard')
                ? $this->resumenInventario
                : [],
            'kits' => $this->vistaActual === 'kits'
                ? $this->kits
                : ['sellados' => collect(), 'incompletos' => collect(), 'completados' => collect(), 'consumidos' => collect()],
            'listadoInventario' => $this->modalListadoAbierto
                ? $this->listadoInventario
                : collect(),
            'listadoInventarioTitulo' => $this->listadoInventarioTitulo,
            'listadoInventarioIcono' => $this->listadoInventarioIcono,
            'listadoInventarioColor' => $this->listadoInventarioColor,
            'kitDetalle' => $this->mostrarDetalleKit ? $this->kitDetalle : null,
        ]);
    }
}