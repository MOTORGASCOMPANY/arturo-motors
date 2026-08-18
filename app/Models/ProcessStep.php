<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    protected $table = 'process_steps';

    protected $fillable = [
        'title',
        'description',
        'step_number',
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
