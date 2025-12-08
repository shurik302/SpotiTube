{{-- resources/views/playlists.blade.php --}}
@extends('layouts.app')

@section('title', 'Playlists ‚ú ' . config('app.name', 'SpotiTube'))
@section('sidebar_active', 'playlists')

@php
  // Data comes from PlaylistController
  // $playlists and $initialPlaylist are passed from the controller
  // If not set, use empty collections
  $playlists = $playlists ?? collect([]);
  $initialPlaylist = $initialPlaylist ?? null;
@endphp

@push('page-styles')
<style>
  .playlists-shell {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
  }

  .playlists-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .playlists-eyebrow {
    font-size: 1.1rem;
    letter-spacing: 0.12em;
  }

  .playlists-divider {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.15);
    margin-top: 0.35rem;
  }

  .playlists-create {
    border: 1px solid rgba(255, 255, 255, 0.25);
    background: rgba(255, 255, 255, 0.04);
    color: inherit;
    padding: 0.55rem 1.6rem;
    border-radius: 0.4rem;
    text-transform: uppercase;
    letter-spacing: 0.16em;
    display: inline-flex;
    gap: 0.55rem;
    align-items: center;
    font-size: 0.82rem;
    cursor: pointer;
  }

  .playlists-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 1.5rem;
    min-height: 360px;
  }

  .playlists-list {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
  }

  .playlist-card {
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 0.8rem;
    padding: 0.8rem;
    display: flex;
    gap: 0.8rem;
    align-items: center;
    background: rgba(10, 15, 35, 0.5);
    cursor: pointer;
    transition: border-color 140ms ease, background 140ms ease, color 140ms ease;
    position: relative;
  }

  .playlist-card.active {
    border-color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.95);
    color: #050914;
  }

  .playlist-card img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 0.6rem;
  }

  .playlist-meta {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    flex: 1;
  }

  .playlist-meta strong {
    font-size: 1.05rem;
  }

  .playlist-meta span {
    font-size: 0.75rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.65);
  }

  .playlist-card.active .playlist-meta span {
    color: rgba(5, 9, 20, 0.55);
  }

  .playlist-detail {
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 0.9rem;
    padding: 1.4rem;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    background: rgba(5, 8, 18, 0.55);
  }

  .playlist-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
  }

  .playlist-detail-title {
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 1rem;
  }

  .playlist-detail-count {
    display: block;
    font-size: 0.78rem;
    letter-spacing: 0.18em;
    color: rgba(255, 255, 255, 0.65);
    margin-top: 0.25rem;
  }

  .playlist-add,
  .playlists-create {
    border: 1px solid rgba(255, 255, 255, 0.25);
    background: rgba(255, 255, 255, 0.05);
    color: inherit;
    padding: 0.4rem 1.2rem;
    border-radius: 0.4rem;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.76rem;
    display: inline-flex;
    gap: 0.4rem;
    align-items: center;
    cursor: pointer;
  }

  .playlist-tracks {
    list-style: none;
    margin: 0;
    padding: 0;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .playlist-track {
    display: grid;
    grid-template-columns: 32px 52px 1fr auto;
    align-items: center;
    gap: 0.8rem;
    padding: 0.8rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  }

  .playlist-track:last-child {
    border-bottom: none;
  }

  .playlist-track img {
    width: 52px;
    height: 52px;
    border-radius: 0.8rem;
    object-fit: cover;
  }

  .playlist-track strong {
    display: block;
    font-size: 0.95rem;
  }

  .playlist-track span {
    display: block;
    font-size: 0.78rem;
    letter-spacing: 0.15em;
    color: rgba(255, 255, 255, 0.7);
  }

  .playlist-track button {
    border: none;
    background: transparent;
    color: inherit;
    font-size: 1rem;
    cursor: pointer;
  }

  @media (max-width: 900px) {
    .playlists-grid {
      grid-template-columns: 1fr;
    }
  }

  body.light .playlist-card.active {
    border-color: rgba(5, 8, 18, 0.6);
  }

  body.light .playlist-detail {
    background: rgba(255, 255, 255, 0.92);
    color: #050914;
  }

  body.light .playlist-detail-count,
  body.light .playlist-track span {
    color: rgba(5, 9, 20, 0.6);
  }

  .playlist-delete {
    position: absolute;
    top: 0.6rem;
    right: 0.6rem;
    border: none;
    background: rgba(255, 255, 255, 0.08);
    color: inherit;
    width: 30px;
    height: 30px;
    border-radius: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 120ms ease;
    cursor: pointer;
  }

  .playlist-card:hover .playlist-delete {
    opacity: 1;
    pointer-events: auto;
  }

  .playlist-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(3, 5, 12, 0.85);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10;
  }

  .playlist-modal-overlay.active {
    display: flex;
  }

  .playlist-modal {
    width: min(420px, 90vw);
    background: rgba(7, 10, 24, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 0.9rem;
    padding: 1.6rem;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
  }

  .playlist-modal header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.85rem;
  }

  .playlist-modal header button {
    border: none;
    background: transparent;
    color: inherit;
    font-size: 1rem;
    cursor: pointer;
  }

  .playlist-modal label {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.75rem;
  }

  .playlist-modal input {
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.4);
    padding: 0.4rem 0;
    background: transparent;
    color: inherit;
    font: inherit;
  }

  .playlist-modal input:focus {
    outline: none;
    border-bottom-color: rgba(255, 255, 255, 0.8);
  }

  .playlist-modal footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.8rem;
    font-size: 0.78rem;
    letter-spacing: 0.18em;
  }

  .playlist-modal footer button {
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: transparent;
    color: inherit;
    padding: 0.45rem 1.4rem;
    border-radius: 0.4rem;
    text-transform: uppercase;
    cursor: pointer;
  }

  .playlist-modal footer button:last-child {
    background: #fff;
    color: #050914;
    border-color: #fff;
  }

  .playlist-track-modal {
    gap: 1.2rem;
  }

  .playlist-track-search input {
    border-bottom-color: rgba(255, 255, 255, 0.3);
  }

  .playlist-track-list {
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 320px;
    overflow-y: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
  }

  .playlist-track-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.8rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .playlist-track-option:last-child {
    border-bottom: none;
  }

  .playlist-track-option button {
    border: none;
    background: transparent;
    color: inherit;
    font-size: 1.2rem;
    cursor: pointer;
  }

  .playlist-track-option img {
    width: 48px;
    height: 48px;
    border-radius: 0.8rem;
    object-fit: cover;
    margin-right: 0.6rem;
  }

  .playlist-track-info {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    flex: 1;
  }

  .playlist-track-info div {
    display: flex;
    flex-direction: column;
  }

  .playlist-track-info strong {
    font-size: 0.95rem;
  }

  .playlist-track-info span {
    font-size: 0.78rem;
    letter-spacing: 0.15em;
    color: rgba(255, 255, 255, 0.65);
  }

  body.light .playlist-meta span {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .playlist-detail {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(4, 9, 28, 0.12);
    color: #0d142b;
  }

  body.light .playlist-detail-title {
    color: #0d142b;
  }

  body.light .playlist-add,
  body.light .playlists-create {
    border-color: rgba(4, 9, 28, 0.2);
    background: rgba(4, 9, 28, 0.05);
    color: #0d142b;
  }

  body.light .playlist-track-info span {
    color: rgba(16, 23, 42, 0.65);
  }
</style>
@endpush

@section('content')
<div class="playlists-shell">
  <div class="playlists-header">
    <div>
      <p class="playlists-eyebrow" data-i18n="playlists_title">PLAYLISTS</p>
      <hr class="playlists-divider">
    </div>
    <button class="playlists-create" type="button" data-playlist-create-trigger>
      <span aria-hidden="true">+</span>
      <span data-i18n="create_btn">CREATE</span>
    </button>
  </div>

  <div class="playlists-grid">
    <section class="playlists-list" aria-label="Available playlists">
      @foreach ($playlists as $playlist)
      <article class="playlist-card {{ $loop->first ? 'active' : '' }}" data-playlist-id="{{ $playlist['id'] }}"
        data-playlist='@json($playlist)'>
        <img src="{{ $playlist['cover'] }}" alt="{{ $playlist['name'] }} cover" loading="lazy">
        <div class="playlist-meta">
          <strong>{{ $playlist['name'] }}</strong>
          <span>{{ $playlist['tracks_count'] ?? count($playlist['tracks']) }} TRACKS</span>
        </div>
        <button class="playlist-delete" type="button" aria-label="Delete {{ $playlist['name'] }}" data-playlist-delete>
          &#128465;
        </button>
      </article>
      @endforeach
    </section>

    <section class="playlist-detail" aria-label="Playlist details" data-playlist-detail>
      <div class="playlist-detail-header">
        <div>
          <p class="playlist-detail-title" data-playlist-title>{{ $initialPlaylist ? strtoupper($initialPlaylist['name']) : 'NO PLAYLIST' }}</p>
          <span class="playlist-detail-count" data-playlist-count>{{ $initialPlaylist ? ($initialPlaylist['tracks_count'] ?? count($initialPlaylist['tracks'])) . ' TRACKS' : '0 TRACKS' }}</span>
        </div>
        <button class="playlist-add" type="button" data-track-library-open {{ !$initialPlaylist ? 'hidden' : '' }}>
          <span aria-hidden="true">+</span>
          ADD
        </button>
      </div>
      <div data-playlist-empty {{ $initialPlaylist && !empty($initialPlaylist['tracks']) ? 'hidden' : '' }}>{{ $initialPlaylist ? 'No tracks yet. Add some!' : 'Create your first playlist to get started!' }}</div>
      <ol class="playlist-tracks" data-playlist-tracks {{ !$initialPlaylist || empty($initialPlaylist['tracks']) ? 'hidden' : '' }}>
        @if ($initialPlaylist)
        @foreach ($initialPlaylist['tracks'] as $index => $track)
        <li class="playlist-track">
          <span>{{ $index + 1 }}</span>
          <img src="{{ $track['cover'] }}" alt="{{ $track['title'] }} cover" loading="lazy">
          <div>
            <strong>{{ $track['title'] }}</strong>
            <span>{{ strtoupper($track['artist']) }}</span>
          </div>
          <button type="button" aria-label="Play {{ $track['title'] }}" data-track-play
            data-track-title="{{ $track['title'] }}" data-track-artist="{{ $track['artist'] }}"
            data-track-cover="{{ $track['cover'] }}" data-track-length="{{ $track['length'] }}"
            @if(isset($track['audio'])) data-track-audio="{{ $track['audio'] }}" @endif>&#9654;</button>
        </li>
        @endforeach
        @endif
      </ol>
    </section>
  </div>

<div class="playlist-modal-overlay" data-playlist-modal aria-hidden="true">
    <div class="playlist-modal" role="dialog" aria-modal="true" aria-labelledby="playlist-modal-title">
      <header>
        <span id="playlist-modal-title">Create playlist</span>
        <button type="button" aria-label="Close" data-playlist-modal-close>&times;</button>
      </header>
      <label>
        Playlist name
        <input type="text" name="playlist_name" placeholder="Enter name" data-playlist-input autocomplete="off">
      </label>
      <footer>
        <button type="button" data-playlist-modal-close>Cancel</button>
        <button type="button" data-playlist-modal-submit>Create</button>
      </footer>
    </div>
  </div>
</div>

<div class="playlist-modal-overlay" data-track-library aria-hidden="true">
  <div class="playlist-modal playlist-track-modal" role="dialog" aria-modal="true"
    aria-labelledby="playlist-track-modal-title">
    <header>
      <span id="playlist-track-modal-title" data-track-library-title>Add to playlist</span>
      <button type="button" aria-label="Close" data-track-library-close>&times;</button>
    </header>
    <label class="playlist-track-search">
      <span>Search tracks</span>
      <input type="search" placeholder="Start typing a title" data-track-library-search autocomplete="off">
    </label>
    <ul class="playlist-track-list" data-track-library-list></ul>
    <p data-track-library-empty hidden>No tracks found.</p>
  </div>
</div>
@endsection

@push('page-scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    let playlists = @json($playlists ?? []);
    let trackLibrary = [];
    const playlistContainer = document.querySelector('.playlists-list');
    const getPlaylistCards = () => document.querySelectorAll('.playlist-card');
    const titleEl = document.querySelector('[data-playlist-title]');
    const countEl = document.querySelector('[data-playlist-count]');
    const tracksEl = document.querySelector('[data-playlist-tracks]');
    const emptyStateEl = document.querySelector('[data-playlist-empty]');
    const modal = document.querySelector('[data-playlist-modal]');
    const modalInput = document.querySelector('[data-playlist-input]');
    const modalTriggers = document.querySelectorAll('[data-playlist-create-trigger]');
    const modalCloseButtons = document.querySelectorAll('[data-playlist-modal-close]');
    const modalSubmit = document.querySelector('[data-playlist-modal-submit]');
    const addTrackButton = document.querySelector('[data-track-library-open]');
    const trackModal = document.querySelector('[data-track-library]');
    const trackModalList = document.querySelector('[data-track-library-list]');
    const trackModalSearch = document.querySelector('[data-track-library-search]');
    const trackModalTitle = document.querySelector('[data-track-library-title]');
    const trackModalEmpty = document.querySelector('[data-track-library-empty]');
    const trackModalClose = document.querySelector('[data-track-library-close]');
    
    // Initialize with first playlist or null
    let activePlaylistId = playlists[0]?.id ?? null;

    const renderTracks = (tracks) => {
      tracksEl.innerHTML = '';
      if (!tracks.length) {
        tracksEl.hidden = true;
        if (emptyStateEl) {
          emptyStateEl.hidden = false;
          emptyStateEl.textContent = 'No tracks yet.';
        }
        return;
      }
      tracksEl.hidden = false;
      if (emptyStateEl) {
        emptyStateEl.hidden = true;
      }
      tracks.forEach((track, index) => {
        const item = document.createElement('li');
        item.className = 'playlist-track';
        item.innerHTML = `
          <span>${index + 1}</span>
          <img src="${track.cover}" alt="${track.title} cover" loading="lazy">
          <div>
            <strong>${track.title}</strong>
            <span>${track.artist.toUpperCase()}</span>
          </div>
          <button type="button" aria-label="Play ${track.title}" data-track-play
            data-track-title="${track.title}" data-track-artist="${track.artist}"
            data-track-cover="${track.cover}" data-track-length="${track.length}"
            ${track.audio ? `data-track-audio="${track.audio}"` : ''}>&#9654;</button>
        `;
        tracksEl.appendChild(item);
      });
    };

    const updateCardData = (playlist) => {
      const card = playlistContainer?.querySelector(`[data-playlist-id="${playlist.id}"]`);
      if (!card) {
        return;
      }
      card.dataset.playlist = JSON.stringify(playlist);
      const countNode = card.querySelector('.playlist-meta span');
      if (countNode) {
        countNode.textContent = `${playlist.tracks.length} TRACKS`;
      }
    };

    const updateDetail = (playlist) => {
      activePlaylistId = playlist.id;
      titleEl.textContent = playlist.name.toUpperCase();
      countEl.textContent = `${playlist.tracks.length} ${playlist.tracks.length === 1 ? 'TRACK' : 'TRACKS'}`;
      renderTracks(playlist.tracks);
      updateCardData(playlist);
    };

    if (playlists.length) {
      updateDetail(playlists[0]);
      const firstCard = playlistContainer?.querySelector(`[data-playlist-id="${playlists[0].id}"]`);
      firstCard?.classList.add('active');
    }

    // Delegated click to switch playlists
    playlistContainer?.addEventListener('click', (e) => {
      const card = e.target.closest('.playlist-card');
      if (!card) return;
      playlistContainer.querySelectorAll('.playlist-card').forEach((c) => c.classList.remove('active'));
      card.classList.add('active');
      const cardData = (() => {
        try {
          return JSON.parse(card.dataset.playlist || '{}');
        } catch {
          return {};
        }
      })();
      if (!cardData.id) return;
      const playlist = playlists.find((p) => p.id == cardData.id) || cardData;
      updateDetail(playlist);
    });

    const removePlaylist = async (playlistId) => {
      const index = playlists.findIndex((item) => item.id === playlistId);
      if (index === -1) {
        return;
      }

      // Delete from database
      try {
        const response = await fetch(`/playlists/session/${playlistId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        });
        if (!response.ok) {
          console.error('Failed to delete playlist');
          return;
        }
      } catch (error) {
        console.error('Error deleting playlist:', error);
        return;
      }

      playlists = playlists.filter((item) => item.id !== playlistId);
      const card = playlistContainer?.querySelector(`[data-playlist-id="${playlistId}"]`);
      const wasActive = card?.classList.contains('active');
      card?.remove();

      if (!playlists.length) {
        titleEl.textContent = 'No playlist';
        countEl.textContent = '0 TRACKS';
        tracksEl.innerHTML = '';
        tracksEl.hidden = true;
        if (emptyStateEl) {
          emptyStateEl.hidden = false;
          emptyStateEl.textContent = 'No playlists available.';
        }
        return;
      }

      if (wasActive) {
        const next = playlists[0];
        const nextCard = playlistContainer?.querySelector(`[data-playlist-id="${next.id}"]`);
        nextCard?.classList.add('active');
        updateDetail(next);
      }
    };

    const attachCardListeners = () => {
      getPlaylistCards().forEach((card) => {
        card.addEventListener('click', () => {
          const playlistId = card.dataset.playlistId;
          const playlist = playlists.find((item) => item.id === playlistId);
          if (!playlist) {
            return;
          }
          getPlaylistCards().forEach((item) => item.classList.toggle('active', item === card));
          updateDetail(playlist);
        });
        const deleteBtn = card.querySelector('[data-playlist-delete]');
        deleteBtn?.addEventListener('click', (event) => {
          event.stopPropagation();
          removePlaylist(card.dataset.playlistId);
        });
      });
    };

    attachCardListeners();

    const toggleModal = (visible) => {
      if (!modal) {
        return;
      }
      modal.classList.toggle('active', visible);
      modal.setAttribute('aria-hidden', visible ? 'false' : 'true');
      if (visible && modalInput) {
        modalInput.value = '';
        modalInput.focus();
      }
    };

    modalTriggers.forEach((btn) => btn.addEventListener('click', () => toggleModal(true)));
    modalCloseButtons.forEach((btn) => btn.addEventListener('click', () => toggleModal(false)));

    if (modal) {
      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          toggleModal(false);
        }
      });
    }

    const createPlaylistCard = (playlist) => {
      const article = document.createElement('article');
      article.className = 'playlist-card';
      article.dataset.playlistId = playlist.id;
      article.dataset.playlist = JSON.stringify(playlist);
      article.innerHTML = `
        <img src="${playlist.cover}" alt="${playlist.name} cover" loading="lazy">
        <div class="playlist-meta">
          <strong>${playlist.name}</strong>
          <span>${playlist.tracks.length} TRACKS</span>
        </div>
        <button class="playlist-delete" type="button" aria-label="Delete ${playlist.name}" data-playlist-delete>
          &#128465;
        </button>
      `;
      article.addEventListener('click', () => {
        getPlaylistCards().forEach((item) => item.classList.toggle('active', item === article));
        updateDetail(playlist);
      });
      const deleteBtn = article.querySelector('[data-playlist-delete]');
      deleteBtn?.addEventListener('click', (event) => {
        event.stopPropagation();
        removePlaylist(playlist.id);
      });
      return article;
    };

    const DEFAULT_COVER = 'https://images.unsplash.com/photo-1485579149621-3123dd979885?auto=format&fit=crop&w=300&q=80';

    modalSubmit?.addEventListener('click', async () => {
      if (!modalInput) {
        return;
      }
      const name = modalInput.value.trim();
      if (!name) {
        modalInput.focus();
        return;
      }

      // Create playlist in database
      try {
        const response = await fetch('/playlists/session', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
          body: JSON.stringify({ name }),
        });

        if (!response.ok) {
          console.error('Failed to create playlist');
          toggleModal(false);
          return;
        }

        const newPlaylist = await response.json();
        playlists = [...playlists, newPlaylist];
        const card = createPlaylistCard(newPlaylist);
        getPlaylistCards().forEach((item) => item.classList.remove('active'));
        card.classList.add('active');
        playlistContainer?.appendChild(card);
        updateDetail(newPlaylist);
        toggleModal(false);
      } catch (error) {
        console.error('Error creating playlist:', error);
        toggleModal(false);
      }
    });

    const toggleTrackModal = (visible) => {
      if (!trackModal) {
        return;
      }
      trackModal.classList.toggle('active', visible);
      trackModal.setAttribute('aria-hidden', visible ? 'false' : 'true');
      if (visible && trackModalSearch) {
        trackModalSearch.value = '';
        renderTrackLibrary();
        trackModalSearch.focus();
      }
    };

    const renderTrackLibrary = async () => {
      if (!trackModalList) {
        return;
      }
      const playlist = playlists.find((item) => item.id === activePlaylistId);
      trackModalList.innerHTML = '<li style="padding: 1rem; text-align: center;">Loading...</li>';

      // Load tracks from API if not loaded yet
      if (!trackLibrary.length) {
        try {
          const response = await fetch('/api/tracks/library');
          if (response.ok) {
            trackLibrary = await response.json();
          }
        } catch (error) {
          console.error('Error loading track library:', error);
        }
      }

      trackModalList.innerHTML = '';
      const term = (trackModalSearch?.value || '').trim().toLowerCase();
      const matches = trackLibrary.filter((track) => 
        track.title.toLowerCase().includes(term) || 
        track.artist.toLowerCase().includes(term)
      );
      if (!matches.length) {
        trackModalEmpty?.removeAttribute('hidden');
        return;
      }
      trackModalEmpty?.setAttribute('hidden', 'hidden');
      matches.forEach((track) => {
        const li = document.createElement('li');
        li.className = 'playlist-track-option';
        const alreadyAdded = playlist?.tracks.some((item) => item.id == track.id);
        li.innerHTML = `
          <div class="playlist-track-info">
            <img src="${track.cover}" alt="${track.title} cover" loading="lazy">
            <div>
              <strong>${track.title}</strong>
              <span>${track.artist.toUpperCase()}</span>
            </div>
          </div>
          <button type="button" ${alreadyAdded ? 'disabled' : ''} data-track-add="${track.id}">
            ${alreadyAdded ? '✓' : '+'}
          </button>
        `;
        trackModalList.appendChild(li);
      });
    };

    addTrackButton?.addEventListener('click', async () => {
      const playlist = playlists.find((item) => item.id === activePlaylistId);
      if (!playlist) {
        return;
      }
      if (trackModalTitle) {
        trackModalTitle.textContent = `Add to ${playlist.name}`;
      }
      toggleTrackModal(true);
      await renderTrackLibrary();
    });

    trackModalSearch?.addEventListener('input', () => renderTrackLibrary());
    trackModalClose?.addEventListener('click', () => toggleTrackModal(false));
    trackModal?.addEventListener('click', (event) => {
      if (event.target === trackModal) {
        toggleTrackModal(false);
      }
    });

    trackModalList?.addEventListener('click', async (event) => {
      const button = event.target.closest('[data-track-add]');
      if (!button || button.disabled) {
        return;
      }
      const trackId = button.dataset.trackAdd;
      const track = trackLibrary.find((item) => item.id == trackId);
      const playlist = playlists.find((item) => item.id === activePlaylistId);
      if (!track || !playlist) {
        return;
      }
      if (playlist.tracks.some((item) => item.id == trackId)) {
        return;
      }

      // Add track to playlist in database
      try {
        const response = await fetch(`/playlists/session/${playlist.id}/tracks`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
          body: JSON.stringify({ track_id: trackId }),
        });

        if (!response.ok) {
          console.error('Failed to add track to playlist');
          toggleTrackModal(false);
          return;
        }

        const updatedPlaylist = await response.json();
        // Update local playlist data
        const playlistIndex = playlists.findIndex((item) => item.id === playlist.id);
        if (playlistIndex !== -1) {
          playlists[playlistIndex] = updatedPlaylist;
        }
        updateDetail(updatedPlaylist);
        // Update card cover if it changed
        const card = playlistContainer?.querySelector(`[data-playlist-id="${playlist.id}"]`);
        if (card) {
          const img = card.querySelector('img');
          if (img && updatedPlaylist.cover) {
            img.src = updatedPlaylist.cover;
          }
        }
        toggleTrackModal(false);
      } catch (error) {
        console.error('Error adding track to playlist:', error);
        toggleTrackModal(false);
      }
    });
  });
</script>
@endpush
