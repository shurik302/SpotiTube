{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name', 'SpotiTube'))</title>
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
      padding-bottom: 0;
      transition: padding-bottom 200ms ease;
    }

    body.player-visible {
      padding-bottom: 0;
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
      text-decoration: none;
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
      margin-bottom: 0;
    }

    body.player-visible .sidebar-bottom {
      margin-bottom: 1.5rem;
      padding-bottom: 5rem;
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
      padding: 2.4rem 3rem 4.5rem;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    body.player-visible .main {
      padding-bottom: 7.5rem;
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
      padding-right: 2rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      position: relative;
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
      flex-shrink: 0;
      margin-right: 0.5rem;
    }

    @media (max-width: 1024px) {
      .main {
        padding: 1.8rem 1.8rem 6rem;
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
        padding: 1.5rem 1.5rem 5.5rem;
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

    body.light .eyebrow {
      color: rgba(16, 23, 42, 0.6) !important;
    }

    body.light .section-intro {
      color: rgba(16, 23, 42, 0.74) !important;
    }

    body.light .page-header h1 {
      color: #0d142b !important;
    }

    body.light .section-header h2 {
      color: #0d142b !important;
    }

    body.light .page-header {
      color: #0d142b;
    }

    body.light .content-section {
      color: #0d142b;
    }

    .player-bar {
      position: fixed;
      inset: auto 0 0 0;
      padding: 0.9rem 2.5rem;
      background: rgba(3, 5, 12, 0.95);
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      display: flex;
      align-items: center;
      gap: 1.5rem;
      transform: translateY(100%);
      opacity: 0;
      transition: transform 200ms ease, opacity 200ms ease;
      z-index: 5;
      border-radius: 1rem 1rem 0 0;
      cursor: pointer;
    }

    .player-bar:not(.visible) {
      cursor: default;
    }

    .player-bar.visible {
      transform: translateY(0);
      opacity: 1;
    }

    .player-track-info {
      display: flex;
      align-items: center;
      gap: 0.9rem;
      min-width: 0;
    }

    .player-track-info img {
      width: 54px;
      height: 54px;
      border-radius: 0.7rem;
      object-fit: cover;
      flex-shrink: 0;
    }

    .player-track-meta {
      display: flex;
      flex-direction: column;
      gap: 0.2rem;
    }

    .player-track-meta strong {
      font-size: 0.95rem;
    }

    .player-track-meta span {
      font-size: 0.74rem;
      letter-spacing: 0.2em;
      color: rgba(255, 255, 255, 0.65);
    }

    .player-controls {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.7rem;
      flex: 1;
    }

    .player-buttons {
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }

    .player-buttons button {
      border: none;
      background: rgba(255, 255, 255, 0.08);
      color: inherit;
      border-radius: 0.4rem;
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .player-buttons button[data-player-toggle] {
      width: 42px;
      height: 42px;
      background: #fff;
      color: #050914;
    }

    .player-progress {
      display: grid;
      grid-template-columns: auto 1fr auto;
      align-items: center;
      gap: 0.6rem;
      width: 100%;
    }

    .player-progress span {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.6);
    }

    .player-progress-bar {
      background: rgba(255, 255, 255, 0.15);
      height: 6px;
      border-radius: 999px;
      overflow: visible;
      cursor: pointer;
      position: relative;
    }

    .player-progress-fill {
      width: 0%;
      height: 100%;
      background: #fff;
      border-radius: 999px;
      position: relative;
    }

    .player-progress-bar:hover .player-progress-fill::after {
      content: '';
      position: absolute;
      right: -6px;
      top: 50%;
      transform: translateY(-50%);
      width: 12px;
      height: 12px;
      background: #fff;
      border-radius: 50%;
      box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
    }

    .player-volume {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      min-width: 160px;
    }

    .player-volume-bar {
      flex: 1;
      height: 6px;
      background: rgba(255, 255, 255, 0.18);
      position: relative;
      cursor: pointer;
      border-radius: 999px;
      overflow: visible;
    }

    .player-volume-fill {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 60%;
      background: #fff;
      transition: width 0.1s ease;
      border-radius: 999px;
    }

    .player-volume-bar:hover .player-volume-fill::after {
      content: '';
      position: absolute;
      right: -6px;
      top: 50%;
      transform: translateY(-50%);
      width: 12px;
      height: 12px;
      background: #fff;
      border-radius: 50%;
      box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
    }

    .player-volume-icon {
      border: none;
      background: transparent;
      color: inherit;
      cursor: pointer;
      font-size: 1rem;
      padding: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .player-progress-bar {
      cursor: pointer;
    }

    .player-fullscreen-btn {
      border: none;
      background: rgba(255, 255, 255, 0.08);
      color: inherit;
      border-radius: 0.4rem;
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1rem;
    }

    .player-fullscreen-overlay {
      display: none;
      position: fixed;
      top: 0 !important;
      left: 0 !important;
      right: 0 !important;
      bottom: 0 !important;
      width: 100vw !important;
      height: 100vh !important;
      max-width: 100vw !important;
      max-height: 100vh !important;
      background: #000;
      z-index: 99999;
      flex-direction: column;
      padding: 2rem;
      gap: 2rem;
      overflow: hidden;
      margin: 0 !important;
      box-sizing: border-box;
    }

    .player-fullscreen-close {
      position: absolute;
      top: 1.5rem;
      right: 1.5rem;
      border: none;
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1.5rem;
      z-index: 10001;
    }

    .player-fullscreen-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      width: 100%;
    }

    .player-fullscreen-cover-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2rem;
      max-width: 600px;
    }

    .player-fullscreen-cover {
      width: min(60vh, 500px);
      height: min(60vh, 500px);
      object-fit: cover;
      border-radius: 1rem;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .player-fullscreen-track-info {
      text-align: center;
      color: #fff;
    }

    .player-fullscreen-track-info h2 {
      font-size: 2.5rem;
      margin: 0 0 0.5rem 0;
      font-weight: 600;
    }

    .player-fullscreen-track-info p {
      font-size: 1.2rem;
      margin: 0;
      color: rgba(255, 255, 255, 0.7);
      letter-spacing: 0.1em;
    }

    .player-fullscreen-sidebar {
      position: absolute;
      top: 5.5rem;
      right: 0.5rem;
      width: 350px;
      max-height: calc(100vh - 14rem);
      background: rgba(255, 255, 255, 0.05);
      border-radius: 1rem;
      padding: 1.5rem 1.75rem 1.5rem 1.5rem; /* extra right padding for scrollbar clearance */
      transition: background 0.3s ease, padding 0.3s ease;
      z-index: 10;
    }

    .player-fullscreen-sidebar.sidebar-closed {
      background: transparent;
      padding: 0;
    }

    .player-sidebar-toggle {
      position: absolute;
      top: -2.9rem; /* tighter under close button */
      right: -1rem; /* move further left so it's clearly outside */
      border: none;
      background: rgba(0,0,0,0.65);
      color: #fff;
      border-radius: 0.4rem;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1.5rem;
      z-index: 10000;
      transition: background 0.2s ease;
    }

    .player-sidebar-toggle:hover {
      background: rgba(255, 255, 255, 0.14);
    }

    body.light .player-sidebar-toggle {
      background: rgba(0,0,0,0.12);
    }

    body.light .player-sidebar-toggle:hover {
      background: rgba(0, 0, 0, 0.18);
    }

    .player-sidebar-content h3 {
      color: #fff;
      font-size: 1.2rem;
      margin: 0 0 1rem 0;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    .player-recommendations-list {
      list-style: none;
      padding: 0;
      margin: 0;
      padding-right: 1.1rem; /* add space for scrollbar and toggle */
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      max-height: 60vh;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
    }

    .player-recommendations-list::-webkit-scrollbar {
      width: 6px;
    }

    .player-recommendations-list::-webkit-scrollbar-track {
      background: transparent;
    }

    .player-recommendations-list::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 3px;
    }

    .player-recommendations-list::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.5);
    }

    .player-recommendation-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .player-recommendation-item:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .player-recommendation-item img {
      width: 50px;
      height: 50px;
      border-radius: 0.5rem;
      object-fit: cover;
    }

    .player-recommendation-item div {
      flex: 1;
      min-width: 0;
    }

    .player-recommendation-item strong {
      display: block;
      color: #fff;
      font-size: 0.95rem;
      margin-bottom: 0.25rem;
    }

    .player-recommendation-item span {
      display: block;
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.8rem;
      letter-spacing: 0.1em;
    }

    .player-fullscreen-controls {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      max-width: 800px;
      margin: 0 auto;
      width: 100%;
    }

    .player-fullscreen-controls .player-controls-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 2rem;
      position: relative;
    }

    .player-fullscreen-controls .player-buttons {
      justify-content: center;
      display: flex;
      gap: 1rem;
      flex: 0 0 auto;
    }

    .player-fullscreen-controls .player-buttons button {
      width: 50px;
      height: 50px;
      font-size: 1.2rem;
    }

    .player-fullscreen-controls .player-buttons button[data-player-fullscreen-toggle] {
      width: 60px;
      height: 60px;
      background: #fff;
      color: #050914;
    }

    .player-fullscreen-controls .player-volume {
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .player-fullscreen-controls .player-volume-icon {
      font-size: 0.9rem;
      width: 20px;
      height: 20px;
      padding: 0;
      flex-shrink: 0;
    }

    .player-fullscreen-controls .player-volume-bar {
      width: 80px;
      height: 3px;
    }

    @media (max-width: 1024px) {
      .player-fullscreen-sidebar {
        width: 300px;
        right: 1rem;
        top: 3.5rem;
      }
    }

    @media (max-width: 768px) {
      .player-fullscreen-sidebar {
        width: 280px;
        right: 0.5rem;
        top: 3rem;
      }
    }

    .player-heart {
      border: none;
      background: transparent;
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
      font-size: 1rem;
    }

    .player-close {
      border: none;
      background: rgba(255, 255, 255, 0.08);
      color: inherit;
      border-radius: 999px;
      width: 28px;
      height: 28px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      margin-left: auto;
    }

    .player-layout-end {
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    body.light .player-bar {
      background: rgba(245, 247, 255, 0.97);
      border-top-color: rgba(4, 9, 28, 0.08);
      color: #050914;
    }

    body.light .player-track-meta span,
    body.light .player-progress span {
      color: rgba(5, 9, 20, 0.55);
    }

    body.light .player-buttons button:not([data-player-toggle]) {
      background: rgba(4, 9, 28, 0.08);
    }

    body.light .player-progress-bar {
      background: rgba(4, 9, 28, 0.15);
    }

    body.light .player-progress-fill,
    body.light .player-volume-fill {
      background: rgba(5, 9, 20, 0.9);
    }

    body.light .player-volume-bar {
      background: rgba(4, 9, 28, 0.18);
    }

    body.light .player-fullscreen-btn {
      background: rgba(4, 9, 28, 0.08);
      color: #0d142b;
    }

    body.light .player-fullscreen-overlay {
      background: #f5f7ff;
    }

    body.light .player-fullscreen-close {
      background: rgba(4, 9, 28, 0.1);
      color: #0d142b;
    }

    body.light .player-fullscreen-track-info h2 {
      color: #0d142b;
    }

    body.light .player-fullscreen-track-info p {
      color: rgba(16, 23, 42, 0.7);
    }

    body.light .player-fullscreen-sidebar {
      background: rgba(255, 255, 255, 0.9);
    }

    body.light .player-sidebar-toggle {
      background: rgba(4, 9, 28, 0.1);
      color: #0d142b;
    }

    body.light .player-sidebar-content h3 {
      color: #0d142b;
    }

    body.light .player-recommendation-item:hover {
      background: rgba(4, 9, 28, 0.08);
    }

    body.light .player-recommendation-item strong {
      color: #0d142b;
    }

    body.light .player-recommendation-item span {
      color: rgba(16, 23, 42, 0.6);
    }

    body.light .player-fullscreen-controls .player-buttons button[data-player-fullscreen-toggle] {
      background: #0d142b;
      color: #fff;
    }
  </style>
  @stack('page-styles')
</head>

@php($sidebarActive = trim($__env->yieldContent('sidebar_active')) ?: 'dashboard')

<body>
  <div class="layout">
    @include('partials.sidebar', ['active' => $sidebarActive])
    <main class="main">
      @yield('content')
    </main>
  </div>
  @include('partials.player')
  @include('partials.sidebar-scripts')
  @stack('page-scripts')
</body>

</html>
