<?php

namespace App\Livewire\Almacen;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use App\Models\ItemSerializado;
use App\Models\ProductoStockSede;
use App\Livewire\Almacen\ListadoInventario;
use App\Livewire\Almacen\ResumenInventario;
use App\Livewire\Almacen\StockSuelto;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductoListado extends Component
{
    use ListadoInventario;
    use ResumenInventario;
    use StockSuelto;
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


    public bool $modalListadoAbierto = false; 


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
        // Modal de items por cantidad eliminado (innecesario).
        if ($tipo === 'sueltosCantidad') {
            return;
        }

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


    public function verDetallePieza(int $itemId): void
    {
        $this->dispatch('productos:detalle-pieza:abrir', itemId: $itemId);
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



    /**
     * Abre el modal de completar kit en su componente Livewire hijo.
     *
     * Se conserva el nombre publico original: lo usan los `wire:click` del
     * listado y del detalle de kit, que se renderizan en ESTE componente.
     */
    public function abrirCompletarKit(int $kitItemId): void
    {
        $this->dispatch('productos:completar-kit:abrir', kitItemId: $kitItemId);
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




    public function abrirEditarItem(int $itemId): void
    {
        $this->dispatch('productos:editar-item:abrir', itemId: $itemId);
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
        return view('livewire.almacen.producto-listado', [
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