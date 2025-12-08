<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlaylistController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return view('playlists', [
                'playlists' => collect([]),
                'initialPlaylist' => null,
            ]);
        }
        
        // Get all playlists for the current user
        $playlists = Playlist::where('user_id', $user->id)
            ->with(['tracks' => function ($query) {
                $query->with(['artist', 'album'])
                    ->orderBy('playlist_tracks.created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($playlist) {
                $tracks = $playlist->tracks->map(function ($track) {
                    $coverPath = $track->cover_path 
                        ?? $track->album?->cover_path 
                        ?? $track->artist?->photo_path;
                    $coverUrl = $coverPath 
                        ? Storage::disk('public')->url($coverPath) 
                        : 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80';
                    
                    $audioUrl = $track->audio_path 
                        ? Storage::disk('public')->url($track->audio_path) 
                        : null;

                    return [
                        'id' => $track->id,
                        'title' => $track->title,
                        'artist' => $track->artist?->name ?? 'Unknown',
                        'cover' => $coverUrl,
                        'length' => $track->formatted_duration ?? '0:00',
                        'audio' => $audioUrl,
                    ];
                });

                // Get cover from the last added track
                $coverUrl = $playlist->cover 
                    ?? 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80';

                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'description' => $playlist->description,
                    'cover' => $coverUrl,
                    'tracks' => $tracks->toArray(),
                    'tracks_count' => $tracks->count(),
                ];
            });

        $initialPlaylist = $playlists->first();

        return view('playlists', [
            'playlists' => $playlists,
            'initialPlaylist' => $initialPlaylist,
        ]);
    }
}
