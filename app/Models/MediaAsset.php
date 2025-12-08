<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'role',
        'path',
        'disk',
        'mime',
        'size',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
