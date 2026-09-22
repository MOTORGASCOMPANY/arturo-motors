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

    public ?int $filtroSedeId = 1;
    public ?string $filtroEstado = null;
    public string $busquedaInventario = '';



    public string $nivelInventario = 'dashboard';
    public ?string $filtroTipoInventario = null;
    public ?int $kitSeleccionadoId = null;

    public bool $modalCompletarKitAbierto = false;
    public int $completarKitItemId = 0;
    public int $completarKitSedeId = 0;
    public string $completarKitNombre = '';
    public array $completarKitComponentes = [];
    public array $completarKitSeleccion = []; 

    
    public bool $modalEditarItemAbierto = false;
    public ?int $editarItemId = null;
    public array $editarItemData = [];

    public function mount()
    {
        $this->filtroSedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;
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
    }

    public function verDetalleKit(int $kitId): void
    {
        $this->nivelInventario = 'detalle';
        $this->kitSeleccionadoId = $kitId;
    }

    public function volverListado(): void
    {
        $this->nivelInventario = 'listado';
        $this->kitSeleccionadoId = null;
    }

    public function volverDashboard(): void
    {
        $this->nivelInventario = 'dashboard';
        $this->filtroTipoInventario = null;
        $this->kitSeleccionadoId = null;
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
            ->pluck('producto_id')
            ->countBy()
            ->toArray();

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
                    $stock = ProductoStockSede::where('producto_id', $comp->producto_id)
                        ->where('sede_id', $sedeId)
                        ->where('cantidad', '>', 0)
                        ->sum('cantidad');
                    $disponibles = $stock > 0 ? ['stock' => $stock] : collect();
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
        $this->completarKitItemId = 0;
        $this->completarKitNombre = '';
        $this->completarKitComponentes = [];
        $this->completarKitSeleccion = [];
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
                $ids = $this->completarKitSeleccion[$comp['producto_id']] ?? [];
                if (count($ids) !== $comp['faltan']) {
                    $this->addError('general', "Para {$comp['nombre']} debés seleccionar exactamente {$comp['faltan']} item(s) (seleccionaste " . count($ids) . ").");
                    return;
                }
                $tieneSeleccion = true;
            }
        }

        if (!$tieneSeleccion) {
            $this->addError('general', 'Debés seleccionar los items exactos para cada componente faltante.');
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

                foreach ($this->completarKitSeleccion as $productoId => $itemIds) {
                    if (empty($itemIds)) continue;

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
                            'estado' => 'reemplazado',
                        ]);
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
                $stock = ProductoStockSede::where('producto_id', $comp['producto_id'])
                    ->where('sede_id', $sedeId)
                    ->where('cantidad', '>', 0)
                    ->sum('cantidad');
                $comp['disponibles'] = $stock > 0 ? ['stock' => $stock] : collect();
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
            return ProductoStockSede::with(['producto.categoria', 'sede'])
                ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
                ->whereNotIn('producto_id', DB::table('kit_componentes')->select('producto_componente_id'))
                ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
                ->when(
                    $this->busquedaInventario,
                    fn ($q) => $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"))
                )
                ->where('cantidad', '>', 0)
                ->get();
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

        $piezasActuales = $kit->piezasEnKit->pluck('producto_id')->countBy()->toArray();

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
        $kit->totalPresente = $kit->piezasEnKit->count();

        return $kit;
    }

    public function getResumenInventarioProperty()
    {
        $sedeId = $this->filtroSedeId;
        $estado = $this->filtroEstado;

        $kitsQuery = ItemSerializado::with('producto.categoria', 'sede')
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
            ->whereNotIn('producto_id', DB::table('kit_componentes')->select('producto_componente_id'))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when(
                $this->busquedaInventario,
                fn ($q) => $q->whereHas(
                    'producto',
                    fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%")
                )
            )
            ->where('cantidad', '>', 0)
            ->get();

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
                'sueltosCantidadTotal' => $sueltosCantidad->sum('cantidad'),
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
        $item = ItemSerializado::with('producto.categoria')->find($itemId);
        if (!$item) return;

        $this->editarItemId = $itemId;
        $this->editarItemData = [];

        $esquema = $item->producto->categoria->esquema_atributos ?? ['serie'];
        $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
        $attrs = $item->atributos ?? [];

        foreach ($campos as $campo) {
            $this->editarItemData[$campo] = $attrs[$campo] ?? '';
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
    }

    public function render()
    {
        $query = Producto::with('categoria')
            ->when(
                $this->buscar,
                fn ($q) => $q->buscar($this->buscar)
            )
            ->when($this->filterStock === 'bajo', function ($q) {
                $sedeId = Sede::activas()->orderBy('id')->first()?->id ?? 1;

                $q->whereRaw(
                    '(SELECT COUNT(*) FROM items_serializados WHERE items_serializados.producto_id = productos.id AND items_serializados.estado = ? AND items_serializados.sede_id = ?) <= productos.stock_minimo',
                    ['en_stock', $sedeId]
                );
            })
            ->when(
                $this->filterStock === 'sin',
                fn ($q) => $q->whereDoesntHave(
                    'items',
                    fn ($iq) => $iq->where('estado', 'en_stock')
                )
            )
            ->when(
                $this->filterProveedor !== '',
                fn ($q) => $q->where('proveedor', 'like', "%{$this->filterProveedor}%")
            )
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.almacen.productos.listado', [
            'productos' => $query,
            'categorias' => CategoriaAlmacen::orderBy('nombre')->get(),
            'sedes' => Sede::activas()->get(),
            'resumenInventario' => $this->resumenInventario,
            'kits' => $this->kits,
            'listadoInventario' => $this->listadoInventario,
            'listadoInventarioTitulo' => $this->listadoInventarioTitulo,
            'listadoInventarioIcono' => $this->listadoInventarioIcono,
            'listadoInventarioColor' => $this->listadoInventarioColor,
            'kitDetalle' => $this->kitDetalle,
        ]);
    }
}