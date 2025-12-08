{{-- resources/views/settings.blade.php --}}
@extends('layouts.app')

@section('title', 'Settings ' . config('app.name', 'SpotiTube'))
@section('sidebar_active', 'settings')

@php
  $settingsTabs = [
      ['id' => 'twofa', 'label' => 'Two-factor auth', 'icon' => '🔐'],
      ['id' => 'theme', 'label' => 'Theme & appearance', 'icon' => '🎨'],
      ['id' => 'profile', 'label' => 'Profile', 'icon' => '👤'],
      ['id' => 'danger', 'label' => 'Danger zone', 'icon' => '⚠️'],
      ['id' => 'logout', 'label' => 'Sign out', 'icon' => '🚪'],
  ];
@endphp

@push('page-styles')
<style>
  .settings-shell {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .settings-header h1 {
    margin: 0;
    font-size: 2rem;
  }

  .settings-layout {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 1.5rem;
  }

  .settings-tabs {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
  }

  .settings-tabs button {
    display: flex;
    gap: 0.6rem;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: transparent;
    color: inherit;
    padding: 0.6rem 0.9rem;
    border-radius: 0.8rem;
    cursor: pointer;
    text-align: left;
    letter-spacing: 0.08em;
  }

  .settings-tabs button.active {
    border-color: rgba(255, 255, 255, 0.6);
    background: rgba(255, 255, 255, 0.05);
  }

  .settings-panel {
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1rem;
    padding: 1.4rem;
    display: none;
    flex-direction: column;
    gap: 1rem;
    background: rgba(5, 8, 18, 0.8);
  }

  .settings-panel.active {
    display: flex;
  }

  .settings-panel h2 {
    margin: 0;
    font-size: 1.25rem;
  }

  .settings-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
  }

  .settings-field label {
    text-transform: uppercase;
    letter-spacing: 0.18em;
    font-size: 0.75rem;
  }

  .settings-field input,
  .settings-field select {
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    background: transparent;
    color: inherit;
    font: inherit;
    padding: 0.4rem 0;
  }

  .settings-field input:focus,
  .settings-field select:focus {
    outline: none;
    border-bottom-color: rgba(255, 255, 255, 0.6);
  }

  .settings-actions {
    display: flex;
    gap: 0.8rem;
  }

  .settings-actions button {
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: transparent;
    color: inherit;
    padding: 0.5rem 1.4rem;
    border-radius: 0.4rem;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    cursor: pointer;
  }

  .danger-panel {
    border-color: rgba(255, 92, 92, 0.4);
    background: rgba(86, 5, 18, 0.35);
  }

  .danger-panel button {
    border-color: rgba(255, 92, 92, 0.7);
    color: #ff5c5c;
  }

  body.light .settings-field input,
  body.light .settings-field select {
    border-bottom-color: rgba(4, 9, 28, 0.2);
    color: #0d142b;
  }

  body.light .settings-field input:focus,
  body.light .settings-field select:focus {
    border-bottom-color: rgba(4, 9, 28, 0.5);
  }

  body.light .settings-actions button {
    border-color: rgba(4, 9, 28, 0.2);
    color: #0d142b;
  }

  body.light .settings-actions button:hover {
    border-color: rgba(4, 9, 28, 0.4);
    background: rgba(4, 9, 28, 0.06);
  }
</style>
@endpush

@section('content')
<div class="settings-shell">
  <div class="settings-header">
    <h1 data-i18n="settings_title">Settings</h1>
    <p data-i18n="settings_subtitle">Fine tune how SpotiTube looks, feels, and secures your account.</p>
  </div>
  <div class="settings-layout">
    <nav class="settings-tabs" aria-label="Settings sections">
      @foreach ($settingsTabs as $tab)
      @php
        $i18nKey = match($tab['id']) {
          'twofa' => 'tab_twofa',
          'theme' => 'tab_theme',
          'profile' => 'tab_profile',
          'danger' => 'tab_danger',
          'logout' => 'tab_logout',
          default => null,
        };
      @endphp
      <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-settings-tab="{{ $tab['id'] }}">
        <span aria-hidden="true">{{ $tab['icon'] }}</span>
        <span @if($i18nKey) data-i18n="{{ $i18nKey }}" @endif>{{ $tab['label'] }}</span>
      </button>
      @endforeach
    </nav>

    <div>
      <section class="settings-panel active" data-settings-panel="twofa">
        <h2 data-i18n="twofa_title">Two-factor authentication</h2>
        <p data-i18n="twofa_text">Require a secondary code every time you sign in. Use an authenticator app or SMS.</p>
        <div class="settings-field">
          <label for="twofa-method" data-i18n="twofa_method">Method</label>
          <select id="twofa-method">
            <option value="app" data-i18n="twofa_app">Authenticator app</option>
            <option value="sms" data-i18n="twofa_sms">SMS code</option>
          </select>
        </div>
        <div class="settings-actions">
          <button type="button" data-i18n="twofa_backup">Generate backup codes</button>
          <button type="button" data-i18n="twofa_enable">Enable 2FA</button>
        </div>
      </section>

      <section class="settings-panel" data-settings-panel="theme">
        <h2 data-i18n="theme_title">Theme & appearance</h2>
        <p data-i18n="theme_text">Pick a default theme and accent color for your library.</p>
        <div class="settings-field">
          <label for="theme-mode" data-i18n="theme_label">Theme</label>
          <select id="theme-mode">
            <option value="system" data-i18n="theme_system">Follow system</option>
            <option value="dark" data-i18n="theme_dark">Dark</option>
            <option value="light" data-i18n="theme_light">Light</option>
          </select>
        </div>
        <div class="settings-field">
          <label for="lang-select" data-i18n="lang_label">Language</label>
          <select id="lang-select">
            <option value="en" data-i18n="lang_en">English</option>
            <option value="uk" data-i18n="lang_uk">Українська</option>
          </select>
        </div>
        <div class="settings-field">
          <label for="accent-color" data-i18n="accent_label">Accent color</label>
          <input id="accent-color" type="color" value="#4a90ff">
        </div>
        <div class="settings-actions">
          <button type="button" data-i18n="save_appearance">Save appearance</button>
        </div>
      </section>

      <section class="settings-panel" data-settings-panel="profile">
        <h2 data-i18n="profile_title">Profile settings</h2>
        <div class="settings-field">
          <label for="display-name" data-i18n="display_name">Display name</label>
          <input id="display-name" type="text" placeholder="Your DJ name">
        </div>
        <div class="settings-field">
          <label for="handle" data-i18n="handle_label">Profile handle</label>
          <input id="handle" type="text" placeholder="@handle">
        </div>
        <div class="settings-actions">
          <button type="button" data-i18n="update_profile">Update profile</button>
        </div>
      </section>

      <section class="settings-panel danger-panel" data-settings-panel="danger">
        <h2 data-i18n="danger_title">Danger zone</h2>
        <p data-i18n="danger_text">Deleting your account removes all playlists, favorites, and saved sessions. This action cannot be undone.</p>
        <div class="settings-field">
          <label for="confirm-name" data-i18n="confirm_username">Type your username to confirm</label>
          <input id="confirm-name" type="text" placeholder="username">
        </div>
        <div class="settings-actions">
          <button type="button" data-i18n="delete_account">Delete account</button>
        </div>
      </section>

      <section class="settings-panel" data-settings-panel="logout">
        <h2 data-i18n="logout_title">Sign out</h2>
        <p data-i18n="logout_text">End this session on the current device.</p>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <div class="settings-actions">
            <button type="submit" data-i18n="logout_btn">Sign out</button>
          </div>
        </form>
      </section>
    </div>
  </div>
</div>
@endsection

@push('page-scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('[data-settings-tab]');
    const panels = document.querySelectorAll('[data-settings-panel]');
    const langSelect = document.getElementById('lang-select');

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.settingsTab;
        tabs.forEach((button) => button.classList.toggle('active', button === tab));
        panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.settingsPanel === target));
      });
    });

    if (langSelect) {
      const currentLang = localStorage.getItem('spotitube.lang') ?? 'en';
      langSelect.value = currentLang;
      langSelect.addEventListener('change', () => {
        const next = langSelect.value;
        if (window.spotitubeSetLanguage) {
          window.spotitubeSetLanguage(next);
        } else {
          localStorage.setItem('spotitube.lang', next);
          window.location.reload();
        }
      });
    }
  });
</script>
@endpush
