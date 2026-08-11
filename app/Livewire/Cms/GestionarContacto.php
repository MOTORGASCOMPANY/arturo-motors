<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use App\Models\ContactInfo;

class GestionarContacto extends Component
{
    public $contacts = [];
    public $editingId = null;
    public $type = 'address';
    public $label = '';
    public $value = '';
    public $icon = '';
    public $active = true;
    public $showForm = false;

    public $types = [
        'address' => 'Dirección',
        'phone' => 'Teléfono',
        'schedule' => 'Horario',
        'whatsapp' => 'WhatsApp',
        'email' => 'Correo',
        'map_iframe' => 'Mapa (iframe)',
    ];

    public $typeIcons = [
        'address' => 'fa-solid fa-map-location-dot',
        'phone' => 'fa-solid fa-phone',
        'schedule' => 'fa-solid fa-clock',
        'whatsapp' => 'fa-brands fa-whatsapp',
        'email' => 'fa-solid fa-envelope',
        'map_iframe' => 'fa-solid fa-map',
    ];

    public function mount()
    {
        $this->loadContacts();
    }

    public function loadContacts()
    {
        $this->contacts = ContactInfo::orderBy('sort_order')->get()->toArray();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $contact = ContactInfo::findOrFail($id);
        $this->editingId = $id;
        $this->type = $contact->type;
        $this->label = $contact->label;
        $this->value = $contact->value;
        $this->icon = $contact->icon;
        $this->active = $contact->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'type' => 'required|in:address,phone,schedule,whatsapp,map_iframe',
            'label' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        $data = [
            'type' => $this->type,
            'label' => $this->label,
            'value' => $this->value,
            'icon' => $this->icon,
            'is_active' => $this->active,
        ];

        if ($this->editingId) {
            ContactInfo::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Contacto actualizado');
        } else {
            $data['sort_order'] = ContactInfo::max('sort_order') + 1;
            ContactInfo::create($data);
            session()->flash('success', 'Contacto creado');
        }

        $this->resetForm();
        $this->loadContacts();
    }

    public function delete($id)
    {
        ContactInfo::findOrFail($id)->delete();
        $this->loadContacts();
        session()->flash('success', 'Contacto eliminado');
    }

    public function toggleActive($id)
    {
        $contact = ContactInfo::findOrFail($id);
        $contact->update(['is_active' => !$contact->is_active]);
        $this->loadContacts();
    }

    public function updatedType()
    {
        $this->icon = $this->typeIcons[$this->type] ?? '';
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'type', 'label', 'value', 'icon', 'active', 'showForm']);
    }

    public function render()
    {
        return view('livewire.cms.gestionar-contacto');
    }
}
