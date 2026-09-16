<?php

namespace App\Livewire\Cms;

use App\Models\ProcessStep;
use Livewire\Component;

class GestionarPasos extends Component
{
    public $steps = [];

    public function mount()
    {
        $this->loadSteps();
    }

    public function loadSteps(): void
    {
        $this->steps = ProcessStep::orderBy('sort_order')->get();
    }

    public function delete(int $id): void
    {
        try {
            ProcessStep::findOrFail($id)->delete();
            $this->loadSteps();
            $this->dispatch('minToast', titulo: '¡Eliminado!', mensaje: 'Paso eliminado correctamente.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo eliminar el paso.', icono: 'error');
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $step = ProcessStep::findOrFail($id);
            $step->update(['is_active' => !$step->is_active]);
            $this->loadSteps();
            $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Estado del paso actualizado.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar el estado.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.cms.gestionar-pasos');
    }
}
