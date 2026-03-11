<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DisplayMedia extends Model
{
    protected $fillable = ['type', 'original_name', 'stored_path', 'sort_order'];

    /** Public URL for the stored file. */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->stored_path);
    }
}
