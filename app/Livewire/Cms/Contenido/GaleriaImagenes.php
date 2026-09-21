<?php

namespace App\Livewire\Cms\Contenido;

use App\Models\Media;
use App\Models\PageMedia;
use Livewire\Component;
use Livewire\WithFileUploads;

class GaleriaImagenes extends Component
{
    use WithFileUploads;

    public int $sectionId;
    public int $maxImages = 0;
    public array $mediaItems = [];
    public $uploadFile;
    public string $uploadUsage = 'image';

    public function mount(int $sectionId, int $maxImages, array $mediaItems): void
    {
        $this->sectionId = $sectionId;
        $this->maxImages = $maxImages;
        $this->mediaItems = $mediaItems;
    }

    public function getCanUploadProperty(): bool
    {
        return $this->maxImages > 0 && count($this->mediaItems) < $this->maxImages;
    }

    public function getHasImagesProperty(): bool
    {
        return $this->maxImages > 0;
    }

    public function uploadMedia(): void
    {
        if (!$this->canUpload) {
            $this->dispatch('minAlert', titulo: 'Límite alcanzado', mensaje: 'No se pueden subir más imágenes para esta sección.', icono: 'warning');
            return;
        }

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

            PageMedia::create([
                'page_section_id' => $this->sectionId,
                'media_id' => $media->id,
                'usage' => $this->uploadUsage,
                'sort_order' => 0,
            ]);

            $this->reset('uploadFile');
            $this->dispatch('minToast', titulo: '¡Subida!', mensaje: 'Imagen subida correctamente.', icono: 'success');
            $this->dispatch('galeria-actualizada', sectionId: $this->sectionId);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo subir la imagen.', icono: 'error');
        }
    }

    public function removeMedia(int $pageMediaId): void
    {
        try {
            $pm = PageMedia::findOrFail($pageMediaId);
            $media = $pm->media;

            if ($media && $media->file_path) {
                \Storage::disk('public')->delete($media->file_path);
                try {
                    $optimizationService = app(\App\Services\ImageOptimizationService::class);
                    $optimizationService->deleteOptimizedVersions($media->file_path);
                } catch (\Throwable $optError) {
                    \Log::warning('removeMedia: no se pudieron borrar versiones optimizadas', ['error' => $optError->getMessage()]);
                }
            }

            $media->delete();
            $pm->delete();

            $this->dispatch('minToast', titulo: '¡Eliminada!', mensaje: 'Imagen eliminada correctamente.', icono: 'success');
            $this->dispatch('galeria-actualizada', sectionId: $this->sectionId);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('minAlert', titulo: 'Error', mensaje: 'No se pudo eliminar la imagen.', icono: 'error');
        }
    }

    public function render()
    {
        return view('livewire.cms.contenido.galeria-imagenes');
    }
}
