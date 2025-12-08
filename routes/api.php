<?php

use App\Infrastructure\Http\Controllers\HealthCheckController;
use App\Models\Playlist;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/healthz', HealthCheckController::class);

Route::get('/tracks/recommendations', function () {
    $limit = (int) request()->query('limit', 10);
    
    $tracks = Track::query()
        ->with(['artist', 'album'])
        ->where('is_published', true)
        ->whereNotNull('audio_path')
        ->inRandomOrder()
        ->limit($limit)
        ->get();
    
    return response()->json($tracks->map(function ($track) {
        $coverPath = $track->cover_path ?? $track->album?->cover_path ?? $track->artist?->photo_path;
        $coverUrl = $coverPath ? Storage::disk('public')->url($coverPath) : '';
        
        return [
            'title' => $track->title,
            'artist' => $track->artist?->name ?? 'Unknown',
            'cover' => $coverUrl,
            'audio' => $track->audio_path ? Storage::disk('public')->url($track->audio_path) : '',
            'length' => $track->formatted_duration ?? '0:00',
        ];
    }));
});

// Get all tracks for library
Route::get('/tracks/library', function () {
    $tracks = Track::query()
        ->with(['artist', 'album'])
        ->where('is_published', true)
        ->orderBy('title')
        ->get();
    
    return response()->json($tracks->map(function ($track) {
        $coverPath = $track->cover_path ?? $track->album?->cover_path ?? $track->artist?->photo_path;
        $coverUrl = $coverPath 
            ? Storage::disk('public')->url($coverPath) 
            : 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80';
        
        $audioUrl = $track->audio_path ? Storage::disk('public')->url($track->audio_path) : null;
        
        return [
            'id' => $track->id,
            'title' => $track->title,
            'artist' => $track->artist?->name ?? 'Unknown',
            'cover' => $coverUrl,
            'audio' => $audioUrl,
            'length' => $track->formatted_duration ?? '0:00',
        ];
    }));
});

// Playlist API routes (session auth; include web to allow session cookie)
Route::middleware(['web', 'auth'])->group(function () {
    // Get all playlists for current user
    Route::get('/playlists', function () {
        $user = Auth::user();
        
        $playlists = Playlist::where('user_id', $user->id)
            ->with(['tracks' => function ($query) {
                $query->with(['artist', 'album'])
                    ->orderBy('playlist_tracks.position');
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

                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'cover' => $playlist->cover ?? 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
                    'tracks' => $tracks->toArray(),
                    'tracks_count' => $tracks->count(),
                ];
            });

        return response()->json($playlists);
    });

    // Create new playlist
    Route::post('/playlists', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        
        $playlist = Playlist::create([
            'user_id' => $user->id,
            'name' => $request->name,
        ]);

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'cover' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
            'tracks' => [],
            'tracks_count' => 0,
        ], 201);
    });

    // Delete playlist
    Route::delete('/playlists/{playlist}', function (Playlist $playlist) {
        $user = Auth::user();
        
        if ($playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $playlist->delete();
        
        return response()->json(['success' => true]);
    });

    // Add track to playlist
    Route::post('/playlists/{playlist}/tracks', function (Request $request, Playlist $playlist) {
        $request->validate([
            'track_id' => 'required|exists:tracks,id',
        ]);

        $user = Auth::user();
        
        if ($playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if track already in playlist
        if ($playlist->tracks()->where('track_id', $request->track_id)->exists()) {
            return response()->json(['error' => 'Track already in playlist'], 422);
        }

        // Get max position
        $maxPosition = $playlist->tracks()->max('position') ?? 0;

        $playlist->tracks()->attach($request->track_id, [
            'position' => $maxPosition + 1,
        ]);

        // Return updated playlist with cover
        $playlist->load(['tracks' => function ($query) {
            $query->with(['artist', 'album'])->orderBy('playlist_tracks.position');
        }]);

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

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'cover' => $playlist->cover ?? 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
            'tracks' => $tracks->toArray(),
            'tracks_count' => $tracks->count(),
        ]);
    });

    // Remove track from playlist
    Route::delete('/playlists/{playlist}/tracks/{track}', function (Playlist $playlist, Track $track) {
        $user = Auth::user();
        
        if ($playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $playlist->tracks()->detach($track->id);

        // Return updated playlist
        $playlist->load(['tracks' => function ($query) {
            $query->with(['artist', 'album'])->orderBy('playlist_tracks.position');
        }]);

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

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'cover' => $playlist->cover ?? 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
            'tracks' => $tracks->toArray(),
            'tracks_count' => $tracks->count(),
        ]);
    });
});

// Playlists without auth (use session)
Route::middleware('web')->group(function () {
    // Get all playlists for current user (web session)
    Route::get('/playlists/session', function () {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }
        
        $playlists = Playlist::where('user_id', $user->id)
            ->with(['tracks' => function ($query) {
                $query->with(['artist', 'album'])
                    ->orderBy('playlist_tracks.position');
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

                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'cover' => $playlist->cover ?? 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
                    'tracks' => $tracks->toArray(),
                    'tracks_count' => $tracks->count(),
                ];
            });

        return response()->json($playlists);
    });

    // Create playlist (web session)
    Route::post('/playlists/session', function (Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $playlist = Playlist::create([
            'user_id' => $user->id,
            'name' => $request->name,
        ]);

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'cover' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
            'tracks' => [],
            'tracks_count' => 0,
        ], 201);
    });

    // Delete playlist (web session)
    Route::delete('/playlists/session/{playlist}', function (Playlist $playlist) {
        $user = Auth::user();
        if (!$user || $playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $playlist->delete();
        
        return response()->json(['success' => true]);
    });

    // Add track to playlist (web session)
    Route::post('/playlists/session/{playlist}/tracks', function (Request $request, Playlist $playlist) {
        $user = Auth::user();
        if (!$user || $playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'track_id' => 'required|exists:tracks,id',
        ]);

        // Check if track already in playlist
        if ($playlist->tracks()->where('track_id', $request->track_id)->exists()) {
            return response()->json(['error' => 'Track already in playlist'], 422);
        }

        // Get max position
        $maxPosition = $playlist->tracks()->max('position') ?? 0;

        $playlist->tracks()->attach($request->track_id, [
            'position' => $maxPosition + 1,
        ]);

        // Return updated playlist with cover
        $playlist->load(['tracks' => function ($query) {
            $query->with(['artist', 'album'])->orderBy('playlist_tracks.position');
        }]);

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

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'cover' => $playlist->cover ?? 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
            'tracks' => $tracks->toArray(),
            'tracks_count' => $tracks->count(),
        ]);
    });

    // Remove track from playlist (web session)
    Route::delete('/playlists/session/{playlist}/tracks/{track}', function (Playlist $playlist, Track $track) {
        $user = Auth::user();
        if (!$user || $playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $playlist->tracks()->detach($track->id);

        // Return updated playlist
        $playlist->load(['tracks' => function ($query) {
            $query->with(['artist', 'album'])->orderBy('playlist_tracks.position');
        }]);

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

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'cover' => $playlist->cover ?? 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80',
            'tracks' => $tracks->toArray(),
            'tracks_count' => $tracks->count(),
        ]);
    });
});
