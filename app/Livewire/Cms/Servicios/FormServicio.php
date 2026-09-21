<?php

namespace App\Livewire\Cms\Servicios;

use App\Models\SiteService;
use Livewire\Component;

class FormServicio extends Component
{
    public bool $mostrarModal = false;
    public ?int $editingId = null;
    public string $title = '';
    public string $description = '';
    public string $icon = '';
    public string $features = '';
    public string $ctaText = '';
    public string $ctaLink = '';
    public bool $active = true;

    public array $serviceIcons = [
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

    public function abrirCrear(): void
    {
        $this->resetForm();
        $this->mostrarModal = true;
    }

    public function abrirEditar(int $id): void
    {
        $service = SiteService::findOrFail($id);
        $this->editingId = $id;
        $this->title = $service->title;
        $this->description = $service->description ?? '';
        $this->icon = $service->icon ?? '';
        $this->features = is_array($service->features) ? implode("\n", $service->features) : '';
        $this->ctaText = $service->cta_text ?? '';
        $this->ctaLink = $service->cta_link ?? '';
        $this->active = $service->is_active;
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
            'description' => 'nullable|string',
            'ctaLink' => 'nullable|url',
        ], [
            'title.required' => 'El campo Título es obligatorio',
            'title.max' => 'El campo Título no debe exceder 255 caracteres',
            'ctaLink.url' => 'La URL debe ser válida (ej: https://ejemplo.com)',
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

        try {
            if ($this->editingId) {
                SiteService::findOrFail($this->editingId)->update($data);
                $this->dispatch('minToast', titulo: '¡Actualizado!', mensaje: 'Servicio actualizado correctamente.', icono: 'success');
            } else {
                $data['sort_order'] = SiteService::max('sort_order') + 1;
                SiteService::create($data);
                $this->dispatch('minToast', titulo: '¡Creado!', mensaje: 'Servicio creado correctamente.', icono: 'success');
            }

            $this->cerrar();
            $this->dispatch('servicio-guardado');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo guardar el servicio.', icono: 'error');
        }
    }

    public function updatedTitle(string $value): void
    {
        $titleLower = mb_strtolower($value);
        foreach ($this->serviceIcons as $keyword => $icon) {
            if (str_contains($titleLower, $keyword)) {
                $this->icon = $icon;
                return;
            }
        }
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'description', 'icon', 'features', 'ctaText', 'ctaLink', 'active']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.cms.servicios.form-servicio');
    }
}
