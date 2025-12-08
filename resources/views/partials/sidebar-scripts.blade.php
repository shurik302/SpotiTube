<script>
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const resizer = document.getElementById('sidebarResizer');
  const themeToggle = document.getElementById('themeToggle');
  const accountButton = document.getElementById('accountButton');
  const accountMenu = document.getElementById('accountMenu');
  const logoutBtn = document.getElementById('logoutBtn');

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
      admin: 'Admin',
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
      settings_title: 'Settings',
      settings_subtitle: 'Fine tune how SpotiTube looks, feels, and secures your account.',
      tab_twofa: 'Two-factor auth',
      tab_theme: 'Theme & appearance',
      tab_profile: 'Profile',
      tab_danger: 'Danger zone',
      tab_logout: 'Sign out',
      twofa_title: 'Two-factor authentication',
      twofa_text: 'Require a secondary code every time you sign in. Use an authenticator app or SMS.',
      twofa_method: 'Method',
      twofa_app: 'Authenticator app',
      twofa_sms: 'SMS code',
      twofa_backup: 'Generate backup codes',
      twofa_enable: 'Enable 2FA',
      theme_title: 'Theme & appearance',
      theme_text: 'Pick a default theme and accent color for your library.',
      theme_label: 'Theme',
      theme_system: 'Follow system',
      theme_dark: 'Dark',
      theme_light: 'Light',
      accent_label: 'Accent color',
      save_appearance: 'Save appearance',
      lang_label: 'Language',
      lang_en: 'English',
      lang_uk: 'Ukrainian',
      profile_title: 'Profile settings',
      display_name: 'Display name',
      handle_label: 'Profile handle',
      update_profile: 'Update profile',
      danger_title: 'Danger zone',
      danger_text: 'Deleting your account removes all playlists, favorites, and saved sessions. This action cannot be undone.',
      confirm_username: 'Type your username to confirm',
      delete_account: 'Delete account',
      logout_title: 'Sign out',
      logout_text: 'End this session on the current device.',
      logout_btn: 'Sign out',
      // Browse / playlists
      browse_title: 'BROWSE',
      search: 'Search',
      all_genres: 'All genres',
      tab_tracks: 'Tracks',
      tab_artists: 'Artists',
      tab_albums: 'Albums',
      tab_genres: 'Genres',
      th_number: '#',
      th_title: 'Title',
      th_artist: 'Artist',
      th_album: 'Album',
      th_genre: 'Genre',
      th_time: 'Time',
      playlists_title: 'PLAYLISTS',
      create_btn: 'CREATE',
      // Home sections
      queue_snapshot: 'Queue snapshot',
      stay_inspired: 'Stay inspired',
      community_favorites: 'Community favorites',
      top_rated_artists: 'Top Rated Artists',
      listener_approved: 'Listener approved',
      top_albums: 'Top Albums',
    },
    uk: {
      dashboard: 'Панель',
      browse: 'Перегляд',
      playlists: 'Плейлисти',
      live_radio: 'Живе радіо',
      admin: 'Адмінка',
      settings: 'Налаштування',
      theme_toggle: 'Змінити тему',
      sign_in: 'Увійти',
      sign_out: 'Вийти',
      dashboard_label: 'Панель',
      welcome_title: 'Поверніться до своїх міксів',
      welcome_text: 'Слідкуйте за останніми сесіями, плейлистами та артистами, не втрачаючи контекст під час дослідження.',
      manage_queue: 'Керувати чергою',
      recently_played: 'Нещодавно програвали',
      view_all: 'Переглянути все',
      recommended: 'Рекомендовано',
      settings_title: 'Налаштування',
      settings_subtitle: 'Тонке налаштування вигляду, зручності та безпеки акаунта.',
      tab_twofa: 'Двофакторка',
      tab_theme: 'Тема та вигляд',
      tab_profile: 'Профіль',
      tab_danger: 'Небезпечна зона',
      tab_logout: 'Вийти',
      twofa_title: 'Двофакторна автентифікація',
      twofa_text: 'Вимагати додатковий код при вході. Використовуйте додаток-автентифікатор або SMS.',
      twofa_method: 'Метод',
      twofa_app: 'Додаток-автентифікатор',
      twofa_sms: 'SMS-код',
      twofa_backup: 'Згенерувати резервні коди',
      twofa_enable: 'Увімкнути 2FA',
      theme_title: 'Тема та вигляд',
      theme_text: 'Оберіть тему та акцентний колір для бібліотеки.',
      theme_label: 'Тема',
      theme_system: 'Як у системі',
      theme_dark: 'Темна',
      theme_light: 'Світла',
      accent_label: 'Акцентний колір',
      save_appearance: 'Зберегти вигляд',
      lang_label: 'Мова',
      lang_en: 'Англійська',
      lang_uk: 'Українська',
      profile_title: 'Налаштування профілю',
      display_name: "Показуване ім'я",
      handle_label: 'Хендл профілю',
      update_profile: 'Оновити профіль',
      danger_title: 'Небезпечна зона',
      danger_text: 'Видалення акаунта прибирає всі плейлисти, вподобання й сесії. Дію не можна скасувати.',
      confirm_username: "Введіть ім'я користувача для підтвердження",
      delete_account: 'Видалити акаунт',
      logout_title: 'Вийти',
      logout_text: 'Завершити цю сесію на поточному пристрої.',
      logout_btn: 'Вийти',
      browse_title: 'ПЕРЕГЛЯД',
      search: 'Пошук',
      all_genres: 'Усі жанри',
      tab_tracks: 'Треки',
      tab_artists: 'Артисти',
      tab_albums: 'Альбоми',
      tab_genres: 'Жанри',
      th_number: '№',
      th_title: 'Назва',
      th_artist: 'Виконавець',
      th_album: 'Альбом',
      th_genre: 'Жанр',
      th_time: 'Час',
      playlists_title: 'ПЛЕЙЛИСТИ',
      create_btn: 'СТВОРИТИ',
      queue_snapshot: 'Знімок черги',
      stay_inspired: 'Натхнення поруч',
      community_favorites: 'Вибір спільноти',
      top_rated_artists: 'Топ артисти',
      listener_approved: 'Слухачі схвалили',
      top_albums: 'Топ альбоми',
    },
  };

  const textNodes = document.querySelectorAll('[data-i18n]');
  const placeholderNodes = document.querySelectorAll('[data-i18n-placeholder]');
  const loadLang = () => localStorage.getItem('spotitube.lang') ?? 'en';
  const saveLang = (lang) => localStorage.setItem('spotitube.lang', lang);
  const applyLanguage = (lang) => {
    const dict = translations[lang] ?? translations.en;
    textNodes.forEach((node) => {
      const key = node.dataset.i18n;
      if (!key) return;
      const translation = dict[key];
      if (translation) {
        node.textContent = translation;
      }
    });
    placeholderNodes.forEach((node) => {
      const key = node.dataset.i18nPlaceholder;
      if (!key) return;
      const translation = dict[key];
      if (translation) {
        node.setAttribute('placeholder', translation);
      }
    });
    if (accountButton && accountButton.dataset.auth === 'guest') {
      const label = accountButton.querySelector('.menu-label');
      if (label) {
        label.textContent = dict.sign_in;
      }
    }
    if (logoutBtn) {
      logoutBtn.textContent = dict.sign_out;
    }
  };
  applyLanguage(loadLang());

  window.spotitubeSetLanguage = (lang) => {
    saveLang(lang);
    applyLanguage(lang);
  };

  accountButton?.addEventListener('click', () => {
    if (accountButton?.dataset.auth === 'guest') {
      const loginUrl = accountButton.dataset.loginUrl;
      if (loginUrl) {
        window.location.href = loginUrl;
      }
      return;
    }
    if (!accountMenu) return;
    const visible = accountMenu.style.display === 'block';
    accountMenu.style.display = visible ? 'none' : 'block';
  });
  document.addEventListener('click', (event) => {
    if (!accountMenu || event.target === accountButton) {
      return;
    }
    if (!accountMenu.contains(event.target)) {
      accountMenu.style.display = 'none';
    }
  });
  updateToggleGlyph();
</script>
