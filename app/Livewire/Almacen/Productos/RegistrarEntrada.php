<?php

namespace App\Livewire\Almacen\Productos;

use App\Models\Producto;
use App\Models\ItemSerializado;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RegistrarEntrada extends Component
{
    public bool $mostrarModal = false;

    public ?Producto $producto = null;

    // Para serializados: se agregan series una por una a una lista antes de guardar
    public string $nuevaSerie = '';
    public array $seriesPendientes = [];

    // Para no serializados: cantidad directa
    public int $cantidadEntrada = 1;

    /*public function mount(int $productoId)
    {
        $this->producto = Producto::with('categoria')->findOrFail($productoId);
    }*/

    #[On('abrir-modal-entrada')]
    public function abrir(int $productoId)
    {
        $this->producto = Producto::with('categoria')->findOrFail($productoId);
        $this->reset(['nuevaSerie', 'seriesPendientes', 'cantidadEntrada']);
        $this->cantidadEntrada = 1;
        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function cerrar()
    {
        $this->mostrarModal = false;
    }    

    public function agregarSerie()
    {
        $this->validate(['nuevaSerie' => 'required|string|max:100']);

        $serie = trim($this->nuevaSerie);

        if (in_array($serie, $this->seriesPendientes) || ItemSerializado::where('serie', $serie)->exists()) {
            $this->addError('nuevaSerie', 'Esta serie ya existe o ya fue agregada a la lista.');
            return;
        }

        $this->seriesPendientes[] = $serie;
        $this->nuevaSerie = '';
    }

    public function quitarSerie(int $index)
    {
        unset($this->seriesPendientes[$index]);
        $this->seriesPendientes = array_values($this->seriesPendientes);
    }

    public function guardarSeries()
    {
        if (empty($this->seriesPendientes)) {
            $this->dispatch('minAlert', titulo: 'Atención', mensaje: 'Agrega al menos una serie antes de guardar.', icono: 'warning');
            return;
        }

        try {
            DB::transaction(function () {
                foreach ($this->seriesPendientes as $serie) {
                    ItemSerializado::create([
                        'producto_id' => $this->producto->id,
                        'serie' => $serie,
                        'estado' => 'en_stock',
                        'sede_id' => 1,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'Ocurrió un error al guardar. Revisa si alguna serie ya existe.', icono: 'error');
            return;
        }

        $cantidad = count($this->seriesPendientes);
        $this->mostrarModal = false;

        $this->dispatch('entrada-registrada');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: "{$cantidad} unidad(es) agregadas a stock.", icono: 'success');

        //session()->flash('mensaje', count($this->seriesPendientes) . ' unidad(es) agregadas a stock.');
        //$this->redirect(route('almacen.productos.listado'), navigate: true);
    }

    public function guardarCantidad()
    {
        $this->validate(['cantidadEntrada' => 'required|integer|min:1']);

        MovimientoStock::registrar(
            $this->producto, 'entrada', $this->cantidadEntrada, null, Auth::id(), 'Compra / reposición', 1
        );

        $cantidad = $this->cantidadEntrada;
        $this->mostrarModal = false;

        $this->dispatch('entrada-registrada');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: "Se agregaron {$cantidad} unidades al stock.", icono: 'success');

        //session()->flash('mensaje', "Se agregaron {$this->cantidadEntrada} unidades al stock.");
        //$this->redirect(route('almacen.productos.listado'), navigate: true);
    }

    public function render()
    {
        return view('livewire.almacen.productos.registrar-entrada');
    }
}
