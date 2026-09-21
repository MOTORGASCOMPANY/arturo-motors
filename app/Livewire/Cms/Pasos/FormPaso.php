<?php

namespace App\Livewire\Cms\Pasos;

use App\Models\ProcessStep;
use Livewire\Component;

class FormPaso extends Component
{
    public bool $mostrarModal = false;
    public ?int $editingId = null;
    public string $title = '';
    public string $description = '';
    public string $stepNumber = '';
    public bool $active = true;

    public function abrirCrear(int $totalSteps): void
    {
        $this->resetForm();
        $this->stepNumber = str_pad($totalSteps + 1, 2, '0', STR_PAD_LEFT);
        $this->mostrarModal = true;
    }

    public function abrirEditar(int $id): void
    {
        $step = ProcessStep::findOrFail($id);
        $this->editingId = $id;
        $this->title = $step->title;
        $this->description = $step->description ?? '';
        $this->stepNumber = $step->step_number;
        $this->active = $step->is_active;
        $this->mostrarModal = true;
    }

    public function cerrar(): void
    {
        $this->mostrarModal = false;
        $this->resetForm();
    }

    public function guardar(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'stepNumber' => 'required|string|max:10',
        ], [
            'title.required' => 'El campo Título es obligatorio',
            'title.max' => 'El campo Título no debe exceder 255 caracteres',
            'stepNumber.required' => 'El campo Número de paso es obligatorio',
            'stepNumber.max' => 'El campo Número de paso no debe exceder 10 caracteres',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'step_number' => $this->stepNumber,
            'is_active' => $this->active,
        ];

        try {
            if ($this->editingId) {
                ProcessStep::findOrFail($this->editingId)->update($data);
                $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Paso actualizado correctamente.', icono: 'success');
            } else {
                $data['sort_order'] = ProcessStep::max('sort_order') + 1;
                ProcessStep::create($data);
                $this->dispatch('minToast', titulo: '¡Creado!', mensaje: 'Paso creado correctamente.', icono: 'success');
            }

            $this->cerrar();
            $this->dispatch('paso-guardado');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo guardar el paso.', icono: 'error');
        }
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'description', 'stepNumber', 'active']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.cms.pasos.form-paso');
    }
}
