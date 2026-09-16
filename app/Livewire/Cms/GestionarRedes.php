<?php

namespace App\Livewire\Cms;

use App\Models\SocialLink;
use Livewire\Component;

class GestionarRedes extends Component
{
    public $links = [];

    public function mount(): void
    {
        $this->loadLinks();
    }

    public function loadLinks(): void
    {
        $this->links = SocialLink::orderBy('sort_order')->get()->toArray();
    }

    public function delete(int $id): void
    {
        try {
            SocialLink::findOrFail($id)->delete();
            $this->loadLinks();
            $this->dispatch('minToast', titulo: '¡Eliminada!', mensaje: 'Red social eliminada correctamente.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo eliminar la red social.', icono: 'error');
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $link = SocialLink::findOrFail($id);
            $link->update(['is_active' => !$link->is_active]);
            $this->loadLinks();
            $this->dispatch('minToast', titulo: '¡Actualizada!', mensaje: 'Estado de la red social actualizado.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar el estado.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.cms.gestionar-redes');
    }
}
