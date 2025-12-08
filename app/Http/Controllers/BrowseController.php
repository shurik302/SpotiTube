<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

final class BrowseController extends Controller
{
    public function __invoke(): View
    {
        $tracks = Track::query()
            ->with(['artist', 'album'])
            ->where('is_published', true)
            ->orderBy('title')
            ->get();

        $artists = Artist::query()
            ->orderByDesc('rating')
            ->orderBy('name')
            ->get();

        $albums = Album::query()
            ->with('artist')
            ->orderByDesc('year')
            ->orderBy('title')
            ->get();

        $genres = Track::query()
            ->select('genre', DB::raw('count(*) as count'))
            ->whereNotNull('genre')
            ->where('is_published', true)
            ->groupBy('genre')
            ->orderBy('genre')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->genre,
                'tracks' => (int) $item->count,
            ]);

        return view('browse', [
            'browseTracks' => $tracks,
            'browseArtists' => $artists,
            'browseAlbums' => $albums,
            'browseGenres' => $genres,
        ]);
    }
}

