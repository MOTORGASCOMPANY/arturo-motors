<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteService extends Model
{
    protected $table = 'site_services';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'features',
        'cta_text',
        'cta_link',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }
}
