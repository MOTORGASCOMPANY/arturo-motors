<?php

namespace App\Livewire\Cms;

use App\Models\ContactInfo;
use Livewire\Component;

class GestionarContacto extends Component
{
    public $contacts = [];

    public function mount(): void
    {
        $this->loadContacts();
    }

    public function loadContacts(): void
    {
        $this->contacts = ContactInfo::orderBy('sort_order')->get()->toArray();
    }

    public function delete(int $id): void
    {
        try {
            ContactInfo::findOrFail($id)->delete();
            $this->loadContacts();
            $this->dispatch('minToast', titulo: '¡Eliminado!', mensaje: 'Contacto eliminado correctamente.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo eliminar el contacto.', icono: 'error');
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $contact = ContactInfo::findOrFail($id);
            $contact->update(['is_active' => !$contact->is_active]);
            $this->loadContacts();
            $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Estado del contacto actualizado.', icono: 'success');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar el estado.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.cms.gestionar-contacto');
    }
}
