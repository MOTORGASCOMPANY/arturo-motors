<?php

namespace App\Livewire\Almacen;

use App\Models\ItemSerializado;
use App\Models\KitComponente;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductoModificacionItem extends Component
{
    public bool $modalEditarItemAbierto = false;
    public ?int $editarItemId = null;
    public array $editarItemData = [];
    public ?array $editarItemKitInfo = null;

    #[On('productos:editar-item:abrir')]
    public function abrirEditarItem(int $itemId): void
    {
        $item = ItemSerializado::with([
            'producto.categoria',
            'kitPadre.producto.categoria',
            'kitPadre.piezasEnKit.producto.categoria',
        ])->find($itemId);
        
        if (!$item) return;

        $this->editarItemId = $itemId;
        $this->editarItemData = [];

        $esquema = $item->producto->categoria->esquema_atributos ?? ['serie'];
        $campos = is_string($esquema) ? json_decode($esquema, true) : $esquema;
        $attrs = $item->atributos ?? [];

        foreach ($campos as $campo) {
            $this->editarItemData[$campo] = $attrs[$campo] ?? '';
        }

        // Determinar a qué componente de la receta del kit padre pertenece
        $this->editarItemKitInfo = null;
        if ($item->kit_padre_id) {
            $kitPadre = $item->kitPadre;
            if ($kitPadre) {
                $receta = KitComponente::recetaDe($kitPadre->producto_id);

                $piezasActuales = $kitPadre->piezasEnKit->pluck('producto_id')->countBy()->toArray();
                
                // Buscar qué componente corresponde a este item
                foreach ($receta as $r) {
                    $esperados = $r->cantidad;
                    $presentes = $piezasActuales[$r->producto_id] ?? 0;
                    // Si este item es de este producto y aún no se completó la cuota
                    if ($r->producto_id === $item->producto_id && $presentes <= $esperados) {
                        $this->editarItemKitInfo = [
                            'kit_nombre' => $kitPadre->producto?->nombre ?? 'Kit',
                            'kit_id' => $kitPadre->id,
                            'kit_serie' => $kitPadre->serie,
                            'componente_nombre' => $r->nombre,
                            'componente_esperados' => $esperados,
                            'componente_presentes' => $presentes,
                        ];
                        break;
                    }
                }
            }
        }

        $this->modalEditarItemAbierto = true;
    }

    public function guardarEditarItem(): void
    {
        $item = ItemSerializado::find($this->editarItemId);
        if (!$item) return;

        $attrs = $item->atributos ?? [];
        foreach ($this->editarItemData as $campo => $valor) {
            if ($valor !== '' && $valor !== null) {
                $attrs[$campo] = $valor;
            }
        }

        if ($this->editarItemData['serie'] ?? null) {
            $item->serie = $this->editarItemData['serie'];
        }

        $item->atributos = $attrs;
        $item->save();

        $this->modalEditarItemAbierto = false;
        $this->editarItemId = null;
        $this->editarItemData = [];

        $this->dispatch('minAlert', titulo: 'Listo!', mensaje: 'Item actualizado correctamente.', icono: 'success');
    }

    public function cerrarEditarItem(): void
    {
        $this->modalEditarItemAbierto = false;
        $this->editarItemId = null;
        $this->editarItemData = [];
        $this->editarItemKitInfo = null;
    }

    public function render()
    {
        return view('livewire.almacen.producto-modificacion-item');
    }
}
