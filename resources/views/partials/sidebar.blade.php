@php($activeTab = $active ?? 'dashboard')
@php($isAuthenticated = auth()->check())
@php($accountLabel = $isAuthenticated ? (auth()->user()->name ?? 'Account') : 'Sign in')
<aside id="sidebar" class="sidebar">
  <div class="sidebar-resizer" id="sidebarResizer"></div>
  <div class="brand-row">
    <span id="logo" class="logo" role="button" tabindex="0">&sim;</span>
    <button id="sidebarToggle" class="toggle-btn" aria-label="Toggle navigation">&lt;</button>
  </div>
  <nav class="tabs">
    <a class="tab {{ $activeTab === 'dashboard' ? 'active' : '' }}" href="{{ route('home') }}">
      <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M3 11l9-8 9 8v10a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1z" />
        </svg></span>
      <span class="tab-text" data-i18n="dashboard">Dashboard</span>
    </a>
    <a class="tab {{ $activeTab === 'browse' ? 'active' : '' }}" href="{{ route('browse') }}">
      <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
          <path
            d="M10 18a8 8 0 106.32-3.1l4.395 4.396-1.415 1.414-4.395-4.395A8 8 0 0010 18zm0-2a6 6 0 110-12 6 6 0 010 12z" />
        </svg></span>
      <span class="tab-text" data-i18n="browse">Browse</span>
    </a>
    <a class="tab {{ $activeTab === 'playlists' ? 'active' : '' }}" href="{{ route('playlists') }}">
      <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M6 5a2 2 0 00-2 2v11l6-3 6 3V7a2 2 0 00-2-2H6z" />
        </svg></span>
      <span class="tab-text" data-i18n="playlists">Playlists</span>
    </a>
    <a class="tab {{ $activeTab === 'live-radio' ? 'active' : '' }}" href="{{ route('live-radio') }}">
      <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M4 5h16v2H4zm0 4h16v6H4zm0 8h16v2H4z" />
        </svg></span>
      <span class="tab-text" data-i18n="live_radio">Live Radio</span>
    </a>
    <a class="tab {{ $activeTab === 'admin' ? 'active' : '' }}" href="{{ route('admin') }}">
      <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z" />
        </svg></span>
      <span class="tab-text" data-i18n="admin">Admin</span>
    </a>
  </nav>
  <div class="sidebar-bottom">
    <button id="themeToggle" class="theme-toggle" type="button">
      <span class="menu-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3a9 9 0 000 18A9 9 0 0112 3zm0 2v14a7 7 0 010-14z" />
        </svg></span>
      <span class="menu-label" data-i18n="theme_toggle">Toggle theme</span>
    </button>
    <a class="settings-btn" href="{{ route('settings') }}">
      <span class="menu-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
          <path
            d="M12 8a4 4 0 100 8 4 4 0 000-8zm9 3h-2.184a7.013 7.013 0 00-.516-1.285l1.547-1.547-1.414-1.414-1.547 1.547A7.013 7.013 0 0013 7.184V5h-2v2.184a7.013 7.013 0 00-1.285.516L8.168 6.153 6.754 7.567l1.547 1.547A7.013 7.013 0 007.184 11H5v2h2.184a7.013 7.013 0 00.516 1.285l-1.547 1.547 1.414 1.414 1.547-1.547A7.013 7.013 0 0011 18.816V21h2v-2.184a7.013 7.013 0 001.285-.516l1.547 1.547 1.414-1.414-1.547-1.547A7.013 7.013 0 0018.816 13H21z" />
        </svg></span>
      <span class="menu-label" data-i18n="settings">Settings</span>
    </a>
    @auth
    <div class="account-wrapper">
      <button class="account-btn" type="button" onclick="document.getElementById('accountMenu').classList.toggle('open')">
        <span class="menu-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z" />
          </svg></span>
        <span class="menu-label">{{ $accountLabel }}</span>
      </button>
      <div id="accountMenu" class="account-menu">
        <a href="{{ route('settings') }}">Account settings</a>
      </div>
    </div>
    @else
    <div class="account-wrapper">
      <a class="account-btn" href="{{ route('login') }}">
        <span class="menu-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z" />
          </svg></span>
        <span class="menu-label">Sign in</span>
      </a>
    </div>
    @endauth
  </div>
</aside>
