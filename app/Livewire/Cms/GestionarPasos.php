<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use App\Models\ProcessStep;

class GestionarPasos extends Component
{
    public $steps = [];
    public $editingId = null;
    public $title = '';
    public $description = '';
    public $stepNumber = '';
    public $icon = '';
    public $active = true;
    public $showForm = false;

    public function mount()
    {
        $this->loadSteps();
    }

    public function loadSteps()
    {
        $this->steps = ProcessStep::orderBy('sort_order')->get()->toArray();
    }

    public function create()
    {
        $this->resetForm();
        $this->stepNumber = str_pad(count($this->steps) + 1, 2, '0', STR_PAD_LEFT);
        $this->showForm = true;
    }

    public function edit($id)
    {
        $step = ProcessStep::findOrFail($id);
        $this->editingId = $id;
        $this->title = $step->title;
        $this->description = $step->description;
        $this->stepNumber = $step->step_number;
        $this->icon = $step->icon;
        $this->active = $step->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'stepNumber' => 'required|string|max:10',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'step_number' => $this->stepNumber,
            'icon' => $this->icon,
            'is_active' => $this->active,
        ];

        if ($this->editingId) {
            ProcessStep::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Paso actualizado');
        } else {
            $data['sort_order'] = ProcessStep::max('sort_order') + 1;
            ProcessStep::create($data);
            session()->flash('success', 'Paso creado');
        }

        $this->resetForm();
        $this->loadSteps();
    }

    public function delete($id)
    {
        ProcessStep::findOrFail($id)->delete();
        $this->loadSteps();
        session()->flash('success', 'Paso eliminado');
    }

    public function toggleActive($id)
    {
        $step = ProcessStep::findOrFail($id);
        $step->update(['is_active' => !$step->is_active]);
        $this->loadSteps();
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'title', 'description', 'stepNumber', 'icon', 'active', 'showForm']);
    }

    public function render()
    {
        return view('livewire.cms.gestionar-pasos');
    }
}
