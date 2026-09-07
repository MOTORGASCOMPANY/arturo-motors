<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Listado extends Component
{
    use WithPagination;

    public string $categoriaId = 'todas';
    public string $buscar = '';
    public string $filterStock = 'todos';
    public string $filterProveedor = '';

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

    public function render()
    {
        $productos = Producto::with('categoria')
            ->when($this->categoriaId !== 'todas', fn ($q) => $q->where('categoria_id', $this->categoriaId))
            ->when($this->buscar, fn ($q) => $q->buscar($this->buscar))
            ->when($this->filterStock === 'bajo', fn ($q) => $q->whereColumn('stock_disponible', '<=', 'stock_minimo'))
            ->when($this->filterStock === 'sin', fn ($q) => $q->where('stock_disponible', 0))
            ->when($this->filterProveedor !== '', fn ($q) => $q->where('proveedor', 'like', "%{$this->filterProveedor}%"))
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.almacen.productos.listado', [
            'productos' => $productos,
            'categorias' => CategoriaAlmacen::orderBy('nombre')->get(),
        ]);
    }
}
