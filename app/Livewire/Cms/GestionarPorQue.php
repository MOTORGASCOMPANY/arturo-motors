<?php

namespace App\Livewire\Cms;

use App\Models\WhyCard;
use Livewire\Component;

class GestionarPorQue extends Component
{
    public $cards = [];

    public function mount()
    {
        $this->loadCards();
    }

    public function loadCards(): void
    {
        $this->cards = WhyCard::orderBy('sort_order')->get();
    }

    public function delete(int $id): void
    {
        try {
            WhyCard::findOrFail($id)->delete();
            $this->loadCards();
            $this->dispatch('minToast', titulo: '¡Eliminado!', mensaje: 'Tarjeta eliminada correctamente.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo eliminar la tarjeta.', icono: 'error');
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $card = WhyCard::findOrFail($id);
            $card->update(['is_active' => !$card->is_active]);
            $this->loadCards();
            $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Estado de la tarjeta actualizado.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar el estado.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.cms.gestionar-por-que');
    }
}
