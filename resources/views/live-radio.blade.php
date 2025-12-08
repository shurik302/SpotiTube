{{-- resources/views/live-radio.blade.php --}}
@extends('layouts.app')

@section('title', 'Live Radio · ' . config('app.name', 'SpotiTube'))
@section('sidebar_active', 'live-radio')

@push('page-styles')
<style>
  .radio-shell {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .radio-eyebrow {
    font-size: 1.1rem;
    letter-spacing: 0.12em;
  }

  .radio-divider {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    margin-top: 0.4rem;
  }

  .radio-now-playing {
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 1rem;
    padding: 1rem;
    display: none;
    align-items: center;
    gap: 1rem;
    background: rgba(5, 8, 18, 0.7);
  }

  .radio-now-playing.active {
    display: flex;
  }

  .radio-now-playing img {
    width: 96px;
    height: 96px;
    border-radius: 0.9rem;
    object-fit: cover;
  }

  .radio-now-meta {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
  }

  .radio-now-meta strong {
    font-size: 1.2rem;
  }

  .radio-now-live {
    font-size: 0.78rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.7);
  }

  .radio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1.1rem;
  }

  .radio-card {
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 1rem;
    overflow: hidden;
    background: rgba(4, 6, 16, 0.8);
    cursor: pointer;
    transition: border-color 150ms ease, transform 150ms ease;
  }

  .radio-card:focus-visible,
  .radio-card:hover {
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-2px);
  }

  .radio-card figure {
    margin: 0;
    position: relative;
  }

  .radio-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
    filter: grayscale(1);
  }

  .radio-live {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    font-size: 0.7rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    background: rgba(0, 0, 0, 0.55);
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
  }

  .radio-body {
    padding: 0.9rem 1rem 1.1rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .radio-body strong {
    font-size: 1rem;
  }

  .radio-genre {
    letter-spacing: 0.18em;
    font-size: 0.72rem;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.7);
  }

  .radio-listeners {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    letter-spacing: 0.08em;
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.8);
  }

  body.light .radio-now-playing {
    background: rgba(255, 255, 255, 0.95);
    color: #050914;
  }

  body.light .radio-live,
  body.light .radio-now-live {
    color: rgba(5, 9, 20, 0.6);
  }

  body.light .radio-genre {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .radio-listeners {
    color: rgba(16, 23, 42, 0.7);
  }
</style>
@endpush

@section('content')
<div class="radio-shell">
  <div>
    <p class="radio-eyebrow">LIVE RADIO</p>
    <hr class="radio-divider">
  </div>

  <section class="radio-now-playing" aria-live="polite" data-radio-now>
    <img src="" alt="Live station cover" data-radio-now-cover>
    <div class="radio-now-meta">
      <span class="radio-now-live">LIVE NOW</span>
      <strong data-radio-now-title>Select a station</strong>
      <span class="radio-genre" data-radio-now-genre></span>
      <span class="radio-listeners" data-radio-now-listeners></span>
    </div>
  </section>

<section class="radio-grid" aria-label="Live radio stations">
    @foreach ([
      ['Electronic Beats', 'Electronic', '12 453', 'https://images.unsplash.com/photo-1485579149621-3123dd979885?auto=format&fit=crop&w=900&q=80'],
      ['Jazz Lounge', 'Jazz', '8 234', 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=900&q=80'],
      ['Rock Radio', 'Rock', '15 789', 'https://images.unsplash.com/photo-1464375117522-1311d6a5b81f?auto=format&fit=crop&w=900&q=80'],
      ['Classical FM', 'Classical', '6 543', 'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=900&q=80'],
      ['Hip Hop Nation', 'Hip Hop', '18 945', 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=900&q=80'],
      ['Indie Waves', 'Indie', '9 876', 'https://images.unsplash.com/photo-1497032205916-ac775f0649ae?auto=format&fit=crop&w=900&q=80'],
      ['Pop Hits', 'Pop', '24 532', 'https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?auto=format&fit=crop&w=900&q=80'],
      ['Chill Vibes', 'Ambient', '11 234', 'https://images.unsplash.com/photo-1444824775686-4185f172c44b?auto=format&fit=crop&w=900&q=80'],
    ] as [$station, $genre, $listeners, $img])
    <article class="radio-card" tabindex="0" data-radio-card data-station="{{ $station }}" data-genre="{{ $genre }}"
      data-listeners="{{ $listeners }}" data-cover="{{ $img }}">
      <figure>
        <img src="{{ $img }}" alt="{{ $station }} studio photo" loading="lazy">
        <span class="radio-live">• LIVE</span>
      </figure>
      <div class="radio-body">
        <strong>{{ $station }}</strong>
        <span class="radio-genre">{{ strtoupper($genre) }}</span>
        <span class="radio-listeners">🔊 {{ $listeners }}</span>
      </div>
    </article>
    @endforeach
  </section>
</div>
@endsection

@push('page-scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('[data-radio-card]');
    const nowPlaying = document.querySelector('[data-radio-now]');
    const coverEl = document.querySelector('[data-radio-now-cover]');
    const titleEl = document.querySelector('[data-radio-now-title]');
    const genreEl = document.querySelector('[data-radio-now-genre]');
    const listenersEl = document.querySelector('[data-radio-now-listeners]');
    const audioEl = new Audio();
    audioEl.preload = 'metadata';
    audioEl.autoplay = true;
    let trackLibrary = [];

    if (!cards.length || !nowPlaying) {
      return;
    }

    const seededRandom = (seed) => {
      const x = Math.sin(seed) * 10000;
      return x - Math.floor(x);
    };

    const parseDuration = (len) => {
      if (!len) return null;
      const parts = String(len).split(':').map((v) => Number(v));
      if (parts.length !== 2 || parts.some(Number.isNaN)) return null;
      return parts[0] * 60 + parts[1];
    };

    const loadTrackLibrary = async () => {
      if (trackLibrary.length) return trackLibrary;
      try {
        const res = await fetch('/api/tracks/library');
        if (!res.ok) return [];
        const data = await res.json();
        trackLibrary = data.filter((t) => t.audio);
      } catch (err) {
        console.error('Failed to load library', err);
      }
      return trackLibrary;
    };

    const playRandomForStation = async (stationIndex, stationData) => {
      const library = await loadTrackLibrary();
      if (!library.length) return;

      const now = new Date();
      const baseSeed = stationIndex * 1_000_000 + now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();
      const trackIdx = Math.floor(seededRandom(baseSeed) * library.length);
      const track = library[Math.max(0, Math.min(trackIdx, library.length - 1))];

      // Pick random offset within track duration
      const durationSec = parseDuration(track.length) ?? 0;
      const offset = durationSec > 5 ? Math.floor(seededRandom(baseSeed + 7) * durationSec) : 0;

      const setNowMeta = () => {
        nowPlaying.classList.add('active');
        coverEl?.setAttribute('src', stationData.cover ?? track.cover ?? '');
        coverEl?.setAttribute('alt', `${stationData.station} artwork`);
        if (titleEl) titleEl.textContent = `${stationData.station} · ${track.title}`;
        if (genreEl) genreEl.textContent = (stationData.genre || '').toUpperCase();
        if (listenersEl) listenersEl.textContent = `🔊 ${stationData.listeners ?? ''}`;
      };

      setNowMeta();

      audioEl.src = track.audio;
      audioEl.currentTime = 0;
      audioEl.addEventListener('loadedmetadata', function onMeta() {
        audioEl.removeEventListener('loadedmetadata', onMeta);
        if (offset > 0 && audioEl.duration && offset < audioEl.duration) {
          audioEl.currentTime = offset;
        }
        audioEl.play().catch(() => {});
      });
      // fallback play
      audioEl.play().catch(() => {});
    };

    cards.forEach((card, index) => {
      card.style.setProperty('--delay', `${index * 60}ms`);
      const updateNowPlaying = () => {
        playRandomForStation(index + 1, {
          station: card.dataset.station,
          genre: card.dataset.genre,
          listeners: card.dataset.listeners,
          cover: card.dataset.cover,
        });
      };

      card.addEventListener('click', updateNowPlaying);
      card.addEventListener('keypress', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          updateNowPlaying();
        }
      });
    });
  });
</script>
@endpush
