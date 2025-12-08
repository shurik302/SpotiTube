<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Artist;
use App\Services\Media\MediaUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function store(Request $request, MediaUploader $uploader): RedirectResponse
    {
        $validated = $request->validate([
            'album_artist_id' => ['required', 'exists:artists,id'],
            'album_title' => ['required', 'string', 'max:255'],
            'album_year' => ['nullable', 'integer', 'min:1900', 'max:' . now()->year + 1],
            'album_label' => ['nullable', 'string', 'max:120'],
            'album_cover' => ['nullable', 'image', 'max:6144'],
        ]);

        $album = Album::create([
            'artist_id' => $validated['album_artist_id'],
            'title' => $validated['album_title'],
            'year' => $validated['album_year'] ?? null,
            'label' => $validated['album_label'] ?? null,
        ]);

        if ($request->hasFile('album_cover')) {
            $asset = $uploader->store($request->file('album_cover'), 'album_cover', $album);
            $album->forceFill(['cover_path' => $asset->path])->save();
        }

        return redirect()->back()->with('status', 'Album created');
    }

    public function destroy(Album $album): RedirectResponse
    {
        if ($album->cover_path) {
            Storage::disk('public')->delete($album->cover_path);
        }

        $album->delete();

        return redirect()->back()->with('status', 'Album removed');
    }
}
