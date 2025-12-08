<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Services\Media\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{
    public function store(Request $request, MediaUploader $uploader): RedirectResponse
    {
        $validated = $request->validate([
            'artist_name' => ['required', 'string', 'max:255'],
            'artist_genre' => ['nullable', 'string', 'max:120'],
            'artist_bio' => ['nullable', 'string'],
            'artist_photo' => ['nullable', 'image', 'max:5120'],
            'social_links' => ['nullable', 'array'],
        ]);

        $artist = Artist::create([
            'name' => $validated['artist_name'],
            'genre' => $validated['artist_genre'] ?? null,
            'bio' => $validated['artist_bio'] ?? null,
            'social_links' => $validated['social_links'] ?? null,
        ]);

        if ($request->hasFile('artist_photo')) {
            $asset = $uploader->store($request->file('artist_photo'), 'artist_photo', $artist);
            $artist->forceFill(['photo_path' => $asset->path])->save();
        }

        return redirect()->back()->with('status', 'Artist created');
    }

    public function destroy(Artist $artist): RedirectResponse
    {
        if ($artist->photo_path) {
            Storage::disk('public')->delete($artist->photo_path);
        }

        $artist->delete();

        return redirect()->back()->with('status', 'Artist removed');
    }
}
