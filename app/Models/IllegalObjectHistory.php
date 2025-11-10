<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class IllegalObjectHistory extends Model
{
    protected $guarded = false;
    protected $casts = [
        'content' => 'array'
    ];

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

}
