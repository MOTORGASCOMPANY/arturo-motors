<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageMedia extends Model
{
    protected $table = 'page_media';

    protected $fillable = [
        'page_section_id',
        'media_id',
        'usage',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function scopeByUsage($query, $usage)
    {
        return $query->where('usage', $usage);
    }

    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }
}
