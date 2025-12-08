{{-- resources/views/admin.blade.php --}}
@php
  use Illuminate\Support\Facades\Storage;
@endphp
@extends('layouts.app')

@section('title', 'Admin · ' . config('app.name', 'SpotiTube'))
@section('sidebar_active', 'admin')

@push('page-styles')
<style>
  .admin-shell {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .admin-alert {
    border: 1px solid rgba(74, 144, 255, 0.35);
    background: rgba(74, 144, 255, 0.08);
    padding: 0.9rem 1.2rem;
    border-radius: 0.8rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .admin-tabs {
    display: flex;
    gap: 2rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    font-size: 0.9rem;
  }

  .admin-tabs button {
    border: none;
    background: transparent;
    color: rgba(255, 255, 255, 0.65);
    cursor: pointer;
    padding-bottom: 0.4rem;
    border-bottom: 2px solid transparent;
  }

  .admin-tabs button.active {
    color: #fff;
    border-color: #fff;
  }

  .admin-panel {
    display: none;
    flex-direction: column;
    gap: 1rem;
  }

  .admin-panel.active {
    display: flex;
  }

  .admin-form {
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.9rem;
    padding: 1rem 1.2rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    background: rgba(5, 8, 18, 0.6);
  }

  .admin-form fieldset {
    border: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0.9rem;
  }

  .admin-form label {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-size: 0.78rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
  }

  .admin-form input,
  .admin-form select,
  .admin-form textarea {
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    background: transparent;
    color: inherit;
    font: inherit;
    padding: 0.4rem 0;
  }

  .admin-form textarea {
    min-height: 90px;
    resize: vertical;
  }

  .admin-form input:focus,
  .admin-form select:focus,
  .admin-form textarea:focus {
    outline: none;
    border-bottom-color: rgba(255, 255, 255, 0.7);
  }

  .admin-form button {
    align-self: flex-start;
    border: none;
    background: #fff;
    color: #050914;
    padding: 0.65rem 1.6rem;
    border-radius: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    cursor: pointer;
  }

  .admin-table-wrapper {
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 0.85rem;
    overflow: hidden;
  }

  .admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.92rem;
  }

  .admin-table th,
  .admin-table td {
    padding: 0.85rem 0.8rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .admin-table th {
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-size: 0.76rem;
    color: rgba(255, 255, 255, 0.65);
  }

  .admin-cover {
    width: 48px;
    height: 48px;
    border-radius: 0.6rem;
    object-fit: cover;
    background: rgba(255, 255, 255, 0.05);
  }

  .admin-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
  }

  .admin-card {
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 1rem;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    background: rgba(5, 8, 18, 0.8);
    position: relative;
  }

  .admin-card figure {
    margin: 0;
    border-radius: 0.9rem;
    overflow: hidden;
  }

  .admin-card figure img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
  }

  .admin-card strong {
    font-size: 1rem;
  }

  .admin-card span {
    font-size: 0.82rem;
    letter-spacing: 0.12em;
    color: rgba(255, 255, 255, 0.7);
  }

  .admin-delete-btn {
    border: none;
    background: rgba(255, 255, 255, 0.1);
    color: inherit;
    border-radius: 0.5rem;
    padding: 0.3rem 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.68rem;
    cursor: pointer;
  }

  .admin-card-action {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
  }

  .admin-delete-icon-btn {
    border: none;
    background: rgba(255, 255, 255, 0.08);
    color: inherit;
    width: 32px;
    height: 32px;
    border-radius: 999px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  body.light .admin-tabs button {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .admin-tabs button.active {
    color: #0d142b;
    border-color: #0d142b;
  }

  body.light .admin-form {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(4, 9, 28, 0.12);
    color: #0d142b;
  }

  body.light .admin-form input,
  body.light .admin-form select,
  body.light .admin-form textarea {
    border-bottom-color: rgba(4, 9, 28, 0.2);
    color: #0d142b;
  }

  body.light .admin-form input:focus,
  body.light .admin-form select:focus,
  body.light .admin-form textarea:focus {
    border-bottom-color: rgba(4, 9, 28, 0.5);
  }

  body.light .admin-form button {
    background: #0d142b;
    color: #fff;
  }

  body.light .admin-table-wrapper {
    border-color: rgba(4, 9, 28, 0.12);
  }

  body.light .admin-table th,
  body.light .admin-table td {
    border-bottom-color: rgba(4, 9, 28, 0.08);
    color: #0d142b;
  }

  body.light .admin-table th {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .admin-cover {
    background: rgba(4, 9, 28, 0.08);
  }

  body.light .admin-card {
    background: rgba(255, 255, 255, 0.9);
    border-color: rgba(4, 9, 28, 0.12);
    color: #0d142b;
  }

  body.light .admin-card span {
    color: rgba(16, 23, 42, 0.65);
  }

  body.light .admin-delete-btn {
    background: rgba(4, 9, 28, 0.08);
    color: #0d142b;
  }

  body.light .admin-delete-icon-btn {
    background: rgba(4, 9, 28, 0.08);
    color: #0d142b;
  }
</style>
@endpush

@section('content')
<div class="admin-shell">
  @if (session('status'))
  <div class="admin-alert">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
  <div class="admin-alert" style="border-color:rgba(255,92,92,0.4);background:rgba(255,92,92,0.1);">
    {{ $errors->first() }}
  </div>
  @endif

  <nav class="admin-tabs" aria-label="Admin categories">
    <button class="active" type="button" data-admin-tab="tracks">Tracks ({{ $tracks->count() }})</button>
    <button type="button" data-admin-tab="artists">Artists ({{ $artists->count() }})</button>
    <button type="button" data-admin-tab="albums">Albums ({{ $albums->count() }})</button>
  </nav>

  <section class="admin-panel active" data-admin-panel="tracks">
    <form class="admin-form" method="POST" action="{{ route('admin.library.tracks.store') }}" enctype="multipart/form-data">
      <h3 style="margin:0;text-transform:uppercase;letter-spacing:0.2em;font-size:0.9rem;">Add track</h3>
      @csrf
      <fieldset>
        <label>
          Title
          <input name="track_title" value="{{ old('track_title') }}" type="text" required>
        </label>
        <label>
          Artist
          <select name="track_artist_id" required>
            <option value="">Select artist</option>
            @foreach ($artists as $artist)
            <option value="{{ $artist->id }}" @selected(old('track_artist_id') == $artist->id)>{{ $artist->name }}</option>
            @endforeach
          </select>
        </label>
        <label>
          Album
          <select name="track_album_id">
            <option value="">Single</option>
            @foreach ($albums as $album)
            <option value="{{ $album->id }}" @selected(old('track_album_id') == $album->id)>{{ $album->title }}</option>
            @endforeach
          </select>
        </label>
        <label>
          Genre
          <input name="track_genre" value="{{ old('track_genre') }}" type="text">
        </label>
        <label>
          Duration (sec)
          <input name="track_duration" value="{{ old('track_duration') }}" type="number" min="1" max="2000">
        </label>
        <label>
          BPM
          <input name="track_bpm" value="{{ old('track_bpm') }}" type="number" min="40" max="300">
        </label>
        <label>
          Track #
          <input name="track_number" value="{{ old('track_number') }}" type="number" min="1" max="40">
        </label>
        <label>
          Audio file
          <input name="track_audio" type="file" accept="audio/mpeg,audio/mp3,audio/wav,audio/flac">
        </label>
        <label>
          Track cover
          <input name="track_cover" type="file" accept="image/*">
        </label>
      </fieldset>
      <button type="submit">Save track</button>
    </form>

    <div class="admin-table-wrapper" role="region" aria-label="Track inventory">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Cover</th>
            <th>Title</th>
            <th>Artist</th>
            <th>Album</th>
            <th>Genre</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($tracks as $track)
          @php
            $coverPath = $track->cover_path ?? $track->album?->cover_path ?? $track->artist?->photo_path;
            $coverUrl = $coverPath ? Storage::disk('public')->url($coverPath) : 'https://via.placeholder.com/80?text=Track';
          @endphp
          <tr>
            <td><img class="admin-cover" src="{{ $coverUrl }}" alt="Cover for {{ $track->title }}" loading="lazy"></td>
            <td>{{ $track->title }}</td>
            <td>{{ $track->artist?->name ?? '—' }}</td>
            <td>{{ $track->album?->title ?? '—' }}</td>
            <td>{{ $track->genre ?? '—' }}</td>
            <td>
              <form method="POST" action="{{ route('admin.library.tracks.destroy', $track) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-delete-btn">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6">No tracks yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="admin-panel" data-admin-panel="artists">
    <form class="admin-form" method="POST" action="{{ route('admin.library.artists.store') }}" enctype="multipart/form-data">
      <h3 style="margin:0;text-transform:uppercase;letter-spacing:0.2em;font-size:0.9rem;">Add artist</h3>
      @csrf
      <fieldset>
        <label>
          Name
          <input name="artist_name" value="{{ old('artist_name') }}" type="text" required>
        </label>
        <label>
          Genre
          <input name="artist_genre" value="{{ old('artist_genre') }}" type="text">
        </label>
        <label>
          Photo
          <input name="artist_photo" type="file" accept="image/*">
        </label>
        <label style="grid-column:1/-1;">
          Bio
          <textarea name="artist_bio">{{ old('artist_bio') }}</textarea>
        </label>
      </fieldset>
      <label style="letter-spacing:0.12em;">
        Instagram
        <input name="social_links[instagram]" value="{{ old('social_links.instagram') }}" type="url" placeholder="https://instagram.com/...">
      </label>
      <button type="submit">Save artist</button>
    </form>

    <div class="admin-card-grid">
      @forelse ($artists as $artist)
      @php
        $photoUrl = $artist->photo_path ? Storage::disk('public')->url($artist->photo_path) : 'https://via.placeholder.com/400?text=Artist';
      @endphp
      <article class="admin-card">
        <div class="admin-card-action">
          <form method="POST" action="{{ route('admin.library.artists.destroy', $artist) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="admin-delete-icon-btn" aria-label="Delete {{ $artist->name }}">×</button>
          </form>
        </div>
        <figure>
          <img src="{{ $photoUrl }}" alt="{{ $artist->name }} photo" loading="lazy">
        </figure>
        <strong>{{ $artist->name }}</strong>
        <span>{{ $artist->genre ?? 'Unknown' }}</span>
        @if ($artist->social_links && isset($artist->social_links['instagram']))
        <span><a href="{{ $artist->social_links['instagram'] }}" target="_blank" rel="noopener" style="color:inherit;">Instagram</a></span>
        @endif
      </article>
      @empty
      <p>No artists yet.</p>
      @endforelse
    </div>
  </section>

  <section class="admin-panel" data-admin-panel="albums">
    <form class="admin-form" method="POST" action="{{ route('admin.library.albums.store') }}" enctype="multipart/form-data">
      <h3 style="margin:0;text-transform:uppercase;letter-spacing:0.2em;font-size:0.9rem;">Add album</h3>
      @csrf
      <fieldset>
        <label>
          Title
          <input name="album_title" value="{{ old('album_title') }}" type="text" required>
        </label>
        <label>
          Artist
          <select name="album_artist_id" required>
            <option value="">Select artist</option>
            @foreach ($artists as $artist)
            <option value="{{ $artist->id }}" @selected(old('album_artist_id') == $artist->id)>{{ $artist->name }}</option>
            @endforeach
          </select>
        </label>
        <label>
          Year
          <input name="album_year" value="{{ old('album_year') }}" type="number" min="1900" max="{{ now()->year + 1 }}">
        </label>
        <label>
          Label
          <input name="album_label" value="{{ old('album_label') }}" type="text">
        </label>
        <label>
          Cover
          <input name="album_cover" type="file" accept="image/*">
        </label>
      </fieldset>
      <button type="submit">Save album</button>
    </form>

    <div class="admin-card-grid">
      @forelse ($albums as $album)
      @php
        $coverUrl = $album->cover_path ? Storage::disk('public')->url($album->cover_path) : 'https://via.placeholder.com/400?text=Album';
      @endphp
      <article class="admin-card">
        <div class="admin-card-action">
          <form method="POST" action="{{ route('admin.library.albums.destroy', $album) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="admin-delete-icon-btn" aria-label="Delete {{ $album->title }}">×</button>
          </form>
        </div>
        <figure>
          <img src="{{ $coverUrl }}" alt="{{ $album->title }} cover" loading="lazy">
        </figure>
        <strong>{{ $album->title }}</strong>
        <span>{{ $album->artist?->name ?? 'Unknown artist' }}</span>
        <span>{{ $album->year ?? '—' }}</span>
      </article>
      @empty
      <p>No albums yet.</p>
      @endforelse
    </div>
  </section>
</div>
@endsection

@push('page-scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('[data-admin-tab]');
    const panels = document.querySelectorAll('[data-admin-panel]');

    const setTab = (tabId) => {
      tabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.adminTab === tabId));
      panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.adminPanel === tabId));
    };

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => setTab(tab.dataset.adminTab));
    });
  });
</script>
@endpush
