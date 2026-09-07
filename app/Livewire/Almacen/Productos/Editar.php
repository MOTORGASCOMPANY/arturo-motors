<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use Livewire\Attributes\On;
use Livewire\Component;

class Editar extends Component
{
    public bool $mostrarModal = false;
    public ?Producto $producto = null;

    public ?int $categoriaId = null;
    public string $nombre = '';
    public string $marca = '';
    public array $atributos = [];
    public $precioReferencial = 0;
    public $stockMinimo = 0;
    public bool $categoriaBloqueada = false;

    #[On('abrir-modal-editar-producto')]
    public function abrir(int $productoId)
    {
        $this->producto = Producto::with('categoria')->findOrFail($productoId);

        $this->categoriaId = $this->producto->categoria_id;
        $this->nombre = $this->producto->nombre;
        $this->marca = $this->producto->marca ?? '';
        $this->atributos = $this->producto->atributos ?? [];
        $this->precioReferencial = $this->producto->precio_referencial ?? 0;
        $this->stockMinimo = $this->producto->stock_minimo ?? 0;

        $tieneStockSuelto = $this->producto->stockPorSede()->where('cantidad', '>', 0)->exists();
        $tieneItems = $this->producto->items()->exists();
        $this->categoriaBloqueada = $tieneStockSuelto || $tieneItems;

        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function cerrar()
    {
        $this->mostrarModal = false;
    }

    public function getCategoriaProperty()
    {
        return $this->categoriaId ? CategoriaAlmacen::find($this->categoriaId) : null;
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required|string|max:150',
            'marca' => 'nullable|string|max:100',
            'precioReferencial' => 'nullable|numeric|min:0',
            'stockMinimo' => 'nullable|integer|min:0',
        ]);

        try {
            $datos = [
                'nombre' => $this->nombre,
                'marca' => $this->marca ?: null,
                'atributos' => array_filter($this->atributos) ?: null,
                'precio_referencial' => $this->precioReferencial ?: null,
                'stock_minimo' => $this->stockMinimo,
            ];

            if (!$this->categoriaBloqueada) {
                $datos['categoria_id'] = $this->categoriaId;
            }

            $this->producto->update($datos);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar el producto.', icono: 'error');
            return;
        }

        $this->mostrarModal = false;
        $this->dispatch('producto-creado');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: 'Producto actualizado.', icono: 'success');
    }

    public function render()
    {
        return view('livewire.almacen.productos.editar', [
            'categorias' => CategoriaAlmacen::orderBy('nombre')->get(),
        ]);
    }
}