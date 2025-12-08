{{-- resources/views/welcome.blade.php --}}
@php
    use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.app')

@section('title', config('app.name', 'SpotiTube'))
@section('sidebar_active', 'dashboard')

@push('page-styles')
<style>
  .top-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 1rem;
  }

  .top-card {
    border-radius: 1.1rem;
    border: 1px solid rgba(255, 255, 255, 0.06);
    background: rgba(5, 8, 18, 0.8);
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    min-height: 260px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .top-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .album-card {
    min-height: 280px;
  }

  .top-card-thumb {
    margin: 0;
    border-radius: 1rem;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    background: #050914;
  }

  .album-card .top-card-thumb {
    aspect-ratio: 4 / 3;
  }

  .top-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .top-card small {
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-size: 0.74rem;
    color: rgba(255, 255, 255, 0.6);
  }

  .rating {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.75);
  }

  .rating-stars {
    letter-spacing: 0.2rem;
    font-size: 0.9rem;
  }

  .rating-value {
    letter-spacing: 0;
    font-weight: 600;
  }

  body.light .top-card {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(4, 9, 28, 0.08);
    color: #0d142b;
  }

  body.light .top-card small,
  body.light .rating {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .rating-stars {
    color: rgba(16, 23, 42, 0.7);
  }

  body.light .rating-value {
    color: rgba(16, 23, 42, 0.9);
  }

  .media-card[data-track-play],
  .recommended-item[data-track-play] {
    cursor: pointer;
    position: relative;
  }

  .media-card[data-track-play]::after,
  .recommended-item[data-track-play]::after {
    content: '▶';
    position: absolute;
    top: 0.8rem;
    right: 0.9rem;
    font-size: 0.7rem;
    opacity: 0.7;
  }

  .recommended-item[data-track-play]::after {
    top: 50%;
    right: 0.5rem;
    transform: translateY(-50%);
  }
  .media-card {
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    min-height: 140px;
  }

  .media-card .media-thumb {
    width: 120px;
    height: 120px;
    margin: 0;
    flex-shrink: 0;
  }

  .media-card .media-meta {
    flex: 1;
  }

</style>
@endpush

@php
    $fallbackRecent = [
        [
            'title' => 'Electric Dreams',
            'artist' => 'The Electric Dreams',
            'genre' => 'Electronic',
            'image' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=600&q=80',
            'alt' => 'DJ mixing console with moody lighting',
        ],
        [
            'title' => 'Midnight Jazz',
            'artist' => 'Jazz Collective',
            'genre' => 'Jazz',
            'image' => 'https://images.unsplash.com/photo-1497032628192-86f99bcd76bc?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Jazz trio in a dimly lit studio',
        ],
        [
            'title' => 'Thunder Strike',
            'artist' => 'Rock Legends',
            'genre' => 'Rock',
            'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Retro boombox portrait session',
        ],
        [
            'title' => 'Classical Movement',
            'artist' => 'Classical Ensemble',
            'genre' => 'Classical',
            'image' => 'https://images.unsplash.com/photo-1487215078519-e21cc028cb29?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Portrait of a smiling artist',
        ],
    ];

    $fallbackRecommended = [
        [
            'title' => 'Electric Dreams',
            'artist' => 'The Electric Dreams',
            'genre' => 'Electronic',
            'image' => 'https://images.unsplash.com/photo-1485579149621-3123dd979885?auto=format&fit=crop&w=200&q=80',
            'alt' => 'Vinyl turntable close-up',
        ],
        [
            'title' => 'Midnight Jazz',
            'artist' => 'Jazz Collective',
            'genre' => 'Jazz',
            'image' => 'https://images.unsplash.com/photo-1483412033650-1015ddeb83d1?auto=format&fit=crop&w=200&q=80',
            'alt' => 'Jazz musician performing live',
        ],
        [
            'title' => 'Thunder Strike',
            'artist' => 'Rock Legends',
            'genre' => 'Rock',
            'image' => 'https://images.unsplash.com/photo-1464375117522-1311d6a5b81f?auto=format&fit=crop&w=200&q=80',
            'alt' => 'Rock guitarist on stage',
        ],
        [
            'title' => 'Classical Movement',
            'artist' => 'Classical Ensemble',
            'genre' => 'Classical',
            'image' => 'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=200&q=80',
            'alt' => 'Classical musician portrait',
        ],
    ];

    $fallbackTopArtists = [
        [
            'name' => 'Classical Ensemble',
            'genre' => 'Classical',
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1487215078519-e21cc028cb29?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Sheet music on a piano',
        ],
        [
            'name' => 'Jazz Collective',
            'genre' => 'Jazz',
            'rating' => 4.8,
            'image' => 'https://images.unsplash.com/photo-1497032628192-86f99bcd76bc?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Jazz musicians performing live',
        ],
        [
            'name' => 'Rock Legends',
            'genre' => 'Rock',
            'rating' => 4.7,
            'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Rock guitarist on stage',
        ],
        [
            'name' => 'Hip Hop Masters',
            'genre' => 'Hip Hop',
            'rating' => 4.6,
            'image' => 'https://images.unsplash.com/photo-1485579149621-3123dd979885?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Cassette tape on dark background',
        ],
        [
            'name' => 'The Electric Dreams',
            'genre' => 'Electronic',
            'rating' => 4.5,
            'image' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=600&q=80',
            'alt' => 'DJ console in neon light',
        ],
        [
            'name' => 'Indie Wave',
            'genre' => 'Indie',
            'rating' => 4.4,
            'image' => 'https://images.unsplash.com/photo-1497032205916-ac775f0649ae?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Crowd at an indie concert',
        ],
    ];

    $fallbackTopAlbums = [
        [
            'title' => 'Symphony No. 5',
            'artist' => 'Classical Ensemble',
            'rating' => 4.9,
            'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Composer portrait in monochrome',
        ],
        [
            'title' => 'Thunder Road',
            'artist' => 'Rock Legends',
            'rating' => 4.8,
            'image' => 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Boombox with sneakers',
        ],
        [
            'title' => 'Smooth Sessions',
            'artist' => 'Jazz Collective',
            'rating' => 4.7,
            'image' => 'https://images.unsplash.com/photo-1497032628192-86f99bcd76bc?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Jazz studio with instruments',
        ],
        [
            'title' => 'Street Chronicles',
            'artist' => 'Hip Hop Masters',
            'rating' => 4.6,
            'image' => 'https://images.unsplash.com/photo-1485579149621-3123dd979885?auto=format&fit=crop&w=600&q=80',
            'alt' => 'Urban gym interior',
        ],
    ];
@endphp

@section('content')
<header class="page-header">
  <div>
    <p class="eyebrow" data-i18n="dashboard_label">Dashboard</p>
    <h1 data-i18n="welcome_title">Jump back into your mixes</h1>
    <p class="section-intro" data-i18n="welcome_text">
      Keep an eye on your latest radio sessions, playlists, and artist drops without losing context while you explore.
    </p>
  </div>
  @guest
  <a class="outline-btn" href="{{ route('login') }}">Sign in</a>
  @endguest
</header>

<section aria-labelledby="recently-played-title" class="content-section">
  <div class="section-header">
    <div>
      <p class="eyebrow" data-i18n="queue_snapshot">Queue snapshot</p>
      <h2 id="recently-played-title" data-i18n="recently_played">Recently played</h2>
    </div>
    <a class="ghost-btn" href="{{ route('browse') }}" data-i18n="view_all">View all</a>
  </div>
  <div class="card-grid">
    @forelse ($recentTracks as $track)
    @php
        $coverPath = $track->cover_path ?? $track->album?->cover_path ?? $track->artist?->photo_path;
        $coverUrl = $coverPath ? Storage::disk('public')->url($coverPath) : 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=600&q=80';
        $audioUrl = $track->audio_path ? Storage::disk('public')->url($track->audio_path) : null;
        $artistName = $track->artist?->name ?? 'Unknown artist';
        $trackDuration = $track->formatted_duration ?? '0:00';
    @endphp
    <article class="media-card"
      @if ($audioUrl)
        data-track-play
        data-track-title="{{ $track->title }}"
        data-track-artist="{{ $artistName }}"
        data-track-cover="{{ $coverUrl }}"
        data-track-audio="{{ $audioUrl }}"
        data-track-length="{{ $trackDuration }}"
      @endif>
      <figure class="media-thumb">
        <img src="{{ $coverUrl }}" alt="Cover art for {{ $track->title }}" loading="lazy">
      </figure>
      <div class="media-meta">
        <strong>{{ $track->title }}</strong>
        <span>{{ strtoupper($artistName) }}</span>
      </div>
      <span class="genre-tag">{{ $track->genre ?? 'Unknown' }}</span>
    </article>
    @empty
    @foreach ($fallbackRecent as $item)
    <article class="media-card">
      <figure class="media-thumb">
        <img src="{{ $item['image'] }}" alt="{{ $item['alt'] }}" loading="lazy">
      </figure>
      <div class="media-meta">
        <strong>{{ $item['title'] }}</strong>
        <span>{{ strtoupper($item['artist']) }}</span>
      </div>
      <span class="genre-tag">{{ $item['genre'] }}</span>
    </article>
    @endforeach
    @endforelse
  </div>
</section>

<section aria-labelledby="recommended-title" class="content-section">
  <div class="section-header">
    <div>
      <p class="eyebrow" data-i18n="stay_inspired">Stay inspired</p>
      <h2 id="recommended-title" data-i18n="recommended">Recommended</h2>
    </div>
  </div>
  <ul class="recommended-list">
    @forelse ($recommendedTracks as $track)
    @php
        $coverPath = $track->cover_path ?? $track->album?->cover_path ?? $track->artist?->photo_path;
        $coverUrl = $coverPath ? Storage::disk('public')->url($coverPath) : 'https://images.unsplash.com/photo-1485579149621-3123dd979885?auto=format&fit=crop&w=200&q=80';
        $audioUrl = $track->audio_path ? Storage::disk('public')->url($track->audio_path) : null;
        $artistName = $track->artist?->name ?? 'Unknown artist';
        $trackDuration = $track->formatted_duration ?? '0:00';
    @endphp
    <li class="recommended-item"
      @if ($audioUrl)
        data-track-play
        data-track-title="{{ $track->title }}"
        data-track-artist="{{ $artistName }}"
        data-track-cover="{{ $coverUrl }}"
        data-track-audio="{{ $audioUrl }}"
        data-track-length="{{ $trackDuration }}"
      @endif>
      <div class="recommended-left">
        <img class="list-thumb" src="{{ $coverUrl }}" alt="Cover art for {{ $track->title }}" loading="lazy">
        <div class="media-meta">
          <strong>{{ $track->title }}</strong>
          <span>{{ strtoupper($artistName) }}</span>
        </div>
      </div>
      <span class="genre-tag">{{ $track->genre ?? 'Unknown' }}</span>
    </li>
    @empty
    @foreach ($fallbackRecommended as $item)
    <li class="recommended-item">
      <div class="recommended-left">
        <img class="list-thumb" src="{{ $item['image'] }}" alt="{{ $item['alt'] }}" loading="lazy">
        <div class="media-meta">
          <strong>{{ $item['title'] }}</strong>
          <span>{{ strtoupper($item['artist']) }}</span>
        </div>
      </div>
      <span class="genre-tag">{{ $item['genre'] }}</span>
    </li>
    @endforeach
    @endforelse
  </ul>
</section>

<section aria-labelledby="top-rated-artists-title" class="content-section">
  <div class="section-header">
    <div>
      <p class="eyebrow" data-i18n="community_favorites">Community favorites</p>
      <h2 id="top-rated-artists-title" data-i18n="top_rated_artists">Top Rated Artists</h2>
    </div>
  </div>
  <div class="top-card-grid">
    @if ($topArtists->isNotEmpty())
      @foreach ($topArtists as $artist)
      @php
          $photoUrl = $artist->photo_path ? Storage::disk('public')->url($artist->photo_path) : 'https://images.unsplash.com/photo-1487215078519-e21cc028cb29?auto=format&fit=crop&w=600&q=80';
          $rating = $artist->rating ? number_format($artist->rating, 1) : '�';
      @endphp
      <article class="top-card" aria-label="Artist {{ $artist->name }}">
        <figure class="top-card-thumb">
          <img src="{{ $photoUrl }}" alt="Photo of {{ $artist->name }}" loading="lazy">
        </figure>
        <div class="media-meta">
          <strong>{{ $artist->name }}</strong>
          <span>{{ strtoupper($artist->genre ?? 'Unknown') }}</span>
        </div>
        <div class="rating" aria-label="Rating {{ $rating }} out of 5">
          <span class="rating-stars" aria-hidden="true">★★★★★</span>
          <span class="rating-value">{{ $rating }}</span>
        </div>
      </article>
      @endforeach
    @else
      @foreach ($fallbackTopArtists as $artist)
      <article class="top-card" aria-label="Artist {{ $artist['name'] }}">
        <figure class="top-card-thumb">
          <img src="{{ $artist['image'] }}" alt="{{ $artist['alt'] }}" loading="lazy">
        </figure>
        <div class="media-meta">
          <strong>{{ $artist['name'] }}</strong>
          <span>{{ strtoupper($artist['genre']) }}</span>
        </div>
        <div class="rating" aria-label="Rating {{ number_format($artist['rating'], 1) }} out of 5">
          <span class="rating-stars" aria-hidden="true">★★★★★</span>
          <span class="rating-value">{{ number_format($artist['rating'], 1) }}</span>
        </div>
      </article>
      @endforeach
    @endif
  </div>
</section>

<section aria-labelledby="top-albums-title" class="content-section">
  <div class="section-header">
    <div>
      <p class="eyebrow" data-i18n="listener_approved">Listener approved</p>
      <h2 id="top-albums-title" data-i18n="top_albums">Top Albums</h2>
    </div>
  </div>
  <div class="top-card-grid">
    @if ($topAlbums->isNotEmpty())
      @foreach ($topAlbums as $album)
      @php
          $coverUrl = $album->cover_path ? Storage::disk('public')->url($album->cover_path) : 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80';
          $artistName = $album->artist?->name ?? 'Unknown artist';
          $rating = $album->rating ? number_format($album->rating, 1) : '�';
      @endphp
      <article class="top-card album-card" aria-label="Album {{ $album->title }}">
        <figure class="top-card-thumb">
          <img src="{{ $coverUrl }}" alt="Cover for {{ $album->title }}" loading="lazy">
        </figure>
        <div class="media-meta">
          <strong>{{ $album->title }}</strong>
          <span>{{ strtoupper($artistName) }}</span>
        </div>
        <div class="rating" aria-label="Rating {{ $rating }} out of 5">
          <span class="rating-stars" aria-hidden="true">★★★★★</span>
          <span class="rating-value">{{ $rating }}</span>
        </div>
      </article>
      @endforeach
    @else
      @foreach ($fallbackTopAlbums as $album)
      <article class="top-card album-card" aria-label="Album {{ $album['title'] }}">
        <figure class="top-card-thumb">
          <img src="{{ $album['image'] }}" alt="{{ $album['alt'] }}" loading="lazy">
        </figure>
        <div class="media-meta">
          <strong>{{ $album['title'] }}</strong>
          <span>{{ strtoupper($album['artist']) }}</span>
        </div>
        <div class="rating" aria-label="Rating {{ number_format($album['rating'], 1) }} out of 5">
          <span class="rating-stars" aria-hidden="true">★★★★★</span>
          <span class="rating-value">{{ number_format($album['rating'], 1) }}</span>
        </div>
      </article>
      @endforeach
    @endif
  </div>
</section>
@endsection

