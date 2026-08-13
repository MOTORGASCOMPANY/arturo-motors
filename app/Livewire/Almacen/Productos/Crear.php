<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use Livewire\Attributes\On;
use Livewire\Component;

class Crear extends Component
{
    public bool $mostrarModal = false;

    public ?int $categoriaId = null;
    public string $nombre = '';
    public string $marca = '';
    public array $atributos = [];
    public $precioReferencial = 0;

    #[On('abrir-modal-producto')]
    public function abrir()
    {
        $this->reset(['categoriaId', 'nombre', 'marca', 'atributos', 'precioReferencial']);
        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function cerrar()
    {
        $this->mostrarModal = false;
    }

    public function updatedCategoriaId($value)
    {
        $categoria = CategoriaAlmacen::find($value);
        $this->atributos = [];
        foreach ($categoria?->esquema_atributos ?? [] as $campo) {
            $this->atributos[$campo] = '';
        }
    }

    public function getCategoriaProperty()
    {
        return $this->categoriaId ? CategoriaAlmacen::find($this->categoriaId) : null;
    }

    public function guardar()
    {
        $this->validate([
            'categoriaId' => 'required|exists:categorias_almacen,id',
            'nombre' => 'required|string|max:150',
            'marca' => 'nullable|string|max:100',
            'precioReferencial' => 'nullable|numeric|min:0',
        ]);

        try {
            Producto::create([
                'categoria_id' => $this->categoriaId,
                'nombre' => $this->nombre,
                'marca' => $this->marca ?: null,
                'atributos' => array_filter($this->atributos) ?: null,
                'precio_referencial' => $this->precioReferencial ?: null,
                'stock' => 0,
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo crear el producto. Intenta de nuevo.', icono: 'error');
            return;
        }

        $this->mostrarModal = false;
        $this->reset(['categoriaId', 'nombre', 'marca', 'atributos', 'precioReferencial']);

        $this->dispatch('producto-creado');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: 'Producto creado correctamente. Ahora registra su entrada de stock.', icono: 'success');

        //session()->flash('mensaje', 'Producto creado. Ahora registra la entrada de stock.');
        //$this->redirect(route('almacen.productos.listado'), navigate: true);
    }

    public function render()
    {
        return view('livewire.almacen.productos.crear', [
            'categorias' => CategoriaAlmacen::orderBy('nombre')->get(),
        ]);
    }
}
