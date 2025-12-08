<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Services\Media\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrackController extends Controller
{
    public function store(Request $request, MediaUploader $uploader): RedirectResponse
    {
        $validated = $request->validate([
            'track_title' => ['required', 'string', 'max:255'],
            'track_artist_id' => ['required', 'exists:artists,id'],
            'track_album_id' => ['nullable', 'exists:albums,id'],
            'track_genre' => ['nullable', 'string', 'max:120'],
            'track_duration' => ['nullable', 'integer', 'min:1', 'max:2000'],
            'track_bpm' => ['nullable', 'integer', 'min:40', 'max:300'],
            'track_number' => ['nullable', 'integer', 'min:1', 'max:40'],
            'track_audio' => ['nullable', 'mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/flac', 'max:51200'],
            'track_cover' => ['nullable', 'image', 'max:5120'],
        ]);

        $track = Track::create([
            'artist_id' => $validated['track_artist_id'],
            'album_id' => $validated['track_album_id'] ?? null,
            'title' => $validated['track_title'],
            'genre' => $validated['track_genre'] ?? null,
            'duration' => $validated['track_duration'] ?? null,
            'bpm' => $validated['track_bpm'] ?? null,
            'track_number' => $validated['track_number'] ?? null,
        ]);

        if ($request->hasFile('track_audio')) {
            $asset = $uploader->store($request->file('track_audio'), 'track_audio', $track);
            $track->forceFill(['audio_path' => $asset->path])->save();
        }

        if ($request->hasFile('track_cover')) {
            $asset = $uploader->store($request->file('track_cover'), 'track_cover', $track);
            $track->forceFill(['cover_path' => $asset->path])->save();
        }

        return redirect()->back()->with('status', 'Track created');
    }

    public function destroy(Track $track): RedirectResponse
    {
        foreach (['audio_path', 'cover_path'] as $column) {
            if ($track->{$column}) {
                Storage::disk('public')->delete($track->{$column});
            }
        }

        $track->delete();

        return redirect()->back()->with('status', 'Track removed');
    }
}
