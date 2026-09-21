<?php

namespace App\Livewire\Cms;

use App\Models\Page;
use App\Models\PageSection;
use Livewire\Component;

class GestionarContenido extends Component
{
    public $pageTitle = '';
    public $sections = [];
    public $sectionData = [];
    public $refreshKey = '';
    public $highlightSection = '';

    public function mount(): void
    {
        $this->loadSections();
    }

    public function loadSections(): void
    {
        $page = Page::firstOrCreate(['slug' => 'home'], [
            'title' => 'Página Principal',
            'is_active' => true,
        ]);

        $this->pageTitle = $page->title;

        $defaultSections = [
            ['key' => 'hero', 'title' => 'Hero / Banner Principal', 'sort_order' => 1],
            ['key' => 'about', 'title' => 'Nosotros', 'sort_order' => 2],
            ['key' => 'services', 'title' => 'Servicios', 'sort_order' => 3],
            ['key' => 'why', 'title' => 'Por Qué Elegirnos', 'sort_order' => 4],
            ['key' => 'process', 'title' => 'Proceso de Trabajo', 'sort_order' => 5],
            ['key' => 'contact', 'title' => 'Contacto', 'sort_order' => 6],
        ];

        foreach ($defaultSections as $s) {
            PageSection::firstOrCreate(
                ['page_id' => $page->id, 'key' => $s['key']],
                $s
            );
        }

        $this->sections = PageSection::where('page_id', $page->id)
            ->with('mediaItems.media')
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        foreach ($this->sections as $section) {
            $this->sectionData[$section['id']] = [
                'title'       => $section['title'],
                'subtitle'    => $section['subtitle'],
                'description' => $section['description'],
                'is_active'   => (bool) $section['is_active'],
            ];
        }
    }

    public function saveSection(int $id): void
    {
        $data = $this->sectionData[$id] ?? null;
        if (!$data) {
            return;
        }

        $this->validate([
            "sectionData.{$id}.title" => 'required|max:255',
        ], [
            "sectionData.{$id}.title.required" => 'El campo Título es obligatorio',
            "sectionData.{$id}.title.max" => 'El campo Título no debe exceder 255 caracteres',
        ]);

        try {
            PageSection::findOrFail($id)->update([
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? '',
                'description' => $data['description'] ?? '',
                'is_active' => $data['is_active'] ?? true,
            ]);

            $section = PageSection::find($id);
            $this->highlightSection = $section ? $section->key : '';
            $this->loadSections();
            $this->refreshKey = time();
            $this->dispatch('minToast', titulo: '¡Guardado!', mensaje: 'Sección actualizada correctamente.', icono: 'success');
            $this->dispatch('refresh-preview');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo actualizar la sección.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.cms.gestionar-contenido');
    }
}
