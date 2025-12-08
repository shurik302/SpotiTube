<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Library\AlbumController;
use App\Http\Controllers\Admin\Library\ArtistController;
use App\Http\Controllers\Admin\Library\TrackController;
use App\Http\Controllers\DashboardController;
use App\Models\Playlist;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('home');

Route::get('/browse', \App\Http\Controllers\BrowseController::class)->name('browse');

Route::get('/playlists', \App\Http\Controllers\PlaylistController::class)
    ->middleware('auth')
    ->name('playlists');

Route::view('/live-radio', 'live-radio')->name('live-radio');

Route::get('/admin', \App\Http\Controllers\Admin\AdminController::class)->name('admin');

Route::view('/settings', 'settings')->name('settings');

// Playlist API for web session (no /api prefix, keeps CSRF + session auth)
Route::middleware('auth')->prefix('playlists/session')->group(function () {
    // List playlists
    Route::get('/', function () {
        $user = auth()->user();
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

        return response()->json([
            'playlists' => $playlists,
            'initialPlaylist' => $playlists->first(),
        ]);
    });

    // Create playlist
    Route::post('/', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
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
    Route::delete('/{playlist}', function (Playlist $playlist) {
        $user = auth()->user();
        if ($playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $playlist->delete();
        return response()->json(['success' => true]);
    });

    // Add track to playlist
    Route::post('/{playlist}/tracks', function (Request $request, Playlist $playlist) {
        $user = auth()->user();
        if ($playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'track_id' => 'required|exists:tracks,id',
        ]);

        if ($playlist->tracks()->where('track_id', $request->track_id)->exists()) {
            return response()->json(['error' => 'Track already in playlist'], 422);
        }

        $maxPosition = $playlist->tracks()->max('position') ?? 0;
        $playlist->tracks()->attach($request->track_id, [
            'position' => $maxPosition + 1,
        ]);

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
    Route::delete('/{playlist}/tracks/{track}', function (Playlist $playlist, Track $track) {
        $user = auth()->user();
        if ($playlist->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $playlist->tracks()->detach($track->id);

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
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::prefix('auth/google')->name('auth.google.')->group(function () {
        Route::get('redirect', [GoogleController::class, 'redirect'])->name('redirect');
        Route::get('callback', [GoogleController::class, 'callback'])->name('callback');
    });
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('admin/library')->name('admin.library.')->group(function () {
    Route::post('artists', [ArtistController::class, 'store'])->name('artists.store');
    Route::delete('artists/{artist}', [ArtistController::class, 'destroy'])->name('artists.destroy');
    Route::post('albums', [AlbumController::class, 'store'])->name('albums.store');
    Route::delete('albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');
    Route::post('tracks', [TrackController::class, 'store'])->name('tracks.store');
    Route::delete('tracks/{track}', [TrackController::class, 'destroy'])->name('tracks.destroy');
});
