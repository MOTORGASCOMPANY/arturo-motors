<?php

namespace App\Livewire\Cms;

use App\Models\SocialLink;
use Livewire\Component;

class GestionarRedes extends Component
{
    public $links = [];

    public $editingId = null;

    public $platform = 'facebook';

    public $url = '';

    public $icon = '';

    public $active = true;

    public $showForm = false;

    public $successMessage = '';

    public $platforms = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'whatsapp' => 'WhatsApp',
        'tiktok' => 'TikTok',
        'youtube' => 'YouTube',
        'twitter' => 'X / Twitter',
        'linkedin' => 'LinkedIn',
    ];

    public $platformIcons = [
        'facebook' => 'fa-brands fa-facebook-f',
        'instagram' => 'fa-brands fa-instagram',
        'whatsapp' => 'fa-brands fa-whatsapp',
        'tiktok' => 'fa-brands fa-tiktok',
        'youtube' => 'fa-brands fa-youtube',
        'twitter' => 'fa-brands fa-x-twitter',
        'linkedin' => 'fa-brands fa-linkedin-in',
    ];

    public function mount()
    {
        $this->loadLinks();
    }

    public function loadLinks()
    {
        $this->links = SocialLink::orderBy('sort_order')->get()->toArray();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $link = SocialLink::findOrFail($id);
        $this->editingId = $id;
        $this->platform = $link->platform;
        $this->url = $link->url;
        $this->icon = $link->icon;
        $this->active = $link->is_active;
        $this->showForm = true;
    }

    public function updatedPlatform()
    {
        $this->icon = $this->platformIcons[$this->platform] ?? '';
    }

    public function save()
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

        if ($this->url && ! preg_match('/^https?:\/\//i', $this->url)) {
            $this->url = 'https://'.$this->url;
        }

        $data = [
            'platform' => $this->platform,
            'url' => $this->url,
            'icon' => $this->icon,
            'is_active' => $this->active,
        ];

        if ($this->editingId) {
            SocialLink::findOrFail($this->editingId)->update($data);
            $this->successMessage = 'Red social actualizada correctamente';
            session()->flash('success', 'Red actualizada');
        } else {
            $data['sort_order'] = SocialLink::max('sort_order') + 1;
            SocialLink::create($data);
            $this->successMessage = 'Red social creada correctamente';
            session()->flash('success', 'Red creada');
        }

        $this->resetForm();
        $this->loadLinks();
    }

    public function delete($id)
    {
        SocialLink::findOrFail($id)->delete();
        $this->loadLinks();
        $this->successMessage = 'Red social eliminada';
        session()->flash('success', 'Red eliminada');
    }

    public function toggleActive($id)
    {
        $link = SocialLink::findOrFail($id);
        $link->update(['is_active' => ! $link->is_active]);
        $this->loadLinks();
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'platform', 'url', 'icon', 'active', 'showForm']);
    }

    public function clearSuccessMessage()
    {
        $this->successMessage = '';
    }

    public function render()
    {
        return view('livewire.cms.gestionar-redes');
    }
}