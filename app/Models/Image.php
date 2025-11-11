<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $guarded = [];

    protected $appends = ['full_url'];

    public function getFullUrlAttribute()
    {
        return Storage::disk('public')->url($this->url);
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
