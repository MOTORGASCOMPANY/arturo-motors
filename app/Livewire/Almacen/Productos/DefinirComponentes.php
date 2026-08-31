<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\KitComponente;
use App\Models\Producto;
use Livewire\Attributes\On;
use Livewire\Component;

class DefinirComponentes extends Component
{
    public bool $mostrarModal = false;
    public ?Producto $producto = null;

    public ?int $componenteId = null;
    public int $cantidadEsperada = 1;

    #[On('abrir-modal-componentes')]
    public function abrir(int $productoId)
    {
        $this->producto = Producto::with('categoria')->findOrFail($productoId);
        $this->reset(['componenteId', 'cantidadEsperada']);
        $this->cantidadEsperada = 1;
        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function cerrar()
    {
        $this->mostrarModal = false;
    }

    public function getComponentesProperty()
    {
        return $this->producto
            ? KitComponente::with('componente.categoria')->where('producto_kit_id', $this->producto->id)->get()
            : collect();
    }

    public function getProductosDisponiblesProperty()
    {
        return Producto::whereHas('categoria', fn ($q) => $q->where('es_kit', false))
            ->when($this->producto, fn ($q) => $q->where('id', '!=', $this->producto->id))
            ->orderBy('nombre')
            ->get();
    }

    public function agregarComponente()
    {
        $this->validate([
            'componenteId' => 'required|exists:productos,id',
            'cantidadEsperada' => 'required|integer|min:1',
        ]);

        $existe = KitComponente::where('producto_kit_id', $this->producto->id)
            ->where('producto_componente_id', $this->componenteId)
            ->exists();

        if ($existe) {
            $this->addError('componenteId', 'Este componente ya está en la lista.');
            return;
        }

        KitComponente::create([
            'producto_kit_id' => $this->producto->id,
            'producto_componente_id' => $this->componenteId,
            'cantidad_esperada' => $this->cantidadEsperada,
        ]);

        $this->reset(['componenteId', 'cantidadEsperada']);
        $this->cantidadEsperada = 1;
        $this->dispatch('minToast', titulo: 'Agregado', mensaje: 'Componente agregado al kit.', icono: 'success');
    }

    public function quitarComponente(int $kitComponenteId)
    {
        KitComponente::where('id', $kitComponenteId)->delete();
        $this->dispatch('minToast', titulo: 'Quitado', mensaje: 'Componente eliminado del kit.', icono: 'success');
    }
    
    public function render()
    {
        return view('livewire.almacen.productos.definir-componentes');
    }
}
