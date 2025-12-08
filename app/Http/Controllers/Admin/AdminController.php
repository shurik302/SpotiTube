<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Illuminate\Contracts\View\View;

class AdminController extends Controller
{
    public function __invoke(): View
    {
        $artists = Artist::query()->latest()->get();
        $albums = Album::query()->with('artist')->latest()->get();
        $tracks = Track::query()->with(['artist', 'album'])->latest()->get();

        return view('admin', [
            'artists' => $artists,
            'albums' => $albums,
            'tracks' => $tracks,
        ]);
    }
}
