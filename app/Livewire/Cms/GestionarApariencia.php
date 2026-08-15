<?php

namespace App\Livewire\Cms;

use App\Models\Page;
use App\Traits\InvalidatesLandingCache;
use Livewire\Component;
use Livewire\WithFileUploads;

class GestionarApariencia extends Component
{
    use InvalidatesLandingCache, WithFileUploads;

    public $page;

    public $colors = [];

    public $successMessage = '';

    public $errorMessage = '';

    public $colorPickerOpen = false;

    public $editingColorKey = null;

    // Default color scheme
    protected $defaultColors = [
        'primary' => '#f59e0b',
        'primary_hover' => '#d97706',
        'secondary' => '#ef4444',
        'secondary_hover' => '#dc2626',
        'accent' => '#3b82f6',
        'background' => '#0a0f1e',
        'surface' => '#1e293b',
        'text_primary' => '#ffffff',
        'text_secondary' => '#cbd5e1',
        'text_muted' => '#94a3b8',
        'border' => '#334155',
        'success' => '#22c55e',
        'warning' => '#f59e0b',
        'error' => '#ef4444',
    ];

    public function mount()
    {
        $this->page = Page::firstOrCreate(['slug' => 'home'], [
            'title' => 'Página Principal',
            'is_active' => true,
        ]);

        $this->loadColors();
    }

    public function loadColors()
    {
        $settings = $this->page->meta ?? [];
        $this->colors = array_merge($this->defaultColors, $settings['colors'] ?? []);
    }

    public function saveColors()
    {
        $this->validate([
            'colors.primary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.secondary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.accent' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.background' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.surface' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.text_primary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.text_secondary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.text_muted' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.border' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            '*.regex' => 'El color debe ser un código hexadecimal válido (ej: #f59e0b)',
        ]);

        $meta = $this->page->meta ?? [];
        $meta['colors'] = $this->colors;
        $this->page->update(['meta' => $meta]);

        $this->invalidateLandingCache();
        $this->successMessage = 'Colores guardados correctamente';
        session()->flash('success', 'Colores actualizados');
    }

    public function resetColors()
    {
        $this->colors = $this->defaultColors;
        $meta = $this->page->meta ?? [];
        $meta['colors'] = $this->defaultColors;
        $this->page->update(['meta' => $meta]);

        $this->invalidateLandingCache();
        $this->successMessage = 'Colores restablecidos a valores por defecto';
        session()->flash('success', 'Colores restablecidos');
    }

    public function clearSuccessMessage()
    {
        $this->successMessage = '';
    }

    public function clearErrorMessage()
    {
        $this->errorMessage = '';
    }

    public function render()
    {
        return view('livewire.cms.gestionar-apariencia');
    }
}
