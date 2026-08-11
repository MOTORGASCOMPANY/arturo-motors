<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'alt_text',
        'caption',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'file_size' => 'integer',
    ];

    public function url()
    {
        return asset('storage/' . $this->file_path);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('file_type', $type);
    }
}
