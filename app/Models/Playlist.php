<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany(Track::class, 'playlist_tracks')
            ->withPivot('position')
            ->withTimestamps()
            ->orderBy('playlist_tracks.position');
    }

    public function getCoverAttribute(): ?string
    {
        // Get the last added track (latest added to playlist)
        $lastTrack = $this->tracks()
            ->orderBy('playlist_tracks.created_at', 'desc')
            ->orderBy('playlist_tracks.id', 'desc')
            ->first();

        if (!$lastTrack) {
            return null;
        }

        // Get cover from track, album, or artist
        $coverPath = $lastTrack->cover_path 
            ?? $lastTrack->album?->cover_path 
            ?? $lastTrack->artist?->photo_path;

        if ($coverPath) {
            return Storage::disk('public')->url($coverPath);
        }

        return null;
    }

    public function getTracksCountAttribute(): int
    {
        return $this->tracks()->count();
    }
}
