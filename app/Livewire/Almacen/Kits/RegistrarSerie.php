<?php

namespace App\Livewire\Almacen\Kits;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use App\Models\Producto;
use Livewire\Attributes\On;
use Livewire\Component;

class RegistrarSerie extends Component
{
    public bool $mostrarModal = false;
    public ?ItemSerializado $kitItem = null;
    public ?Producto $productoReductor = null;
    public string $serieReductor = '';

    #[On('abrir-modal-registrar-serie')]
    public function abrir(int $itemId)
    {
        $this->kitItem = ItemSerializado::with('producto')->findOrFail($itemId);

        $componenteSerializado = KitComponente::with('componente')
            ->where('producto_kit_id', $this->kitItem->producto_id)
            ->whereHas('componente.categoria', fn ($q) => $q->where('es_serializado', true))
            ->first();

        $this->productoReductor = $componenteSerializado?->componente;

        // Si ya se había registrado antes, precarga el valor existente para poder corregirlo
        $existente = ItemSerializado::where('kit_padre_id', $this->kitItem->id)->first();
        $this->serieReductor = $existente->serie ?? '';

        $this->resetErrorBag();
        $this->mostrarModal = true;
    }

    public function cerrar()
    {
        $this->mostrarModal = false;
    }

    public function guardar()
    {
        $this->validate(['serieReductor' => 'required|string|max:100']);

        $existente = ItemSerializado::where('kit_padre_id', $this->kitItem->id)->first();

        try {
            if ($existente) {
                $existente->update(['serie' => $this->serieReductor]);
            } else {
                ItemSerializado::create([
                    'producto_id' => $this->productoReductor->id,
                    'kit_padre_id' => $this->kitItem->id,
                    'serie' => $this->serieReductor,
                    'estado' => 'en_kit',
                    'sede_id' => $this->kitItem->sede_id,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // La restricción unique de la columna "serie" es la que detecta el duplicado
            $this->addError('serieReductor', 'Esta serie ya está registrada en otro equipo del sistema.');
            return;
        }

        $this->mostrarModal = false;
        $this->dispatch('kit-abierto');
        $this->dispatch('minToast', titulo: '¡Listo!', mensaje: 'Serie registrada. El kit sigue sellado.', icono: 'success');
    }
    
    public function render()
    {
        return view('livewire.almacen.kits.registrar-serie');
    }
}
