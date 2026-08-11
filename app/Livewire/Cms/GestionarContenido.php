<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Media;
use App\Models\PageMedia;

class GestionarContenido extends Component
{
    use WithFileUploads;

    public $pageTitle = '';
    public $sections = [];
    public $editingSection = null;
    public $sectionTitle = '';
    public $sectionSubtitle = '';
    public $sectionDescription = '';
    public $sectionKey = '';
    public $sectionActive = true;

    public $uploadSection = null;
    public $uploadUsage = 'image';
    public $uploadFile;

    public function mount()
    {
        $this->loadSections();
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
    }

    public function editSection($id)
    {
        $section = PageSection::findOrFail($id);
        $this->editingSection = $section->id;
        $this->sectionTitle = $section->title;
        $this->sectionSubtitle = $section->subtitle;
        $this->sectionDescription = $section->description;
        $this->sectionKey = $section->key;
        $this->sectionActive = $section->is_active;
    }

    public function saveSection()
    {
        PageSection::findOrFail($this->editingSection)->update([
            'title' => $this->sectionTitle,
            'subtitle' => $this->sectionSubtitle,
            'description' => $this->sectionDescription,
            'is_active' => $this->sectionActive,
        ]);

        $this->editingSection = null;
        $this->reset(['sectionTitle', 'sectionSubtitle', 'sectionDescription', 'sectionKey']);
        $this->loadSections();
        session()->flash('success', 'Sección actualizada');
    }

    public function uploadMedia($sectionId)
    {
        $this->validate([
            'uploadFile' => 'required|image|max:5120',
        ]);

        $path = $this->uploadFile->store('cms', 'public');

        $media = Media::create([
            'name' => $this->uploadFile->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => 'image',
            'mime_type' => $this->uploadFile->getMimeType(),
            'file_size' => $this->uploadFile->getSize(),
        ]);

        PageMedia::create([
            'page_section_id' => $sectionId,
            'media_id' => $media->id,
            'usage' => $this->uploadUsage,
            'sort_order' => 0,
        ]);

        $this->reset(['uploadFile', 'uploadUsage']);
        $this->loadSections();
        session()->flash('success', 'Imagen subida');
    }

    public function removeMedia($pageMediaId)
    {
        $pm = PageMedia::findOrFail($pageMediaId);
        $pm->media->delete();
        $pm->delete();
        $this->loadSections();
        session()->flash('success', 'Imagen eliminada');
    }

    public function render()
    {
        return view('livewire.cms.gestionar-contenido');
    }
}
