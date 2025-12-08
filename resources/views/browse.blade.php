{{-- resources/views/browse.blade.php --}}
@extends('layouts.app')

@section('title', 'Browse · ' . config('app.name', 'SpotiTube'))
@section('sidebar_active', 'browse')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@push('page-styles')
<style>
  .browse-shell {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }

  .browse-headline {
    font-size: 2rem;
    letter-spacing: 0.12em;
  }

  .browse-divider {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
  }

  .browse-search-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    justify-content: space-between;
    flex-wrap: wrap;
  }

  .browse-search-box {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.65rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
  }

  .browse-search-box input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    color: inherit;
    font: inherit;
  }

  .browse-filter {
    position: relative;
  }

  .genre-select-trigger {
    border: none;
    background: transparent;
    color: inherit;
    font-size: 0.95rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }

  .genre-select-menu {
    position: absolute;
    top: calc(100% + 0.4rem);
    right: 0;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 0.4rem;
    background: rgba(5, 8, 18, 0.95);
    min-width: 160px;
    list-style: none;
    padding: 0.2rem 0;
    margin: 0;
    display: none;
    z-index: 3;
  }

  .genre-select-menu.active {
    display: block;
  }

  .genre-select-menu button {
    width: 100%;
    border: none;
    background: transparent;
    color: inherit;
    text-align: left;
    padding: 0.4rem 0.9rem;
    font-size: 0.85rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    cursor: pointer;
  }

  .genre-select-menu button:hover,
  .genre-select-menu button[aria-selected="true"] {
    background: rgba(255, 255, 255, 0.1);
  }

  .browse-tabs {
    display: flex;
    gap: 1.8rem;
    font-size: 0.95rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
  }

  .browse-tabs button {
    border: none;
    background: transparent;
    color: inherit;
    padding-bottom: 0.45rem;
    letter-spacing: inherit;
    font: inherit;
    cursor: pointer;
    opacity: 0.55;
    border-bottom: 2px solid transparent;
  }

  .browse-tabs button.active {
    opacity: 1;
    border-color: #ffffff;
  }

  body.light .browse-tabs button.active {
    border-color: #0d142b;
  }

  .browse-panels {
    margin-top: 1rem;
  }

  .browse-panel {
    display: none;
    flex-direction: column;
    gap: 1.2rem;
  }

  .browse-panel.active {
    display: flex;
  }

  .browse-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.92rem;
  }

  .browse-table thead {
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-size: 0.78rem;
    color: rgba(244, 244, 245, 0.7);
  }

  .browse-table th,
  .browse-table td {
    padding: 0.85rem 0.6rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    vertical-align: middle;
  }

  .browse-table td:first-child,
  .browse-table th:first-child {
    width: 32px;
    text-align: center;
    padding: 0.85rem 0.3rem;
  }

  .browse-table td:nth-child(2) {
    width: 48px;
    padding: 0.5rem 0.6rem;
    text-align: center;
  }

  .browse-cover {
    width: 36px;
    height: 36px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.05);
    object-fit: cover;
    display: block;
    flex-shrink: 0;
  }

  .browse-play {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.35);
    background: transparent;
    color: inherit;
    font-size: 0.7rem;
    cursor: pointer;
  }

  .browse-play:hover {
    border-color: rgba(255, 255, 255, 0.6);
  }

  .browse-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.03);
  }

  .browse-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.4rem;
  }

  .browse-card {
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 1.2rem;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    background: rgba(5, 8, 18, 0.6);
  }

  .browse-card figure {
    margin: 0;
    border-radius: 1rem;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    background: #050914;
  }

  .browse-card figure img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .browse-card strong {
    font-size: 1.05rem;
  }

  .browse-card span {
    letter-spacing: 0.1em;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.82rem;
  }

  .browse-rating {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.9rem;
    letter-spacing: 0.2rem;
  }

  .browse-rating small {
    letter-spacing: 0;
    font-weight: 600;
  }

  .browse-card.yearly span:last-child {
    font-size: 0.78rem;
    letter-spacing: 0.08em;
  }

  .browse-genre-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .browse-genre-card {
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 1rem;
    padding: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    background: rgba(5, 8, 18, 0.45);
  }

  .browse-genre-card span {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
  }

  body.light .browse-headline {
    color: #0d142b;
  }

  body.light .browse-divider {
    border-top-color: rgba(4, 9, 28, 0.12);
  }

  body.light .browse-search-box {
    border-bottom-color: rgba(4, 9, 28, 0.12);
  }

  body.light .browse-search-box input {
    color: #0d142b;
  }

  body.light .browse-search-box input::placeholder {
    color: rgba(16, 23, 42, 0.5);
  }

  body.light .genre-select-menu {
    background: rgba(255, 255, 255, 0.98);
    border-color: rgba(4, 9, 28, 0.12);
  }

  body.light .genre-select-menu button:hover,
  body.light .genre-select-menu button[aria-selected="true"] {
    background: rgba(4, 9, 28, 0.08);
  }

  body.light .browse-tabs button {
    color: #0d142b;
  }

  body.light .browse-tabs button.active {
    border-color: #0d142b;
  }

  body.light .browse-table thead {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .browse-table th,
  body.light .browse-table td {
    color: #0d142b;
    border-bottom-color: rgba(4, 9, 28, 0.08);
  }

  body.light .browse-table tbody tr:hover {
    background: rgba(4, 9, 28, 0.04);
  }

  body.light .browse-cover {
    background: rgba(4, 9, 28, 0.08);
  }

  body.light .browse-play {
    border-color: rgba(4, 9, 28, 0.2);
    color: #0d142b;
  }

  body.light .browse-play:hover {
    border-color: rgba(4, 9, 28, 0.4);
    background: rgba(4, 9, 28, 0.06);
  }

  body.light .browse-card {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(4, 9, 28, 0.12);
    color: #0d142b;
  }

  body.light .browse-card figure {
    background: rgba(4, 9, 28, 0.05);
  }

  body.light .browse-card span {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .browse-rating {
    color: #0d142b;
  }

  body.light .browse-genre-card {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(4, 9, 28, 0.12);
    color: #0d142b;
  }

  body.light .browse-genre-card span {
    color: rgba(16, 23, 42, 0.65);
  }

  .browse-empty-message {
    text-align: center;
    padding: 2rem;
    color: rgba(255, 255, 255, 0.5);
  }

  body.light .browse-empty-message {
    color: rgba(16, 23, 42, 0.5);
  }
</style>
@endpush

@section('content')
<div class="browse-shell">
  <div>
    <p class="browse-headline" data-i18n="browse_title">BROWSE</p>
    <hr class="browse-divider">
  </div>

  <div class="browse-search-row">
    <label class="browse-search-box">
      <span aria-hidden="true">🔍</span>
      <input type="text" placeholder="Search" data-track-search autocomplete="off" data-i18n-placeholder="search">
    </label>
    <div class="browse-filter" data-genre-select>
      <button class="genre-select-trigger" type="button" data-genre-toggle>
        <span data-genre-label data-i18n="all_genres">All genres</span>
        <span aria-hidden="true">▾</span>
      </button>
      <ul class="genre-select-menu" data-genre-menu>
        <li><button type="button" data-genre-option="all" aria-selected="true" data-i18n="all_genres">All genres</button></li>
        @foreach ($browseGenres as $genre)
        <li><button type="button" data-genre-option="{{ strtolower($genre['name']) }}">{{ strtoupper($genre['name']) }}</button></li>
        @endforeach
      </ul>
    </div>
  </div>

  <nav class="browse-tabs" aria-label="Browse sections">
    <button class="active" type="button" data-panel="tracks" data-i18n="tab_tracks">Tracks</button>
    <button type="button" data-panel="artists" data-i18n="tab_artists">Artists</button>
    <button type="button" data-panel="albums" data-i18n="tab_albums">Albums</button>
    <button type="button" data-panel="genres" data-i18n="tab_genres">Genres</button>
  </nav>

  <div class="browse-panels">
    <section aria-label="Track listing" class="browse-panel active" data-panel-id="tracks">
      <table class="browse-table">
        <thead>
          <tr>
            <th data-i18n="th_number">#</th>
            <th></th>
            <th data-i18n="th_title">Title</th>
            <th data-i18n="th_artist">Artist</th>
            <th data-i18n="th_album">Album</th>
            <th data-i18n="th_genre">Genre</th>
            <th data-i18n="th_time">Time</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($browseTracks as $index => $track)
          @php
              $coverPath = $track->cover_path ?? $track->album?->cover_path ?? $track->artist?->photo_path;
              $coverUrl = $coverPath ? Storage::disk('public')->url($coverPath) : 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=300&q=80';
              $audioUrl = $track->audio_path ? Storage::disk('public')->url($track->audio_path) : null;
              $artistName = $track->artist?->name ?? 'Unknown artist';
              $albumName = $track->album?->title ?? '—';
              $trackDuration = $track->formatted_duration ?? '0:00';
              $genre = $track->genre ?? 'Unknown';
          @endphp
          <tr data-track-row data-title="{{ strtolower($track->title) }}" data-genre="{{ strtolower($genre) }}">
            <td>{{ $index + 1 }}</td>
            <td>
              <img src="{{ $coverUrl }}" alt="Cover for {{ $track->title }}" class="browse-cover" loading="lazy">
            </td>
            <td><strong>{{ $track->title }}</strong></td>
            <td>{{ $artistName }}</td>
            <td>{{ $albumName }}</td>
            <td>{{ $genre }}</td>
            <td>{{ $trackDuration }}</td>
            <td style="text-align: center;">
              @if ($audioUrl)
              <button class="browse-play" type="button" aria-label="Play {{ $track->title }}" data-track-play
                data-track-title="{{ $track->title }}" data-track-artist="{{ $artistName }}"
                data-track-cover="{{ $coverUrl }}" data-track-audio="{{ $audioUrl }}" data-track-length="{{ $trackDuration }}">
                &#9654;
              </button>
              @else
              <span style="opacity: 0.3; cursor: not-allowed; display: inline-block; width: 30px; text-align: center;">—</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="browse-empty-message">
              No tracks available yet.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </section>

    <section aria-label="Top artists" class="browse-panel" data-panel-id="artists">
      <div class="browse-card-grid">
        @forelse ($browseArtists as $artist)
        @php
            $photoUrl = $artist->photo_path ? Storage::disk('public')->url($artist->photo_path) : 'https://images.unsplash.com/photo-1487215078519-e21cc028cb29?auto=format&fit=crop&w=600&q=80';
            $rating = $artist->rating > 0 ? number_format($artist->rating / 20, 1) : '—';
        @endphp
        <article class="browse-card" data-artist-row data-title="{{ strtolower($artist->name) }}" data-genre="{{ strtolower($artist->genre ?? 'unknown') }}">
          <figure>
            <img src="{{ $photoUrl }}" alt="{{ $artist->name }}" loading="lazy">
          </figure>
          <div>
            <strong>{{ $artist->name }}</strong>
            <span>{{ strtoupper($artist->genre ?? 'Unknown') }}</span>
          </div>
          <div class="browse-rating" aria-label="Rating {{ $rating }} out of 5">
            <span aria-hidden="true">★★★★★</span>
            <small>{{ $rating }}</small>
          </div>
        </article>
        @empty
        <div class="browse-empty-message" style="grid-column: 1 / -1;">
          No artists available yet.
        </div>
        @endforelse
      </div>
    </section>

    <section aria-label="Featured albums" class="browse-panel" data-panel-id="albums">
      <div class="browse-card-grid">
        @forelse ($browseAlbums as $album)
        @php
            $coverUrl = $album->cover_path ? Storage::disk('public')->url($album->cover_path) : 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80';
            $artistName = $album->artist?->name ?? 'Unknown artist';
            $rating = $album->rating > 0 ? number_format($album->rating / 20, 1) : '—';
        @endphp
        <article class="browse-card yearly" data-album-row data-title="{{ strtolower($album->title) }}" data-artist="{{ strtolower($artistName) }}">
          <figure>
            <img src="{{ $coverUrl }}" alt="{{ $album->title }}" loading="lazy">
          </figure>
          <div>
            <strong>{{ $album->title }}</strong>
            <span>{{ strtoupper($artistName) }}</span>
            @if ($album->year)
            <span>{{ $album->year }}</span>
            @endif
          </div>
          <div class="browse-rating" aria-label="Rating {{ $rating }} out of 5">
            <span aria-hidden="true">★★★★★</span>
            <small>{{ $rating }}</small>
          </div>
        </article>
        @empty
        <div class="browse-empty-message" style="grid-column: 1 / -1;">
          No albums available yet.
        </div>
        @endforelse
      </div>
    </section>

    <section aria-label="Genres overview" class="browse-panel" data-panel-id="genres">
      <div class="browse-genre-grid">
        @forelse ($browseGenres as $genre)
        <article class="browse-genre-card">
          <strong>{{ strtoupper($genre['name']) }}</strong>
          <span>{{ $genre['tracks'] }} {{ \Illuminate\Support\Str::plural('track', $genre['tracks']) }}</span>
        </article>
        @empty
        <div class="browse-empty-message" style="grid-column: 1 / -1;">
          No genres available yet.
        </div>
        @endforelse
      </div>
    </section>
  </div>
</div>
@endsection

@push('page-scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.browse-tabs button');
    const panels = document.querySelectorAll('.browse-panel');
    const searchInput = document.querySelector('[data-track-search]');
    const trackRows = document.querySelectorAll('[data-track-row]');
    const artistRows = document.querySelectorAll('[data-artist-row]');
    const albumRows = document.querySelectorAll('[data-album-row]');
    const genreToggle = document.querySelector('[data-genre-toggle]');
    const genreMenu = document.querySelector('[data-genre-menu]');
    const genreLabel = document.querySelector('[data-genre-label]');

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.panel;

        tabs.forEach((btn) => btn.classList.toggle('active', btn === tab));
        panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.panelId === target));
        // Trigger filter when switching tabs
        filterAll();
      });
    });

    let currentGenre = 'all';

    const filterAll = () => {
      const term = (searchInput?.value || '').trim().toLowerCase();
      const activePanel = document.querySelector('.browse-panel.active');
      const currentPanel = activePanel?.dataset.panelId || 'tracks';

      // Filter tracks only if tracks panel is active
      if (currentPanel === 'tracks') {
        trackRows.forEach((row) => {
          const title = row.dataset.title || '';
          const genre = row.dataset.genre || 'all';
          const matchesSearch = !term || title.includes(term);
          const matchesGenre = currentGenre === 'all' || genre === currentGenre;
          row.hidden = !(matchesSearch && matchesGenre);
        });
      } else {
        // Show all tracks when other panels are active
        trackRows.forEach((row) => {
          row.hidden = true;
        });
      }

      // Filter artists only if artists panel is active
      if (currentPanel === 'artists') {
        artistRows.forEach((row) => {
          const title = row.dataset.title || '';
          const genre = row.dataset.genre || 'all';
          const matchesSearch = !term || title.includes(term);
          const matchesGenre = currentGenre === 'all' || genre === currentGenre;
          row.hidden = !(matchesSearch && matchesGenre);
        });
      } else {
        // Show all artists when other panels are active
        artistRows.forEach((row) => {
          row.hidden = true;
        });
      }

      // Filter albums only if albums panel is active
      if (currentPanel === 'albums') {
        albumRows.forEach((row) => {
          const title = row.dataset.title || '';
          const artist = row.dataset.artist || '';
          const matchesSearch = !term || title.includes(term) || artist.includes(term);
          row.hidden = !matchesSearch;
        });
      } else {
        // Show all albums when other panels are active
        albumRows.forEach((row) => {
          row.hidden = true;
        });
      }

      // Filter genres only if genres panel is active
      if (currentPanel === 'genres') {
        const genreCards = document.querySelectorAll('.browse-genre-card');
        genreCards.forEach((card) => {
          const genreName = card.textContent.trim().toLowerCase();
          const matchesSearch = !term || genreName.includes(term);
          card.hidden = !matchesSearch;
        });
      } else {
        // Show all genres when other panels are active
        const genreCards = document.querySelectorAll('.browse-genre-card');
        genreCards.forEach((card) => {
          card.hidden = true;
        });
      }
    };

    searchInput?.addEventListener('input', filterAll);

    genreToggle?.addEventListener('click', () => {
      genreMenu?.classList.toggle('active');
    });

    genreMenu?.addEventListener('click', (event) => {
      const option = event.target.closest('[data-genre-option]');
      if (!option) {
        return;
      }
      currentGenre = option.dataset.genreOption;
      genreLabel.textContent = currentGenre === 'all' ? 'All genres' : option.textContent;
      genreMenu.querySelectorAll('button').forEach((btn) => {
        btn.setAttribute('aria-selected', btn === option ? 'true' : 'false');
      });
      genreMenu.classList.remove('active');
      filterAll();
    });

    document.addEventListener('click', (event) => {
      if (!genreMenu?.classList.contains('active')) {
        return;
      }
      if (!event.target.closest('[data-genre-select]')) {
        genreMenu.classList.remove('active');
      }
    });
  });
</script>
@endpush
