<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait InvalidatesLandingCache
{
    /**
     * Invalidate the landing page cache.
     * Call this after any CMS data modification.
     */
    protected function invalidateLandingCache(): void
    {
        Cache::tags(['landing', 'cms'])->flush();
    }
}
