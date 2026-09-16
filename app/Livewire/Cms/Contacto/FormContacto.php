<?php

namespace App\Livewire\Cms\Contacto;

use App\Models\ContactInfo;
use Livewire\Component;

class FormContacto extends Component
{
    public bool $mostrarModal = false;
    public ?int $editingId = null;
    public string $type = 'address';
    public string $label = '';
    public string $value = '';
    public string $icon = '';
    public bool $active = true;

    public array $types = [
        'address' => 'Dirección',
        'phone' => 'Teléfono',
        'schedule' => 'Horario',
        'whatsapp' => 'WhatsApp',
        'email' => 'Correo',
        'map_iframe' => 'Mapa (iframe)',
    ];

    public array $typeIcons = [
        'address' => 'fa-solid fa-map-location-dot',
        'phone' => 'fa-solid fa-phone',
        'schedule' => 'fa-solid fa-clock',
        'whatsapp' => 'fa-brands fa-whatsapp',
        'email' => 'fa-solid fa-envelope',
        'map_iframe' => 'fa-solid fa-map',
    ];

    public function abrirCrear(): void
    {
        $this->resetForm();
        $this->mostrarModal = true;
    }

    public function abrirEditar(int $id): void
    {
        $contact = ContactInfo::findOrFail($id);
        $this->editingId = $id;
        $this->type = $contact->type;
        $this->label = $contact->label;
        $this->value = $contact->value;
        $this->icon = $contact->icon;
        $this->active = $contact->is_active;
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
            'type' => 'required|in:address,phone,schedule,whatsapp,map_iframe',
            'label' => 'required|string|max:255',
            'value' => 'required|string',
        ], [
            'type.required' => 'El campo Tipo es obligatorio',
            'type.in' => 'Seleccioná un tipo válido',
            'label.required' => 'El campo Etiqueta es obligatorio',
            'label.max' => 'El campo Etiqueta no debe exceder 255 caracteres',
            'value.required' => 'El campo Valor es obligatorio',
        ]);

        $data = [
            'type' => $this->type,
            'label' => $this->label,
            'value' => $this->value,
            'icon' => $this->icon,
            'is_active' => $this->active,
        ];

        try {
            if ($this->editingId) {
                ContactInfo::findOrFail($this->editingId)->update($data);
                $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Contacto actualizado correctamente.', icono: 'success');
            } else {
                $data['sort_order'] = ContactInfo::max('sort_order') + 1;
                ContactInfo::create($data);
                $this->dispatch('minToast', titulo: '¡Creado!', mensaje: 'Contacto creado correctamente.', icono: 'success');
            }

            $this->cerrar();
            $this->dispatch('contacto-guardado');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo guardar el contacto.', icono: 'error');
        }
    }

    public function updatedType(string $value): void
    {
        $this->icon = $this->typeIcons[$value] ?? '';
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'type', 'label', 'value', 'icon', 'active']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.cms.contacto.form-contacto');
    }
}
