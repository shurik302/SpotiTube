<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $recentTracks = Track::query()
            ->with(['artist', 'album'])
            ->whereNotNull('audio_path')
            ->latest('updated_at')
            ->take(4)
            ->get();

        $recommendedTracks = Track::query()
            ->with(['artist', 'album'])
            ->whereNotNull('audio_path')
            ->latest('updated_at')
            ->take(6)
            ->get();

        $topArtists = Artist::query()
            ->orderByDesc('rating')
            ->orderByDesc('updated_at')
            ->take(6)
            ->get();

        $topAlbums = Album::query()
            ->with('artist')
            ->orderByDesc('year')
            ->orderByDesc('updated_at')
            ->take(4)
            ->get();

        return view('welcome', [
            'recentTracks' => $recentTracks,
            'recommendedTracks' => $recommendedTracks,
            'topArtists' => $topArtists,
            'topAlbums' => $topAlbums,
        ]);
    }
}
