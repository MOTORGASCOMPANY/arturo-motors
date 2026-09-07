<?php

namespace App\Livewire\Cms;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageMedia;
use App\Models\PageSection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class GestionarContenido extends Component
{
    use WithFileUploads;

    public $pageTitle = '';

    public $sections = [];

    public $sectionData = [];

    public $uploadSection = null;

    public $uploadUsage = 'image';

    public $uploadFile;

    public $successMessage = '';

    public $errorMessage = '';

    public $refreshKey = '';

    public $highlightSection = '';

    public function mount()
    {
        $this->loadSections();
    }

    #[On('mediaUploaded')]
    public function refreshAfterUpload($sectionId)
    {
        $this->loadSections();
        $this->refreshKey = time();
        $section = PageSection::find($sectionId);
        $this->highlightSection = $section ? $section->key : '';
    }

    public function loadSections()
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

        // Precargar sectionData para todas las secciones
        foreach ($this->sections as $section) {
            $this->sectionData[$section['id']] = [
                'title'       => $section['title'],
                'subtitle'    => $section['subtitle'],
                'description' => $section['description'],
                'is_active'   => (bool) $section['is_active'],
            ];
        }
    }

    public function editSection($id)
    {
        $section = PageSection::findOrFail($id);
        $this->sectionData[$id] = [
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'description' => $section->description,
            'is_active' => (bool) $section->is_active,
        ];
    }

    public function saveSection($id)
    {
        $data = $this->sectionData[$id] ?? null;
        if (! $data) {
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
            $this->successMessage = 'Sección actualizada correctamente';
            session()->flash('success', 'Sección actualizada');
        } catch (\Throwable $e) {
            $this->errorMessage = 'No se pudo actualizar la sección';
        }
    }

    public function uploadMedia($sectionId)
    {
        $this->dispatch('uploading');

        $this->validate([
            'uploadFile' => 'required|image|max:5120',
        ], [
            'uploadFile.required' => 'Seleccioná una imagen para subir',
            'uploadFile.image' => 'La imagen debe ser JPG, PNG o WebP',
            'uploadFile.max' => 'La imagen no debe exceder 5MB',
        ]);

        try {
            $path = $this->uploadFile->store('cms', 'public');

            $media = Media::create([
                'name' => $this->uploadFile->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => 'image',
                'mime_type' => $this->uploadFile->getMimeType(),
                'file_size' => $this->uploadFile->getSize(),
            ]);

            $pageMedia = PageMedia::create([
                'page_section_id' => $sectionId,
                'media_id' => $media->id,
                'usage' => $this->uploadUsage,
                'sort_order' => 0,
            ]);

            // Update local state without full reload
            foreach ($this->sections as &$section) {
                if ($section['id'] == $sectionId) {
                    $section['media_items'][] = [
                        'id' => $pageMedia->id,
                        'media' => [
                            'file_path' => $path,
                        ],
                    ];
                    break;
                }
            }
            unset($section);

            $section = PageSection::find($sectionId);
            $this->highlightSection = $section ? $section->key : '';
            $this->reset(['uploadFile', 'uploadUsage']);
            $this->successMessage = 'Imagen subida correctamente';
            session()->flash('success', 'Imagen subida');

            $this->dispatch('upload-done');
        } catch (\Throwable $e) {
            $this->errorMessage = 'No se pudo subir la imagen';
            $this->dispatch('upload-done');
        }
    }

    public function removeMedia($pageMediaId)
    {
        try {
            $pm = PageMedia::findOrFail($pageMediaId);
            $media = $pm->media;

            // Delete physical file and optimized versions from storage
            if ($media && $media->file_path) {
                \Storage::disk('public')->delete($media->file_path);

                // Delete optimized versions (webp, responsive sizes) — no falla si no están instaladas
                try {
                    $optimizationService = app(\App\Services\ImageOptimizationService::class);
                    $optimizationService->deleteOptimizedVersions($media->file_path);
                } catch (\Throwable $optError) {
                    \Log::warning('removeMedia: no se pudieron borrar versiones optimizadas', ['error' => $optError->getMessage()]);
                }
            }

            // SIEMPRE borrar las filas de DB sin importar si falló la limpieza de optimizados
            $media->delete();
            $pm->delete();
            $this->loadSections();
            $this->refreshKey = time();
            $this->successMessage = 'Imagen eliminada';
            session()->flash('success', 'Imagen eliminada');
        } catch (\Throwable $e) {
            \Log::error('removeMedia ERROR', ['message' => $e->getMessage()]);
            $this->errorMessage = 'No se pudo eliminar la imagen';
        }
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
        return view('livewire.cms.gestionar-contenido');
    }
}