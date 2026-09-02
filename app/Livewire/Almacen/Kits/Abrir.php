<?php

namespace App\Livewire\Almacen\Kits;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Abrir extends Component
{
    public bool $mostrarModal = false;
    public ?ItemSerializado $kitItem = null;
    public $componentesKit;
    public array $seriesComponentes = [];

    #[On('abrir-modal-abrir-kit')]
    public function abrir(int $itemId)
    {
        $this->kitItem = ItemSerializado::with('producto')->findOrFail($itemId);
        $this->componentesKit = KitComponente::with('componente.categoria')
            ->where('producto_kit_id', $this->kitItem->producto_id)
            ->get();

        $this->seriesComponentes = [];
        foreach ($this->componentesKit as $kc) {
            if ($kc->componente->categoria->es_serializado) {
                $this->seriesComponentes[$kc->producto_componente_id] = '';
            }
        }

        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function cerrar()
    {
        $this->mostrarModal = false;
    }

    public function confirmarApertura()
    {
        if ($this->componentesKit->isEmpty()) {
            $this->addError('general', 'Este kit no tiene componentes definidos. Configúralos primero desde Productos.');
            return;
        }

        foreach ($this->seriesComponentes as $productoId => $serie) {
            if (trim($serie) === '') {
                $this->addError("seriesComponentes.$productoId", 'Ingresa el número de serie.');
                return;
            }
        }

        try {
            DB::transaction(function () {
                $item = ItemSerializado::where('id', $this->kitItem->id)
                    ->where('estado', 'en_stock')
                    ->lockForUpdate()
                    ->first();

                if (!$item) {
                    throw new \RuntimeException('Este kit ya no está disponible para abrir. Actualiza la lista.');
                }

                foreach ($this->componentesKit as $kc) {
                    if ($kc->componente->categoria->es_serializado) {
                        $serie = trim($this->seriesComponentes[$kc->producto_componente_id]);

                        if (ItemSerializado::where('serie', $serie)->exists()) {
                            throw new \RuntimeException("La serie {$serie} ya existe en el sistema.");
                        }

                        ItemSerializado::create([
                            'producto_id' => $kc->producto_componente_id,
                            'serie' => $serie,
                            'estado' => 'en_stock',
                            'sede_id' => 1,
                        ]);
                    } else {
                        MovimientoStock::registrar(
                            $kc->componente, 'entrada', $kc->cantidad_esperada, null, Auth::id(),
                            'Apertura de kit #' . $item->id, 1
                        );
                    }
                }

                $item->update(['estado' => 'abierto']);
            });
        } catch (\RuntimeException $e) {
            $this->addError('general', $e->getMessage());
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('general', 'Ocurrió un error al abrir el kit. Intenta de nuevo.');
            return;
        }

        $this->mostrarModal = false;
        $this->dispatch('kit-abierto');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: 'Kit abierto. Los componentes ya están en stock.', icono: 'success');
    }
    
    public function render()
    {
        return view('livewire.almacen.kits.abrir');
    }
}
