<?php

namespace App\Livewire\Cms;

use App\Models\SiteService;
use Livewire\Component;

class GestionarServicios extends Component
{
    public $services = [];

    public function mount()
    {
        $this->loadServices();
    }

    public function loadServices(): void
    {
        $this->services = SiteService::orderBy('sort_order')->get();
    }

    public function delete(int $id): void
    {
        try {
            SiteService::findOrFail($id)->delete();
            $this->loadServices();
            $this->dispatch('minToast', titulo: '¡Eliminado!', mensaje: 'Servicio eliminado correctamente.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo eliminar el servicio.', icono: 'error');
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $service = SiteService::findOrFail($id);
            $service->update(['is_active' => !$service->is_active]);
            $this->loadServices();
            $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Estado del servicio actualizado.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar el estado.', icono: 'error');
        }
    }

    public function moveUp(int $id): void
    {
        $service = SiteService::findOrFail($id);
        if ($service->sort_order > 0) {
            $service->update(['sort_order' => $service->sort_order - 1]);
            $this->loadServices();
        }
    }

    public function moveDown(int $id): void
    {
        $service = SiteService::findOrFail($id);
        $service->update(['sort_order' => $service->sort_order + 1]);
        $this->loadServices();
    }

    public function render()
    {
        return view('livewire.cms.gestionar-servicios');
    }
}
