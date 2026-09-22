<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\CategoriaAlmacen;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\Auth;
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
    public $stockMinimo = 0;
    public int $stockInicial = 0;

    #[On('abrir-modal-producto')]
    public function abrir()
    {
        $this->reset(['categoriaId', 'nombre', 'marca', 'atributos', 'precioReferencial', 'stockMinimo']);
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
        $schema = $categoria?->esquema_atributos ?? [];
        
        
        foreach ($schema as $campo => $valor) {
            if (is_int($campo)) {
                
                $this->atributos[$valor] = '';
            } else {
                
                $this->atributos[$campo] = '';
            }
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
            'stockInicial' => 'nullable|integer|min:0',
        ]);

        try {
            $producto = Producto::create([
                'categoria_id' => $this->categoriaId,
                'nombre' => $this->nombre,
                'marca' => $this->marca ?: null,
                'atributos' => array_filter($this->atributos) ?: null,
                'precio_referencial' => $this->precioReferencial ?: null,
                'stock' => 0,
                'stock_minimo' => $this->stockMinimo,
            ]);

            
            if ($this->stockInicial > 0 && !$producto->categoria->es_serializado) {
                $sedeId = Auth::user()->sede_id ?? 1;
                MovimientoStock::registrar(
                    producto: $producto,
                    tipo: 'entrada',
                    cantidad: $this->stockInicial,
                    serviceOrderId: null,
                    usuarioId: Auth::id(),
                    motivo: 'Stock inicial al crear producto',
                    sedeId: $sedeId
                );
            }
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo crear el producto. Intenta de nuevo.', icono: 'error');
            return;
        }

        $this->mostrarModal = false;
        $this->reset(['categoriaId', 'nombre', 'marca', 'atributos', 'precioReferencial', 'stockInicial']);

        $this->dispatch('producto-creado');

        if ($this->stockInicial > 0 && !$producto->categoria->es_serializado) {
            $this->dispatch('minToast', titulo: '¡Listo!', mensaje: "Producto creado con {$this->stockInicial} unidades en inventario.", icono: 'success');
        } else {
            $this->dispatch('minToast', titulo: '¡Listo!', mensaje: 'Producto creado correctamente. Ahora registra su entrada de stock.', icono: 'success');
        }
    }

    public function render()
    {
        return view('livewire.almacen.productos.crear', [
            'categorias' => CategoriaAlmacen::orderBy('nombre')->get(),
        ]);
    }
}
