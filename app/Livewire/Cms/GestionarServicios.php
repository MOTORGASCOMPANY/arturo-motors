<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use App\Models\SiteService;

class GestionarServicios extends Component
{
    public $services = [];
    public $editingId = null;
    public $title = '';
    public $description = '';
    public $icon = '';
    public $features = '';
    public $ctaText = '';
    public $ctaLink = '';
    public $active = true;
    public $showForm = false;

    public $serviceIcons = [
        'gnv' => 'fa-solid fa-gas-pump',
        'glp' => 'fa-solid fa-gas-pump',
        'conversión' => 'fa-solid fa-gas-pump',
        'conver' => 'fa-solid fa-gas-pump',
        'certific' => 'fa-solid fa-file-signature',
        'inspección' => 'fa-solid fa-file-signature',
        'mantenimiento' => 'fa-solid fa-sliders',
        'gas' => 'fa-solid fa-sliders',
        'mecánica' => 'fa-solid fa-screwdriver-wrench',
        'motor' => 'fa-solid fa-screwdriver-wrench',
        'frenos' => 'fa-solid fa-screwdriver-wrench',
        'diagnóstico' => 'fa-solid fa-laptop-code',
        'escáner' => 'fa-solid fa-laptop-code',
        'escaneo' => 'fa-solid fa-laptop-code',
        'inyector' => 'fa-solid fa-filter-circle-xmark',
        'lavado' => 'fa-solid fa-filter-circle-xmark',
        'filtro' => 'fa-solid fa-filter-circle-xmark',
    ];

    public function mount()
    {
        $this->loadServices();
    }

    public function loadServices()
    {
        $this->services = SiteService::orderBy('sort_order')->get()->toArray();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $service = SiteService::findOrFail($id);
        $this->editingId = $id;
        $this->title = $service->title;
        $this->description = $service->description;
        $this->icon = $service->icon;
        $this->features = is_array($service->features) ? implode("\n", $service->features) : '';
        $this->ctaText = $service->cta_text;
        $this->ctaLink = $service->cta_link;
        $this->active = $service->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $featuresArray = array_filter(array_map('trim', explode("\n", $this->features)));

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'features' => $featuresArray ?: null,
            'cta_text' => $this->ctaText,
            'cta_link' => $this->ctaLink,
            'is_active' => $this->active,
        ];

        if ($this->editingId) {
            SiteService::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Servicio actualizado');
        } else {
            $data['sort_order'] = SiteService::max('sort_order') + 1;
            SiteService::create($data);
            session()->flash('success', 'Servicio creado');
        }

        $this->resetForm();
        $this->loadServices();
    }

    public function delete($id)
    {
        SiteService::findOrFail($id)->delete();
        $this->loadServices();
        session()->flash('success', 'Servicio eliminado');
    }

    public function toggleActive($id)
    {
        $service = SiteService::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);
        $this->loadServices();
    }

    public function moveUp($id)
    {
        $service = SiteService::findOrFail($id);
        if ($service->sort_order > 0) {
            $service->update(['sort_order' => $service->sort_order - 1]);
            $this->loadServices();
        }
    }

    public function moveDown($id)
    {
        $service = SiteService::findOrFail($id);
        $service->update(['sort_order' => $service->sort_order + 1]);
        $this->loadServices();
    }

    public function updatedTitle()
    {
        $titleLower = mb_strtolower($this->title);
        foreach ($this->serviceIcons as $keyword => $icon) {
            if (str_contains($titleLower, $keyword)) {
                $this->icon = $icon;
                return;
            }
        }
    }

    public function resetForm()
    {
        $this->reset(['editingId', 'title', 'description', 'icon', 'features', 'ctaText', 'ctaLink', 'active', 'showForm']);
    }

    public function render()
    {
        return view('livewire.cms.gestionar-servicios');
    }
}
