{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Auth · ' . config('app.name', 'SpotiTube'))</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600" rel="stylesheet" />
  <style>
    :root {
      font-family: 'Space Grotesk', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: 15px;
      background: #010104;
      color: #f5f5f7;
    }

    :root[data-theme="light"] {
      background: #f8fbff;
      color: #0b0b16;
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at top, rgba(77, 137, 255, 0.25), transparent 58%), #02030c;
      color: inherit;
      padding: 2rem 1rem;
    }

    body.light {
      background: radial-gradient(circle at top, rgba(77, 137, 255, 0.2), transparent 60%), #f2f3ff;
      color: #0b0b17;
    }

    .auth-shell {
      width: min(440px, calc(100vw - 32px));
      padding: 2.5rem 2.8rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 1.25rem;
      background: rgba(5, 8, 22, 0.85);
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    body.light .auth-shell {
      background: rgba(255, 255, 255, 0.95);
      border-color: rgba(10, 13, 38, 0.15);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .auth-section {
      display: flex;
      flex-direction: column;
    }

    .auth-title {
      text-align: center;
      letter-spacing: 0.35em;
      text-transform: uppercase;
      margin: 0;
      font-size: 1.75rem;
      font-weight: 600;
      background: linear-gradient(135deg, #fff 0%, #a8c0ff 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    body.light .auth-title {
      background: linear-gradient(135deg, #0a0f2a 0%, #4a6cf7 100%);
      -webkit-background-clip: text;
      background-clip: text;
    }

    .auth-subtitle {
      text-align: center;
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.9rem;
      margin: 0.75rem 0 0 0;
      letter-spacing: 0.02em;
    }

    body.light .auth-subtitle {
      color: rgba(10, 15, 42, 0.6);
    }

    .auth-form {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
      margin-top: 2rem;
    }

    .auth-field {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .auth-label {
      letter-spacing: 0.15em;
      font-size: 0.75rem;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.7);
      font-weight: 500;
    }

    body.light .auth-label {
      color: rgba(10, 15, 42, 0.7);
    }

    .auth-input {
      border: none;
      border-bottom: 1.5px solid rgba(255, 255, 255, 0.2);
      padding: 0.75rem 0;
      background: transparent;
      color: inherit;
      font: inherit;
      font-size: 1rem;
      transition: border-color 0.2s ease;
    }

    .auth-input::placeholder {
      color: rgba(255, 255, 255, 0.35);
    }

    body.light .auth-input {
      border-bottom-color: rgba(8, 13, 36, 0.25);
    }

    body.light .auth-input::placeholder {
      color: rgba(10, 15, 42, 0.4);
    }

    .auth-input:focus {
      outline: none;
      border-bottom-color: #6b8cff;
    }

    body.light .auth-input:focus {
      border-bottom-color: #4a6cf7;
    }

    .auth-checkbox {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-top: 0.25rem;
    }

    .auth-checkbox input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: #6b8cff;
      cursor: pointer;
    }

    .auth-checkbox label {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
      letter-spacing: 0.02em;
    }

    body.light .auth-checkbox label {
      color: rgba(10, 15, 42, 0.7);
    }

    .auth-button {
      border: none;
      padding: 1rem 1.5rem;
      border-radius: 0.6rem;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all 0.2s ease;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
    }

    .auth-button-primary {
      background: linear-gradient(135deg, #6b8cff 0%, #4a6cf7 100%);
      color: #fff;
      margin-top: 0.5rem;
      box-shadow: 0 4px 15px rgba(74, 108, 247, 0.3);
    }

    .auth-button-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
    }

    .auth-divider {
      display: flex;
      align-items: center;
      margin: 1.5rem 0;
      gap: 1rem;
    }

    .auth-divider::before,
    .auth-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(255, 255, 255, 0.15);
    }

    body.light .auth-divider::before,
    body.light .auth-divider::after {
      background: rgba(10, 15, 42, 0.15);
    }

    .auth-divider span {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    body.light .auth-divider span {
      color: rgba(10, 15, 42, 0.5);
    }

    .auth-button-google {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #fff;
    }

    .auth-button-google:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.25);
    }

    body.light .auth-button-google {
      background: rgba(10, 15, 42, 0.03);
      border-color: rgba(10, 15, 42, 0.15);
      color: #0a0f2a;
    }

    body.light .auth-button-google:hover {
      background: rgba(10, 15, 42, 0.06);
      border-color: rgba(10, 15, 42, 0.25);
    }

    .auth-footer {
      text-align: center;
      margin: 1.75rem 0 0 0;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.6);
    }

    body.light .auth-footer {
      color: rgba(10, 15, 42, 0.6);
    }

    .auth-footer a {
      color: #6b8cff;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .auth-footer a:hover {
      color: #8ba4ff;
    }

    body.light .auth-footer a {
      color: #4a6cf7;
    }

    body.light .auth-footer a:hover {
      color: #3a5ce7;
    }

    .auth-alert {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      padding: 0.9rem 1.2rem;
      border-radius: 0.6rem;
      font-size: 0.85rem;
      color: #fca5a5;
      margin-top: 1.5rem;
    }

    body.light .auth-alert {
      background: rgba(239, 68, 68, 0.08);
      border-color: rgba(239, 68, 68, 0.25);
      color: #dc2626;
    }

    .theme-toggle-floating {
      position: fixed;
      top: 1.25rem;
      right: 1.25rem;
      width: 44px;
      height: 44px;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(5, 8, 22, 0.6);
      color: inherit;
      cursor: pointer;
      transition: all 0.2s ease;
      font-size: 1.2rem;
    }

    .theme-toggle-floating:hover {
      background: rgba(5, 8, 22, 0.8);
      border-color: rgba(255, 255, 255, 0.3);
    }

    body.light .theme-toggle-floating {
      border-color: rgba(10, 13, 38, 0.15);
      background: rgba(255, 255, 255, 0.8);
    }

    body.light .theme-toggle-floating:hover {
      background: rgba(255, 255, 255, 0.95);
      border-color: rgba(10, 13, 38, 0.25);
    }

    @media (max-width: 480px) {
      .auth-shell {
        padding: 2rem 1.75rem;
      }

      .auth-title {
        font-size: 1.5rem;
      }
    }
  </style>
  @stack('auth-styles')
</head>

<body>
  <button type="button" class="theme-toggle-floating" id="authThemeToggle" aria-label="Toggle theme">◐</button>
  <main class="auth-shell">
    @yield('card')
  </main>
  <script>
    const applyTheme = (mode) => {
      if (mode === 'light') {
        document.body.classList.add('light');
        document.documentElement.dataset.theme = 'light';
      } else {
        document.body.classList.remove('light');
        document.documentElement.dataset.theme = 'dark';
      }
    };
    const loadTheme = () => localStorage.getItem('spotitube.auth.theme') ?? 'dark';
    const saveTheme = (mode) => localStorage.setItem('spotitube.auth.theme', mode);
    applyTheme(loadTheme());
    document.getElementById('authThemeToggle').addEventListener('click', () => {
      const next = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
      saveTheme(next);
      applyTheme(next);
    });
  </script>
  @stack('auth-scripts')
</body>

</html>
