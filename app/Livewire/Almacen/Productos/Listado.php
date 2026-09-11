<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use App\Models\ItemSerializado;
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

    public function verDetalle(int $productoId)
    {
        $this->detalleProductoId = $this->detalleProductoId === $productoId ? null : $productoId;
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

        // Piezas sueltas
        $sueltos = ItemSerializado::with('producto.categoria', 'sede')
            ->whereHas('producto.categoria', fn ($q) => $q->where('es_kit', false))
            ->when($sedeId, fn ($q) => $q->where('sede_id', $sedeId))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($this->busquedaInventario, function ($q) {
                $q->whereHas('producto', fn ($p) => $p->where('nombre', 'like', "%{$this->busquedaInventario}%"));
            })
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('producto_id');

        return [
            'kits' => $kitsSellados,
            'kitsSellados' => $kitsSellados,
            'kitsUtilizados' => $kitsUtilizados,
            'kitsAsignados' => $kitsAsignados,
            'kitsConsumidos' => $kitsConsumidos,
            'sueltos' => $sueltos,
        ];
    }

    public function getDetallesInventarioProperty()
    {
        if (!$this->detalleProductoId) return collect();

        return ItemSerializado::with('producto', 'sede')
            ->where('producto_id', $this->detalleProductoId)
            ->when($this->filtroSedeId, fn ($q) => $q->where('sede_id', $this->filtroSedeId))
            ->when($this->filtroEstado, fn ($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->get();
    }

    public function render()
    {
        $query = Producto::with('categoria')
            ->when($this->categoriaId !== 'todas' && $this->categoriaId !== 'solo_kits', fn ($q) => $q->where('categoria_id', $this->categoriaId))
            ->when($this->categoriaId === 'solo_kits', fn ($q) => $q->whereHas('categoria', fn ($cq) => $cq->where('es_kit', true)))
            ->when($this->buscar, fn ($q) => $q->buscar($this->buscar))
            ->when($this->filterStock === 'bajo', fn ($q) => $q->whereColumn('stock_disponible', '<=', 'stock_minimo'))
            ->when($this->filterStock === 'sin', fn ($q) => $q->where('stock_disponible', 0))
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
