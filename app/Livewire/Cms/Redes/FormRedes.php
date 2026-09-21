<?php

namespace App\Livewire\Cms\Redes;

use App\Models\SocialLink;
use Livewire\Component;

class FormRedes extends Component
{
    public bool $mostrarModal = false;
    public ?int $editingId = null;
    public string $platform = 'facebook';
    public string $url = '';
    public string $icon = '';
    public bool $active = true;

    public array $platforms = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'whatsapp' => 'WhatsApp',
        'tiktok' => 'TikTok',
        'youtube' => 'YouTube',
        'twitter' => 'X / Twitter',
        'linkedin' => 'LinkedIn',
    ];

    public array $platformIcons = [
        'facebook' => 'fa-brands fa-facebook-f',
        'instagram' => 'fa-brands fa-instagram',
        'whatsapp' => 'fa-brands fa-whatsapp',
        'tiktok' => 'fa-brands fa-tiktok',
        'youtube' => 'fa-brands fa-youtube',
        'twitter' => 'fa-brands fa-x-twitter',
        'linkedin' => 'fa-brands fa-linkedin-in',
    ];

    public function abrirCrear(): void
    {
        $this->resetForm();
        $this->mostrarModal = true;
    }

    public function abrirEditar(int $id): void
    {
        $link = SocialLink::findOrFail($id);
        $this->editingId = $id;
        $this->platform = $link->platform;
        $this->url = $link->url;
        $this->icon = $link->icon;
        $this->active = $link->is_active;
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
            'platform' => 'required|string|max:50',
            'url' => 'required|url',
        ], [
            'platform.required' => 'El campo Plataforma es obligatorio',
            'platform.max' => 'El campo Plataforma no debe exceder 50 caracteres',
            'url.required' => 'El campo URL es obligatorio',
            'url.url' => 'La URL debe ser válida (ej: https://ejemplo.com)',
        ]);

        if ($this->url && !preg_match('/^https?:\/\//i', $this->url)) {
            $this->url = 'https://' . $this->url;
        }

        $data = [
            'platform' => $this->platform,
            'url' => $this->url,
            'icon' => $this->icon,
            'is_active' => $this->active,
        ];

        try {
            if ($this->editingId) {
                SocialLink::findOrFail($this->editingId)->update($data);
                $this->dispatch('minToast', titulo: '¡Actualizada!', mensaje: 'Red social actualizada correctamente.', icono: 'success');
            } else {
                $data['sort_order'] = SocialLink::max('sort_order') + 1;
                SocialLink::create($data);
                $this->dispatch('minToast', titulo: '¡Creada!', mensaje: 'Red social creada correctamente.', icono: 'success');
            }

            $this->cerrar();
            $this->dispatch('redes-guardada');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo guardar la red social.', icono: 'error');
        }
    }

    public function updatedPlatform(string $value): void
    {
        $this->icon = $this->platformIcons[$value] ?? '';
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'platform', 'url', 'icon', 'active']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.cms.redes.form-redes');
    }
}
