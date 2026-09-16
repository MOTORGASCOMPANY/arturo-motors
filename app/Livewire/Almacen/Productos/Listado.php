<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use App\Models\ItemSerializado;
use App\Models\ProductoStockSede;
use App\Models\Sede;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $vistaActual = 'inventario'; // inventario | catalogo
    public string $categoriaId = 'todas';
    public string $buscar = '';
    public string $filterStock = 'todos';
    public string $filterProveedor = '';

    // Inventario filters
    public ?int $filtroSedeId = 1; // Default Callao
    public ?string $filtroEstado = null;
    public string $busquedaInventario = '';
    public ?int $detalleProductoId = null;
    public ?string $tipoKitDetalle = null;

    public function mount()
    {
        $this->filtroSedeId = 1; // Callao por defecto
    }

    public function updating($property)
    {
        if (in_array($property, ['categoriaId', 'buscar', 'filterStock', 'filterProveedor'])) $this->resetPage();
    }

    public function updatedFilterStock(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProveedor(): void
    {
        $this->resetPage();
    }

    #[On('producto-creado')]
    #[On('entrada-registrada')]
    public function refrescar()
    {
        // Vacío a propósito: escuchar el evento ya fuerza el re-render.
    }

    public function verDetalle(int $productoId, string $tipo = 'sellado')
    {
        $this->detalleProductoId = $this->detalleProductoId === $productoId ? null : $productoId;
        $this->tipoKitDetalle = $this->detalleProductoId ? $tipo : null;
    }

    public function getResumenInventarioProperty()
    {
        $sedeId = $this->filtroSedeId;
        $estado = $this->filtroEstado;

        // Todos los kits (para agrupar por estado)
        $kitsQuery = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($this->busquedaInventario, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"));
            })
            ->orderByDesc('created_at');

        // Aplicar filtro de estado solo si se selecciona uno específico
        if ($estado) {
            $kitsQuery->where('estado', $estado);
        }

        $kitsAll = $kitsQuery->get();

        // Separar por estado
        $kitsSellados = $kitsAll->where('estado', 'en_stock')->groupBy('producto_id');
        $kitsUtilizados = $kitsAll->where('estado', 'abierto')->groupBy('producto_id');
        $kitsAsignados = $kitsAll->where('estado', 'asignado')->groupBy('producto_id');
        $kitsConsumidos = $kitsAll->where('estado', 'consumido')->groupBy('producto_id');

        // Piezas sueltas serializadas
        $sueltos = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false)->where('es_serializado', true))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($this->busquedaInventario, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"));
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('producto_id');

        // Productos por cantidad (no serializados, no kits) — desde producto_stock_sede
        $porCantidad = ProductoStockSede::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_serializado', false)->where('es_kit', false))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($this->busquedaInventario, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"));
            })
            ->where('cantidad', '>', 0)
            ->get();

        return [
            'kits' => $kitsSellados,
            'kitsSellados' => $kitsSellados,
            'kitsUtilizados' => $kitsUtilizados,
            'kitsAsignados' => $kitsAsignados,
            'kitsConsumidos' => $kitsConsumidos,
            'sueltos' => $sueltos,
            'porCantidad' => $porCantidad,
            'conteos' => [
                'sellados' => $kitsSellados->flatten()->count(),
                'utilizados' => $kitsUtilizados->flatten()->count(),
                'asignados' => $kitsAsignados->flatten()->count(),
                'consumidos' => $kitsConsumidos->flatten()->count(),
                'instalados' => $kitsAll->where('estado', 'instalado')->flatten()->count(),
                'sueltos' => $sueltos->flatten()->count(),
                'porCantidad' => $porCantidad->count(),
                'total' => $kitsAll->flatten()->count() + $sueltos->flatten()->count() + $porCantidad->count(),
            ],
        ];
    }

    public function getDetallesInventarioProperty()
    {
        if (!$this->detalleProductoId) return collect();

        $query = ItemSerializado::with(['producto', 'sede', 'serviceOrder.cliente', 'vehiculoInstalado', 'piezasEnKit.producto', 'piezasEnKit.serviceOrder.cliente', 'piezasEnKit.vehiculoInstalado'])
            ->where('producto_id', $this->detalleProductoId)
            ->when($this->filtroSedeId, fn ($q) => $q->where('sede_id', $this->filtroSedeId))
            ->orderByDesc('created_at');

        // Si es utilizado, traer solo kits abiertos
        if ($this->tipoKitDetalle === 'utilizado') {
            $query->where('estado', 'abierto');
        }
        // Si es asignado, traer solo items asignados
        elseif ($this->tipoKitDetalle === 'asignado') {
            $query->where('estado', 'asignado');
        }
        // Si es sellado, traer solo en stock
        elseif ($this->tipoKitDetalle === 'sellado') {
            $query->where('estado', 'en_stock');
        }
        // Si hay filtro de estado, aplicarlo
        elseif ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        return $query->get();
    }

    public function render()
    {
        $query = Producto::with('categoria')
            ->when($this->categoriaId !== 'todas' && $this->categoriaId !== 'solo_kits', fn ($q) => $q->where('categoria_id', $this->categoriaId))
            ->when($this->categoriaId === 'solo_kits', fn ($q) => $q->whereHas('categoria', fn ($cq) => $cq->where('es_kit', true)))
            ->when($this->buscar, fn ($q) => $q->buscar($this->buscar))
            ->when($this->filterStock === 'bajo', function ($q) {
                $sedeId = \App\Models\Sede::activas()->orderBy('id')->first()?->id ?? 1;
                $q->whereRaw('(SELECT COUNT(*) FROM items_serializados WHERE items_serializados.producto_id = productos.id AND items_serializados.estado = ? AND items_serializados.sede_id = ?) <= productos.stock_minimo', ['en_stock', $sedeId]);
            })
            ->when($this->filterStock === 'sin', function ($q) {
                $q->whereDoesntHave('items', fn($iq) => $iq->where('estado', 'en_stock'));
            })
            ->when($this->filterProveedor !== '', fn ($q) => $q->where('proveedor', 'like', "%{$this->filterProveedor}%"))
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.almacen.productos.listado', [
            'productos' => $query,
            'categorias' => CategoriaAlmacen::orderBy('nombre')->get(),
            'sedes' => Sede::activas()->get(),
            'resumenInventario' => $this->resumenInventario,
            'detallesInventario' => $this->detallesInventario,
        ]);
    }
}
