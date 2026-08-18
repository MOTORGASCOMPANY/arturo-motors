<?php

namespace App\Livewire\Cms;

use App\Models\WhyCard;
use Livewire\Component;

class GestionarPorQue extends Component
{
    public $cards = [];

    public $editingId = null;

    public $title = '';

    public $description = '';

    public $icon = '';

    public $active = true;

    public $showForm = false;

    public $successMessage = '';

    public function mount()
    {
        $this->loadCards();
    }

    public function loadCards()
    {
        $this->cards = WhyCard::orderBy('sort_order')->get()->toArray();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $card = WhyCard::findOrFail($id);
        $this->editingId = $id;
        $this->title = $card->title;
        $this->description = $card->description;
        $this->icon = $card->icon;
        $this->active = $card->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'El campo Título es obligatorio',
            'title.max' => 'El campo Título no debe exceder 255 caracteres',
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'is_active' => $this->active,
        ];

        if ($this->editingId) {
            WhyCard::findOrFail($this->editingId)->update($data);
            $this->successMessage = 'Tarjeta actualizada correctamente';
        } else {
            $data['sort_order'] = WhyCard::max('sort_order') + 1;
            WhyCard::create($data);
            $this->successMessage = 'Tarjeta creada correctamente';
        }

        $this->resetForm();
        $this->loadCards();
    }

    public function delete($id)
    {
        WhyCard::findOrFail($id)->delete();
        $this->loadCards();
        $this->successMessage = 'Tarjeta eliminada';
    }

    public function toggleActive($id)
    {
        $card = WhyCard::findOrFail($id);
        $card->update(['is_active' => ! $card->is_active]);
        $this->loadCards();
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'title', 'description', 'icon', 'active', 'showForm']);
    }

    public function clearSuccessMessage()
    {
        $this->successMessage = '';
    }

    public function render()
    {
        return view('livewire.cms.gestionar-por-que');
    }
}