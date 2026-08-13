<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageOptimizationService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /**
     * Optimize and convert image to WebP format.
     * Returns array with original and WebP paths.
     */
    public function optimizeAndConvert(string $path, string $disk = 'public'): array
    {
        $diskInstance = Storage::disk($disk);

        if (! $diskInstance->exists($path)) {
            throw new \InvalidArgumentException("Image not found: {$path}");
        }

        $image = $this->manager->read($diskInstance->path($path));

        // Generate WebP version
        $webpPath = $this->generateWebP($image, $path, $diskInstance);

        // Generate responsive sizes
        $responsivePaths = $this->generateResponsiveSizes($image, $path, $diskInstance);

        return [
            'original' => $path,
            'webp' => $webpPath,
            'responsive' => $responsivePaths,
        ];
    }

    /**
     * Generate WebP version of the image.
     */
    protected function generateWebP($image, string $originalPath, $disk): string
    {
        $pathInfo = pathinfo($originalPath);
        $webpPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'.webp';

        // Convert to WebP with 85% quality
        $image->toWebP(85)->save($disk->path($webpPath));

        return $webpPath;
    }

    /**
     * Generate responsive image sizes.
     */
    protected function generateResponsiveSizes($image, string $originalPath, $disk): array
    {
        $pathInfo = pathinfo($originalPath);
        $baseName = $pathInfo['filename'];
        $dirname = $pathInfo['dirname'];

        $sizes = [
            'xs' => 480,
            'sm' => 768,
            'md' => 1024,
            'lg' => 1440,
            'xl' => 1920,
        ];

        $responsive = [];

        foreach ($sizes as $suffix => $width) {
            $resizedImage = $image->scaleDown(width: $width);
            $responsivePath = "{$dirname}/{$baseName}-{$suffix}.webp";
            $resizedImage->toWebP(80)->save($disk->path($responsivePath));
            $responsive[$suffix] = $responsivePath;
        }

        return $responsive;
    }

    /**
     * Generate srcset attribute value for responsive images.
     */
    public function generateSrcset(array $responsivePaths): string
    {
        $widths = [
            'xs' => 480,
            'sm' => 768,
            'md' => 1024,
            'lg' => 1440,
            'xl' => 1920,
        ];

        $srcset = [];
        foreach ($responsivePaths as $suffix => $path) {
            if (isset($widths[$suffix])) {
                $srcset[] = asset('storage/'.$path).' '.$widths[$suffix].'w';
            }
        }

        return implode(', ', $srcset);
    }

    /**
     * Generate sizes attribute for responsive images.
     */
    public function generateSizesAttribute(): string
    {
        return '(max-width: 480px) 480px, (max-width: 768px) 768px, (max-width: 1024px) 1024px, (max-width: 1440px) 1440px, 1920px';
    }

    /**
     * Delete all optimized versions of an image.
     */
    public function deleteOptimizedVersions(string $originalPath, string $disk = 'public'): void
    {
        $diskInstance = Storage::disk($disk);
        $pathInfo = pathinfo($originalPath);
        $baseName = $pathInfo['filename'];
        $dirname = $pathInfo['dirname'];

        // Delete WebP
        $webpPath = "{$dirname}/{$baseName}.webp";
        if ($diskInstance->exists($webpPath)) {
            $diskInstance->delete($webpPath);
        }

        // Delete responsive sizes
        $suffixes = ['xs', 'sm', 'md', 'lg', 'xl'];
        foreach ($suffixes as $suffix) {
            $responsivePath = "{$dirname}/{$baseName}-{$suffix}.webp";
            if ($diskInstance->exists($responsivePath)) {
                $diskInstance->delete($responsivePath);
            }
        }
    }
}
