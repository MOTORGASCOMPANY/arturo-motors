<?php

namespace App\Livewire\Cms;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageMedia;
use App\Services\ImageOptimizationService;
use App\Traits\InvalidatesLandingCache;
use Livewire\Component;
use Livewire\WithFileUploads;

class GestionarLogo extends Component
{
    use InvalidatesLandingCache, WithFileUploads;

    public $page;

    public $logoFile;

    public $faviconFile;

    public $logoPreview = '';

    public $faviconPreview = '';

    public $currentLogo = null;

    public $currentFavicon = null;

    public $successMessage = '';

    public $errorMessage = '';

    public function mount()
    {
        $this->page = Page::firstOrCreate(['slug' => 'home'], [
            'title' => 'Página Principal',
            'is_active' => true,
        ]);

        $this->loadCurrentAssets();
    }

    public function loadCurrentAssets()
    {
        $meta = $this->page->meta ?? [];

        // Load logo
        if (! empty($meta['logo_media_id'])) {
            $media = Media::find($meta['logo_media_id']);
            if ($media) {
                $this->currentLogo = $media;
                $this->logoPreview = $media->webpUrl() ?? asset('storage/'.$media->file_path);
            }
        }

        // Load favicon
        if (! empty($meta['favicon_media_id'])) {
            $media = Media::find($meta['favicon_media_id']);
            if ($media) {
                $this->currentFavicon = $media;
                $this->faviconPreview = asset('storage/'.$media->file_path);
            }
        }
    }

    public function uploadLogo()
    {
        $this->validate([
            'logoFile' => 'required|image|max:2048|dimensions:min_width=200,min_height=50,max_width=800,max_height=200',
        ], [
            'logoFile.required' => 'Selecciona un archivo de logo',
            'logoFile.image' => 'El archivo debe ser una imagen (JPG, PNG, WebP, SVG)',
            'logoFile.max' => 'El logo no debe exceder 2MB',
            'logoFile.dimensions' => 'Dimensiones recomendadas: 200-800px ancho, 50-200px alto',
        ]);

        try {
            $path = $this->logoFile->store('cms/logo', 'public');

            // Optimize
            $optimizationService = app(ImageOptimizationService::class);
            $optimized = $optimizationService->optimizeAndConvert($path);
            $webpPath = $optimized['webp'];

            // Delete old logo if exists
            if ($this->currentLogo) {
                $this->deleteMediaAsset($this->currentLogo);
            }

            $media = Media::create([
                'name' => 'Logo - '.$this->logoFile->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => 'image',
                'mime_type' => $this->logoFile->getMimeType(),
                'file_size' => $this->logoFile->getSize(),
                'alt_text' => 'Logo de la empresa',
                'meta' => [
                    'webp_path' => $webpPath,
                    'responsive_paths' => $optimized['responsive'],
                ],
            ]);

            $meta = $this->page->meta ?? [];
            $meta['logo_media_id'] = $media->id;
            $this->page->update(['meta' => $meta]);

            $this->currentLogo = $media;
            $this->logoPreview = $media->webpUrl() ?? asset('storage/'.$media->file_path);

            $this->invalidateLandingCache();
            $this->reset('logoFile');
            $this->successMessage = 'Logo actualizado correctamente';
            session()->flash('success', 'Logo actualizado');
        } catch (\Throwable $e) {
            $this->errorMessage = 'Error al subir el logo: '.$e->getMessage();
        }
    }

    public function uploadFavicon()
    {
        $this->validate([
            'faviconFile' => 'required|image|max:512|dimensions:width=32,height=32',
        ], [
            'faviconFile.required' => 'Selecciona un archivo de favicon',
            'faviconFile.image' => 'El archivo debe ser una imagen (PNG, ICO, WebP)',
            'faviconFile.max' => 'El favicon no debe exceder 512KB',
            'faviconFile.dimensions' => 'El favicon debe ser exactamente 32x32 píxeles',
        ]);

        try {
            $path = $this->faviconFile->store('cms/favicon', 'public');

            // Delete old favicon if exists
            if ($this->currentFavicon) {
                $this->deleteMediaAsset($this->currentFavicon);
            }

            $media = Media::create([
                'name' => 'Favicon - '.$this->faviconFile->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => 'image',
                'mime_type' => $this->faviconFile->getMimeType(),
                'file_size' => $this->faviconFile->getSize(),
                'alt_text' => 'Favicon del sitio',
            ]);

            $meta = $this->page->meta ?? [];
            $meta['favicon_media_id'] = $media->id;
            $this->page->update(['meta' => $meta]);

            $this->currentFavicon = $media;
            $this->faviconPreview = asset('storage/'.$media->file_path);

            $this->invalidateLandingCache();
            $this->reset('faviconFile');
            $this->successMessage = 'Favicon actualizado correctamente';
            session()->flash('success', 'Favicon actualizado');
        } catch (\Throwable $e) {
            $this->errorMessage = 'Error al subir el favicon: '.$e->getMessage();
        }
    }

    public function removeLogo()
    {
        if ($this->currentLogo) {
            $this->deleteMediaAsset($this->currentLogo);

            $meta = $this->page->meta ?? [];
            unset($meta['logo_media_id']);
            $this->page->update(['meta' => $meta]);

            $this->currentLogo = null;
            $this->logoPreview = '';

            $this->invalidateLandingCache();
            $this->successMessage = 'Logo eliminado';
            session()->flash('success', 'Logo eliminado');
        }
    }

    public function removeFavicon()
    {
        if ($this->currentFavicon) {
            $this->deleteMediaAsset($this->currentFavicon);

            $meta = $this->page->meta ?? [];
            unset($meta['favicon_media_id']);
            $this->page->update(['meta' => $meta]);

            $this->currentFavicon = null;
            $this->faviconPreview = '';

            $this->invalidateLandingCache();
            $this->successMessage = 'Favicon eliminado';
            session()->flash('success', 'Favicon eliminado');
        }
    }

    protected function deleteMediaAsset(Media $media)
    {
        // Delete optimized versions
        $optimizationService = app(ImageOptimizationService::class);
        try {
            $optimizationService->deleteOptimizedVersions($media->file_path);
        } catch (\Throwable) {
            // Ignore errors
        }

        // Delete PageMedia relations
        PageMedia::where('media_id', $media->id)->delete();

        // Delete file from storage
        \Storage::disk('public')->delete($media->file_path);

        // Delete media record
        $media->delete();
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
        return view('livewire.cms.gestionar-logo');
    }
}
