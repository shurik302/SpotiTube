{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'SpotiTube') }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
  <style>
    :root {
      font-family: 'Instrument Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
      color: #f4f4f5;
      background: #030719;
      font-size: 15.5px;
    }

    :root[data-theme="light"] {
      background: #eef4ff;
      color: #10172a;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      color: inherit;
      background: radial-gradient(circle at top left, rgba(70, 125, 255, 0.35), transparent 45%), #020514;
    }

    body.light {
      background: radial-gradient(circle at top left, rgba(120, 135, 255, 0.28), transparent 40%), #f5f7ff;
      color: #0d142b;
    }

    button {
      font: inherit;
    }

    .layout {
      display: flex;
      min-height: 100vh;
      align-items: stretch;
    }

    .sidebar {
      width: 212px;
      padding: 1.1rem 0.95rem;
      background: rgba(4, 8, 24, 0.94);
      border-right: 1px solid rgba(255, 255, 255, 0.05);
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      flex-shrink: 0;
      align-self: flex-start;
      transition: width 220ms ease;
    }

    .sidebar.collapsed {
      width: 74px;
    }

    .sidebar-resizer {
      position: absolute;
      top: 0;
      right: 0;
      width: 10px;
      height: 100%;
      cursor: ew-resize;
    }

    .brand-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.4rem;
      position: relative;
    }

    .logo {
      font-size: 1.2rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      user-select: none;
      border-radius: 0.85rem;
      background: rgba(255, 255, 255, 0.04);
      width: 38px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .toggle-btn {
      border: none;
      border-radius: 0.85rem;
      padding: 0.35rem;
      width: 38px;
      height: 38px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      background: rgba(255, 255, 255, 0.08);
      color: inherit;
      transition: background 120ms ease, opacity 120ms ease;
      position: relative;
    }

    .toggle-btn:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .sidebar.collapsed .toggle-btn {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      position: absolute;
      right: 0.3rem;
      top: 0;
    }

    .sidebar.collapsed .brand-row:hover .logo {
      opacity: 0;
    }

    .sidebar.collapsed .brand-row:hover .toggle-btn {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
    }

    .tabs {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
    }

    .tab {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.58rem 0.78rem;
      border-radius: 0.85rem;
      border: 1px solid transparent;
      background: rgba(255, 255, 255, 0.02);
      color: inherit;
      cursor: pointer;
      transition: background 150ms, border-color 150ms;
      font-size: 0.92rem;
    }

    .tab:hover {
      border-color: rgba(255, 255, 255, 0.08);
      background: rgba(255, 255, 255, 0.07);
    }

    .tab.active {
      border-color: rgba(74, 144, 255, 0.4);
      background: rgba(54, 106, 255, 0.2);
    }

    .icon-holder {
      width: 1.35rem;
      height: 1.35rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .tab-icon svg,
    .menu-icon svg {
      width: 1.05rem;
      height: 1.05rem;
      display: block;
    }

    .sidebar.collapsed .tab-text {
      display: none;
    }

    .sidebar:not(.collapsed) .tab-icon {
      display: none;
    }

    .sidebar-bottom {
      margin-top: auto;
      display: flex;
      flex-direction: column;
      gap: 0.45rem;
      padding-bottom: 0.5rem;
    }

    .theme-toggle,
    .settings-btn,
    .account-btn {
      border-radius: 0.85rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(255, 255, 255, 0.04);
      padding: 0.58rem 0.78rem;
      display: flex;
      gap: 0.45rem;
      align-items: center;
      color: inherit;
      cursor: pointer;
      width: 100%;
      justify-content: flex-start;
      font-size: 0.92rem;
    }

    .account-wrapper {
      position: relative;
    }

    .account-menu {
      position: absolute;
      bottom: calc(100% + 0.35rem);
      left: 0;
      width: 100%;
      background: rgba(12, 18, 38, 0.96);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 0.9rem;
      padding: 0.35rem 0;
      display: none;
      z-index: 2;
    }

    .account-menu button {
      width: 100%;
      border: none;
      background: none;
      padding: 0.5rem 0.9rem;
      text-align: left;
      color: inherit;
      cursor: pointer;
    }

    .account-menu button:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .menu-label {
      display: inline-flex;
    }

    .menu-icon {
      display: none;
    }

    .sidebar.collapsed .menu-label {
      display: none;
    }

    .sidebar.collapsed .menu-icon {
      display: inline-flex;
    }

    .main {
      flex: 1;
      padding: 2.4rem 3rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1.5rem;
      flex-wrap: wrap;
    }

    .page-header h1 {
      margin: 0 0 0.6rem 0;
      font-size: 2.4rem;
      font-weight: 600;
    }

    .eyebrow {
      text-transform: uppercase;
      letter-spacing: 0.18em;
      font-size: 0.76rem;
      color: rgba(255, 255, 255, 0.6);
      margin: 0 0 0.4rem 0;
    }

    .section-intro {
      margin: 0;
      color: rgba(255, 255, 255, 0.74);
      max-width: 540px;
      line-height: 1.5;
    }

    .content-section {
      display: flex;
      flex-direction: column;
      gap: 1.4rem;
    }

    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .section-header h2 {
      margin: 0;
      font-size: 1.35rem;
    }

    .outline-btn,
    .ghost-btn {
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: inherit;
      padding: 0.4rem 1.2rem;
      background: transparent;
      cursor: pointer;
      transition: border-color 140ms ease, background 140ms ease;
    }

    .outline-btn:hover,
    .ghost-btn:hover {
      border-color: rgba(255, 255, 255, 0.4);
      background: rgba(255, 255, 255, 0.05);
    }

    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: 1rem;
    }

    .media-card {
      border-radius: 1.2rem;
      border: 1px solid rgba(255, 255, 255, 0.04);
      background: rgba(5, 8, 18, 0.75);
      padding: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.7rem;
      min-height: 250px;
    }

    .media-thumb {
      border-radius: 1rem;
      overflow: hidden;
      aspect-ratio: 1 / 1;
      background: #050914;
      margin: 0;
    }

    .media-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .media-meta {
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
    }

    .media-meta strong {
      font-size: 1.05rem;
      font-weight: 600;
    }

    .media-meta span {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.88rem;
      letter-spacing: 0.06em;
    }

    .recommended-list {
      list-style: none;
      margin: 0;
      padding: 0;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .recommended-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 0.85rem 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }

    .recommended-left {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      min-width: 0;
    }

    .list-thumb {
      width: 52px;
      height: 52px;
      border-radius: 0.9rem;
      object-fit: cover;
      flex-shrink: 0;
    }

    .genre-tag {
      font-size: 0.78rem;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.7);
    }

    @media (max-width: 1024px) {
      .main {
        padding: 1.8rem;
      }
    }

    @media (max-width: 768px) {
      .layout {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      }

      .sidebar.collapsed {
        width: 100%;
      }

      .main {
        padding: 1.5rem;
      }
    }

    body.light .sidebar {
      background: rgba(245, 247, 255, 0.96);
      border-right: 1px solid rgba(4, 9, 28, 0.08);
      color: #10172a;
    }

    body.light .logo {
      background: rgba(4, 9, 28, 0.08);
    }

    body.light .toggle-btn {
      border: 1px solid rgba(4, 9, 28, 0.12);
      background: rgba(4, 9, 28, 0.05);
    }

    body.light .tab {
      background: rgba(4, 9, 28, 0.05);
      border-color: rgba(4, 9, 28, 0.05);
    }

    body.light .tab:hover {
      border-color: rgba(4, 9, 28, 0.12);
      background: rgba(4, 9, 28, 0.09);
    }

    body.light .tab.active {
      background: rgba(90, 120, 255, 0.18);
      border-color: rgba(90, 120, 255, 0.35);
    }

    body.light .theme-toggle,
    body.light .settings-btn,
    body.light .account-btn {
      border-color: rgba(4, 9, 28, 0.08);
      background: rgba(4, 9, 28, 0.04);
    }

    body.light .account-menu {
      background: rgba(255, 255, 255, 0.98);
      border-color: rgba(4, 9, 28, 0.12);
      color: #10172a;
    }

    body.light .account-menu button:hover {
      background: rgba(4, 9, 28, 0.08);
    }

    body.light .outline-btn,
    body.light .ghost-btn {
      border-color: rgba(4, 9, 28, 0.2);
    }

    body.light .outline-btn:hover,
    body.light .ghost-btn:hover {
      border-color: rgba(4, 9, 28, 0.4);
      background: rgba(4, 9, 28, 0.06);
    }

    body.light .media-card {
      background: rgba(255, 255, 255, 0.9);
      border-color: rgba(4, 9, 28, 0.08);
      color: #10172a;
    }

    body.light .media-meta span {
      color: rgba(16, 23, 42, 0.6);
    }

    body.light .recommended-list {
      border-top-color: rgba(4, 9, 28, 0.09);
    }

    body.light .recommended-item {
      border-bottom-color: rgba(4, 9, 28, 0.08);
    }

    body.light .genre-tag {
      color: rgba(16, 23, 42, 0.7);
    }
  </style>
</head>

<body>
  <div class="layout">
    <aside id="sidebar" class="sidebar">
      <div class="sidebar-resizer" id="sidebarResizer"></div>
      <div class="brand-row">
        <span id="logo" class="logo" role="button" tabindex="0">&sim;</span>
        <button id="sidebarToggle" class="toggle-btn" aria-label="Toggle navigation">&lt;</button>
      </div>
      <nav class="tabs">
        <button class="tab active">
          <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 11l9-8 9 8v10a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1z" />
            </svg></span>
          <span class="tab-text" data-i18n="dashboard">Dashboard</span>
        </button>
        <button class="tab">
          <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M10 18a8 8 0 106.32-3.1l4.395 4.396-1.415 1.414-4.395-4.395A8 8 0 0010 18zm0-2a6 6 0 110-12 6 6 0 010 12z" />
            </svg></span>
          <span class="tab-text" data-i18n="browse">Browse</span>
        </button>
        <button class="tab">
          <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M6 5a2 2 0 00-2 2v11l6-3 6 3V7a2 2 0 00-2-2H6z" />
            </svg></span>
          <span class="tab-text" data-i18n="playlists">Playlists</span>
        </button>
        <button class="tab">
          <span class="tab-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M4 5h16v2H4zm0 4h16v6H4zm0 8h16v2H4z" />
            </svg></span>
          <span class="tab-text" data-i18n="live_radio">Live Radio</span>
        </button>
      </nav>
      <div class="sidebar-bottom">
        <button id="themeToggle" class="theme-toggle">
          <span class="menu-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 3a9 9 0 000 18A9 9 0 0112 3zm0 2v14a7 7 0 010-14z" />
            </svg></span>
          <span class="menu-label" data-i18n="theme_toggle">Toggle theme</span>
        </button>
        <button class="settings-btn">
          <span class="menu-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M12 8a4 4 0 100 8 4 4 0 000-8zm9 3h-2.184a7.013 7.013 0 00-.516-1.285l1.547-1.547-1.414-1.414-1.547 1.547A7.013 7.013 0 0013 7.184V5h-2v2.184a7.013 7.013 0 00-1.285.516L8.168 6.153 6.754 7.567l1.547 1.547A7.013 7.013 0 007.184 11H5v2h2.184a7.013 7.013 0 00.516 1.285l-1.547 1.547 1.414 1.414 1.547-1.547A7.013 7.013 0 0011 18.816V21h2v-2.184a7.013 7.013 0 001.285-.516l1.547 1.547 1.414-1.414-1.547-1.547A7.013 7.013 0 0018.816 13H21z" />
            </svg></span>
          <span class="menu-label" data-i18n="settings">Settings</span>
        </button>
        <div class="account-wrapper">
          <button class="account-btn" id="accountButton" data-auth="guest">
            <span class="menu-icon icon-holder" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z" />
              </svg></span>
            <span class="menu-label">Sign in</span>
          </button>
          <div id="accountMenu" class="account-menu">
            <button id="logoutBtn">Sign out</button>
          </div>
        </div>
      </div>
    </aside>
    <main class="main">
      <header class="page-header">
        <div>
          <p class="eyebrow" data-i18n="dashboard_label">Dashboard</p>
          <h1 data-i18n="welcome_title">Jump back into your mixes</h1>
          <p class="section-intro" data-i18n="welcome_text">
            Keep an eye on your latest radio sessions, playlists, and artist drops without losing context while you
            explore.
          </p>
        </div>
        <button class="outline-btn" type="button" data-i18n="manage_queue">Manage queue</button>
      </header>
      <section aria-labelledby="recently-played-title" class="content-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Queue snapshot</p>
            <h2 id="recently-played-title" data-i18n="recently_played">Recently played</h2>
          </div>
          <button class="ghost-btn" type="button" data-i18n="view_all">View all</button>
        </div>
        <div class="card-grid">
          <article class="media-card">
            <figure class="media-thumb">
              <img src="https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=600&q=80"
                alt="DJ mixing console with moody lighting" loading="lazy">
            </figure>
            <div class="media-meta">
              <strong>Electric Dreams</strong>
              <span>THE ELECTRIC DREAMS</span>
            </div>
          </article>
          <article class="media-card">
            <figure class="media-thumb">
              <img src="https://images.unsplash.com/photo-1497032628192-86f99bcd76bc?auto=format&fit=crop&w=600&q=80"
                alt="Jazz trio in a dimly lit studio" loading="lazy">
            </figure>
            <div class="media-meta">
              <strong>Midnight Jazz</strong>
              <span>JAZZ COLLECTIVE</span>
            </div>
          </article>
          <article class="media-card">
            <figure class="media-thumb">
              <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=600&q=80"
                alt="Retro boombox portrait session" loading="lazy">
            </figure>
            <div class="media-meta">
              <strong>Thunder Strike</strong>
              <span>ROCK LEGENDS</span>
            </div>
          </article>
          <article class="media-card">
            <figure class="media-thumb">
              <img src="https://images.unsplash.com/photo-1487215078519-e21cc028cb29?auto=format&fit=crop&w=600&q=80"
                alt="Portrait of a smiling artist" loading="lazy">
            </figure>
            <div class="media-meta">
              <strong>Classical Movement</strong>
              <span>CLASSICAL ENSEMBLE</span>
            </div>
          </article>
        </div>
      </section>
      <section aria-labelledby="recommended-title" class="content-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Stay inspired</p>
            <h2 id="recommended-title" data-i18n="recommended">Recommended</h2>
          </div>
        </div>
        <ul class="recommended-list">
          <li class="recommended-item">
            <div class="recommended-left">
              <img class="list-thumb"
                src="https://images.unsplash.com/photo-1485579149621-3123dd979885?auto=format&fit=crop&w=200&q=80"
                alt="Vinyl turntable close-up" loading="lazy">
              <div class="media-meta">
                <strong>Electric Dreams</strong>
                <span>THE ELECTRIC DREAMS</span>
              </div>
            </div>
            <span class="genre-tag">Electronic</span>
          </li>
          <li class="recommended-item">
            <div class="recommended-left">
              <img class="list-thumb"
                src="https://images.unsplash.com/photo-1483412033650-1015ddeb83d1?auto=format&fit=crop&w=200&q=80"
                alt="Jazz musician performing live" loading="lazy">
              <div class="media-meta">
                <strong>Midnight Jazz</strong>
                <span>JAZZ COLLECTIVE</span>
              </div>
            </div>
            <span class="genre-tag">Jazz</span>
          </li>
          <li class="recommended-item">
            <div class="recommended-left">
              <img class="list-thumb"
                src="https://images.unsplash.com/photo-1464375117522-1311d6a5b81f?auto=format&fit=crop&w=200&q=80"
                alt="Rock guitarist on stage" loading="lazy">
              <div class="media-meta">
                <strong>Thunder Strike</strong>
                <span>ROCK LEGENDS</span>
              </div>
            </div>
            <span class="genre-tag">Rock</span>
          </li>
          <li class="recommended-item">
            <div class="recommended-left">
              <img class="list-thumb"
                src="https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=200&q=80"
                alt="Classical musician portrait" loading="lazy">
              <div class="media-meta">
                <strong>Classical Movement</strong>
                <span>CLASSICAL ENSEMBLE</span>
              </div>
            </div>
            <span class="genre-tag">Classical</span>
          </li>
        </ul>
      </section>
    </main>
  </div>
  <script>
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const resizer = document.getElementById('sidebarResizer');
    const themeToggle = document.getElementById('themeToggle');
    const accountButton = document.getElementById('accountButton');
    const accountMenu = document.getElementById('accountMenu');

    const updateToggleGlyph = () => {
      sidebarToggle.textContent = sidebar.classList.contains('collapsed') ? '>' : '<';
    };
    const toggleSidebar = () => {
      sidebar.classList.toggle('collapsed');
      updateToggleGlyph();
    };
    sidebarToggle.addEventListener('click', toggleSidebar);
    resizer.addEventListener('click', toggleSidebar);
    document.getElementById('logo').addEventListener('click', () => {
      if (!sidebar.classList.contains('collapsed')) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });

    const applyTheme = (mode) => {
      if (mode === 'light') {
        document.body.classList.add('light');
        document.documentElement.dataset.theme = 'light';
      } else {
        document.body.classList.remove('light');
        document.documentElement.dataset.theme = 'dark';
      }
    };
    const loadTheme = () => localStorage.getItem('spotitube.theme') ?? 'dark';
    const saveTheme = (mode) => localStorage.setItem('spotitube.theme', mode);
    applyTheme(loadTheme());
    themeToggle.addEventListener('click', () => {
      const next = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
      saveTheme(next);
      applyTheme(next);
    });

    const translations = {
      en: {
        dashboard: 'Dashboard',
        browse: 'Browse',
        playlists: 'Playlists',
        live_radio: 'Live Radio',
        settings: 'Settings',
        theme_toggle: 'Toggle theme',
        sign_in: 'Sign in',
        sign_out: 'Sign out',
        dashboard_label: 'Dashboard',
        welcome_title: 'Jump back into your mixes',
        welcome_text: 'Keep an eye on your latest radio sessions, playlists, and artist drops without losing context while you explore.',
        manage_queue: 'Manage queue',
        recently_played: 'Recently played',
        view_all: 'View all',
        recommended: 'Recommended',
      },
      uk: {
        dashboard: 'Панель',
        browse: 'Огляд',
        playlists: 'Плейлісти',
        live_radio: 'Онлайн-радіо',
        settings: 'Налаштування',
        theme_toggle: 'Змінити тему',
        sign_in: 'Увійти',
        sign_out: 'Вийти',
        dashboard_label: 'Панель',
        welcome_title: 'Повернення до ваших сетів',
        welcome_text: 'Слідкуйте за останніми сесіями, плейлістами та релізами артистів і швидко повертайтеся до відтворення.',
        manage_queue: 'Керуйте чергою',
        recently_played: 'Нещодавно прослухані',
        view_all: 'Усі треки',
        recommended: 'Рекомендовано',
      },
    };

    const textNodes = document.querySelectorAll('[data-i18n]');
    const loadLang = () => localStorage.getItem('spotitube.lang') ?? 'en';
    const applyLanguage = (lang) => {
      textNodes.forEach((node) => {
        const key = node.dataset.i18n;
        if (key === 'sign_in' || key === 'sign_out') {
          return;
        }
        const translation = translations[lang][key];
        if (translation) {
          node.textContent = translation;
        }
      });
      if (accountButton.dataset.auth === 'guest') {
        accountButton.querySelector('.menu-label').textContent = translations[lang].sign_in;
      }
      document.getElementById('logoutBtn').textContent = translations[lang].sign_out;
    };
    applyLanguage(loadLang());

    accountButton.addEventListener('click', () => {
      if (accountButton.dataset.auth === 'guest') {
        alert('Open auth modal here');
      } else {
        const visible = accountMenu.style.display === 'block';
        accountMenu.style.display = visible ? 'none' : 'block';
      }
    });
    document.addEventListener('click', (event) => {
      if (!accountMenu.contains(event.target) && event.target !== accountButton) {
        accountMenu.style.display = 'none';
      }
    });

    setTimeout(() => {
      accountButton.dataset.auth = 'user';
      accountButton.querySelector('.menu-label').textContent = 'Alex Stone';
      document.getElementById('logoutBtn').textContent = translations[loadLang()].sign_out;
    }, 1500);

    updateToggleGlyph();
  </script>
</body>

</html>
