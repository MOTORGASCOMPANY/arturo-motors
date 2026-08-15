<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyCard extends Model
{
    protected $table = 'why_cards';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }
}
