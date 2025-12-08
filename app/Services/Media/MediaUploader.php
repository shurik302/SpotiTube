<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaUploader
{
    public function store(UploadedFile $file, string $role, object $owner, string $disk = 'public'): MediaAsset
    {
        $path = $file->store($this->directoryFor($role), ['disk' => $disk]);

        return MediaAsset::create([
            'owner_type' => $owner::class,
            'owner_id' => $owner->getKey(),
            'role' => $role,
            'disk' => $disk,
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'meta' => [
                'original_name' => $file->getClientOriginalName(),
            ],
        ]);
    }

    public function url(MediaAsset $asset): string
    {
        return Storage::disk($asset->disk)->url($asset->path);
    }

    protected function directoryFor(string $role): string
    {
        return match ($role) {
            'artist_photo' => 'artists/photos',
            'album_cover' => 'albums/covers',
            'track_audio' => 'tracks/audio',
            'track_cover' => 'tracks/covers',
            default => 'media',
        };
    }
}
