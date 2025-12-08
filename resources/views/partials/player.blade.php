{{-- resources/views/partials/player.blade.php --}}
<div class="player-bar" data-player aria-hidden="true">
  <audio data-player-audio preload="metadata" hidden></audio>
  <div class="player-track-info">
    <img data-player-cover src="https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=200&q=80"
      alt="Current track cover" loading="lazy">
    <div class="player-track-meta">
      <strong data-player-title>Now Playing</strong>
      <span data-player-artist>Artist</span>
    </div>
    <button class="player-heart" type="button" aria-label="Toggle favorite">&#9825;</button>
  </div>
  <div class="player-controls">
    <div class="player-buttons">
      <button type="button" data-player-prev aria-label="Previous track">&#9198;</button>
      <button type="button" data-player-toggle aria-label="Play or pause">&#9654;</button>
      <button type="button" data-player-next aria-label="Next track">&#9197;</button>
    </div>
    <div class="player-progress player-seek-area">
      <span data-player-current>0:00</span>
      <div class="player-progress-bar" data-player-seek>
        <div class="player-progress-fill" data-player-progress></div>
      </div>
      <span data-player-duration>0:00</span>
    </div>
  </div>
  <div class="player-layout-end">
    <div class="player-volume player-volume-area">
      <button type="button" data-player-mute aria-label="Toggle mute" class="player-volume-icon">&#128266;</button>
      <div class="player-volume-bar" data-player-volume>
        <div class="player-volume-fill" data-player-volume-fill></div>
      </div>
    </div>
    <button class="player-close" type="button" data-player-close aria-label="Close player">&times;</button>
  </div>
</div>

<!-- Fullscreen overlay (outside player-bar) -->
<div class="player-fullscreen-overlay" data-player-fullscreen-overlay>
    <button class="player-fullscreen-close" type="button" data-player-fullscreen-close aria-label="Exit fullscreen">&times;</button>
    
    <div class="player-fullscreen-content">
      <div class="player-fullscreen-cover-wrapper">
        <img data-player-fullscreen-cover src="" alt="Album cover" class="player-fullscreen-cover">
        <div class="player-fullscreen-track-info">
          <h2 data-player-fullscreen-title>Track Title</h2>
          <p data-player-fullscreen-artist>Artist Name</p>
        </div>
      </div>

      <div class="player-fullscreen-sidebar" data-player-sidebar>
        <button class="player-sidebar-toggle" type="button" data-player-sidebar-toggle aria-label="Toggle sidebar">☰</button>
        <div class="player-sidebar-content" data-player-sidebar-content>
          <h3>Recommended</h3>
          <ul class="player-recommendations-list" data-player-recommendations></ul>
        </div>
      </div>
    </div>

    <div class="player-fullscreen-controls">
      <div class="player-controls-row">
        <div class="player-buttons">
          <button type="button" data-player-fullscreen-prev aria-label="Previous track">&#9198;</button>
          <button type="button" data-player-fullscreen-toggle aria-label="Play or pause">&#9654;</button>
          <button type="button" data-player-fullscreen-next aria-label="Next track">&#9197;</button>
        </div>
        <div class="player-volume player-volume-area">
          <button type="button" data-player-fullscreen-mute aria-label="Toggle mute" class="player-volume-icon">&#128266;</button>
          <div class="player-volume-bar" data-player-fullscreen-volume>
            <div class="player-volume-fill" data-player-fullscreen-volume-fill></div>
          </div>
        </div>
      </div>
      <div class="player-progress player-seek-area">
        <span data-player-fullscreen-current>0:00</span>
        <div class="player-progress-bar" data-player-fullscreen-seek>
          <div class="player-progress-fill" data-player-fullscreen-progress></div>
        </div>
        <span data-player-fullscreen-duration>0:00</span>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const player = document.querySelector('[data-player]');
    if (!player) {
      return;
    }

    const audioEl = player.querySelector('[data-player-audio]') || new Audio();
    audioEl.autoplay = true;
    const pageRoot = document.body;
    pageRoot.classList.remove('player-visible');
    const coverEl = player.querySelector('[data-player-cover]');
    const titleEl = player.querySelector('[data-player-title]');
    const artistEl = player.querySelector('[data-player-artist]');
    const currentEl = player.querySelector('[data-player-current]');
    const durationEl = player.querySelector('[data-player-duration]');
    const progressEl = player.querySelector('[data-player-progress]');
    const progressBar = player.querySelector('[data-player-seek]');
    const toggleBtn = player.querySelector('[data-player-toggle]');
    const prevBtn = player.querySelector('[data-player-prev]');
    const nextBtn = player.querySelector('[data-player-next]');
    const closeBtn = player.querySelector('[data-player-close]');
    const volumeBar = player.querySelector('[data-player-volume]');
    const volumeFill = player.querySelector('[data-player-volume-fill]');
    const muteBtn = player.querySelector('[data-player-mute]');

    let isPlaying = false;
    let hasTrack = false;
    let isMuted = false;
    let savedVolume = 1.0;
    let currentTrackData = null;
    const PLAYER_STATE_KEY = 'spotitube.player.state';
    let trackQueue = [];
    let currentTrackIndex = -1;
    audioEl.volume = savedVolume;
    coverEl.dataset.fallback = coverEl.src;

    const icons = {
      play: '\u25B6',
      pause: '\u23F8',
    };

    const updateToggle = () => {
      toggleBtn.textContent = isPlaying ? icons.pause : icons.play;
      if (fullscreenToggleBtn) {
        fullscreenToggleBtn.textContent = isPlaying ? icons.pause : icons.play;
      }
    };

    const formatTime = (value) => {
      if (Number.isNaN(value) || !Number.isFinite(value)) {
        return '0:00';
      }
      const minutes = Math.floor(value / 60);
      const seconds = Math.floor(value % 60);
      return `${minutes}:${seconds.toString().padStart(2, '0')}`;
    };

    const syncProgress = () => {
      if (!hasTrack) {
        return;
      }
      currentEl.textContent = formatTime(audioEl.currentTime);
      if (audioEl.duration > 0) {
        const progress = (audioEl.currentTime / audioEl.duration) * 100;
        progressEl.style.width = `${progress}%`;
      } else {
        progressEl.style.width = '0%';
      }
    };

    const setCurrentTimeSafe = (nextTime) => {
      if (!hasTrack) return;
      if (Number.isNaN(nextTime) || !Number.isFinite(nextTime) || nextTime < 0) return;

      const applyTime = () => {
        const clamped = Math.min(nextTime, audioEl.duration || nextTime);
        const oldSrc = audioEl.src;
        try {
          if (typeof audioEl.fastSeek === 'function') {
            audioEl.fastSeek(clamped);
          } else {
            audioEl.currentTime = clamped;
          }
          // Ensure src not changed unexpectedly
          if (audioEl.src !== oldSrc) {
            audioEl.src = oldSrc;
            audioEl.currentTime = clamped;
          }
        } catch (err) {
          console.warn('Seek error:', err);
          audioEl.currentTime = clamped;
        }
        syncProgress();
        if (isFullscreen) {
          updateFullscreenProgress();
        }
      };

      if (!audioEl.duration || audioEl.readyState < 1) {
        audioEl.addEventListener('loadedmetadata', applyTime, { once: true });
      } else {
        applyTime();
      }
    };

    const updateVolume = (volume) => {
      audioEl.volume = volume;
      savedVolume = volume;
      if (volumeFill) volumeFill.style.width = `${volume * 100}%`;
      if (fullscreenVolumeFill) fullscreenVolumeFill.style.width = `${volume * 100}%`;
      if (volume === 0) {
        isMuted = true;
        if (muteBtn) muteBtn.textContent = '\u{1F507}'; // 🔇
        if (fullscreenMuteBtn) fullscreenMuteBtn.textContent = '\u{1F507}';
      } else {
        isMuted = false;
        if (muteBtn) muteBtn.textContent = '\u{1F50A}'; // 🔊
        if (fullscreenMuteBtn) fullscreenMuteBtn.textContent = '\u{1F50A}';
      }
    };

    const toggleMute = () => {
      if (isMuted) {
        updateVolume(savedVolume > 0 ? savedVolume : 0.5);
      } else {
        savedVolume = audioEl.volume;
        updateVolume(0);
      }
      // Update mute button icons
      if (muteBtn) {
        muteBtn.textContent = isMuted ? '\u{1F507}' : '\u{1F50A}';
      }
      if (fullscreenMuteBtn) {
        fullscreenMuteBtn.textContent = isMuted ? '\u{1F507}' : '\u{1F50A}';
      }
    };

    const hidePlayer = () => {
      player.classList.remove('visible');
      player.setAttribute('aria-hidden', 'true');
      pageRoot.classList.remove('player-visible');
      audioEl.pause();
      audioEl.removeAttribute('src');
      audioEl.load();
      hasTrack = false;
      isPlaying = false;
      progressEl.style.width = '0%';
      currentEl.textContent = '0:00';
      durationEl.textContent = '0:00';
      updateToggle();
      try {
        localStorage.removeItem(PLAYER_STATE_KEY);
      } catch (err) {
        console.warn('Failed to clear player state', err);
      }
    };

    const persistState = () => {
      if (!currentTrackData || !hasTrack) return;
      try {
        localStorage.setItem(
          PLAYER_STATE_KEY,
          JSON.stringify({
            ...currentTrackData,
            position: audioEl.currentTime || 0,
            wasPlaying: !audioEl.paused,
          })
        );
      } catch (err) {
        console.warn('Persist player state failed', err);
      }
    };

    const restoreState = () => {
      try {
        const raw = localStorage.getItem(PLAYER_STATE_KEY);
        if (!raw) return;
        const state = JSON.parse(raw);
        if (!state.trackAudio) return;
        showPlayer(state, true);
        const seekTo = Number(state.position) || 0;
        const playAfter = !!state.wasPlaying;
        const apply = () => {
          if (seekTo > 0 && audioEl.duration && seekTo < audioEl.duration) {
            audioEl.currentTime = seekTo;
          }
          if (playAfter) {
            audioEl.play().catch(() => {});
            isPlaying = true;
            updateToggle();
          } else {
            isPlaying = false;
            updateToggle();
          }
        };
        if (audioEl.readyState >= 1) {
          apply();
        } else {
          audioEl.addEventListener('loadedmetadata', () => apply(), { once: true });
          audioEl.load();
        }
      } catch (err) {
        console.warn('Restore player state failed', err);
      }
    };

    const showPlayer = ({ trackTitle, trackArtist, trackCover, trackLength, trackAudio }, skipReset = false) => {
      if (!trackAudio) {
        return;
      }
      const safeTitle = trackTitle || 'Unknown track';
      const safeArtist = trackArtist || 'Unknown artist';
      coverEl.src = trackCover || coverEl.dataset.fallback;
      coverEl.alt = `${safeTitle} cover`;
      titleEl.textContent = safeTitle;
      artistEl.textContent = safeArtist.toUpperCase();
      
      // Only reset progress if we're loading a new track
      if (!skipReset) {
        durationEl.textContent = trackLength || '0:00';
        currentEl.textContent = '0:00';
        progressEl.style.width = '0%';
      }

      // Store current track data
      currentTrackData = { trackTitle: safeTitle, trackArtist: safeArtist, trackCover: coverEl.src, trackLength, trackAudio };
      
      // Build queue from all playable tracks on page
      buildTrackQueue();

      player.classList.add('visible');
      player.removeAttribute('aria-hidden');
      pageRoot.classList.add('player-visible');
      hasTrack = true;
      
      // Only change src if it's different to avoid reloading
      const currentSrc = audioEl.src || '';
      const newSrc = trackAudio;
      // Normalize URLs for comparison (remove trailing slashes, query params, etc.)
      const normalizeUrl = (url) => {
        if (!url) return '';
        try {
          const urlObj = new URL(url, window.location.origin);
          return urlObj.pathname;
        } catch {
          return url.split('?')[0].split('#')[0];
        }
      };
      
      if (normalizeUrl(currentSrc) !== normalizeUrl(newSrc)) {
        audioEl.src = trackAudio;
      }
      // Always try to play after selection
      audioEl.play()
        .then(() => {
          isPlaying = true;
          updateToggle();
          persistState();
        })
        .catch(() => {
          isPlaying = false;
          updateToggle();
          persistState();
        });
    };

    const buildTrackQueue = () => {
      trackQueue = [];
      const playableTracks = document.querySelectorAll('[data-track-play]');
      playableTracks.forEach((track, index) => {
        const data = {
          trackTitle: track.dataset.trackTitle,
          trackArtist: track.dataset.trackArtist,
          trackCover: track.dataset.trackCover,
          trackLength: track.dataset.trackLength,
          trackAudio: track.dataset.trackAudio,
        };
        trackQueue.push(data);
        if (data.trackAudio === currentTrackData?.trackAudio) {
          currentTrackIndex = index;
        }
      });
    };

    const playNextTrack = () => {
      if (trackQueue.length === 0) return;
      currentTrackIndex = (currentTrackIndex + 1) % trackQueue.length;
      showPlayer(trackQueue[currentTrackIndex]);
    };

    const playPreviousTrack = () => {
      if (trackQueue.length === 0) return;
      currentTrackIndex = currentTrackIndex <= 0 ? trackQueue.length - 1 : currentTrackIndex - 1;
      showPlayer(trackQueue[currentTrackIndex]);
    };

    document.addEventListener('click', (event) => {
      // Don't trigger if we're currently seeking
      if (isSeeking || isFullscreenSeeking) {
        return;
      }
      const trigger = event.target.closest('[data-track-play]');
      if (!trigger) {
        return;
      }
      event.preventDefault();
      showPlayer(trigger.dataset);
      persistState();
    });

    toggleBtn.addEventListener('click', () => {
      if (!hasTrack) {
        return;
      }
      if (audioEl.paused) {
        audioEl.play().then(() => {
          isPlaying = true;
          updateToggle();
        });
      } else {
        audioEl.pause();
        isPlaying = false;
        updateToggle();
      }
    });

    audioEl.addEventListener('loadedmetadata', () => {
      durationEl.textContent = formatTime(audioEl.duration);
      if (fullscreenDurationEl) {
        fullscreenDurationEl.textContent = formatTime(audioEl.duration);
      }
    });

    audioEl.addEventListener('timeupdate', syncProgress);
    
    // Prevent audio from resetting when seeking
    audioEl.addEventListener('seeked', () => {
      // Ensure currentTime is preserved
      if (isSeeking || isFullscreenSeeking) {
        // Don't reset, just update display
        syncProgress();
        if (isFullscreen) {
          updateFullscreenProgress();
        }
      }
    });

    audioEl.addEventListener('ended', () => {
      hidePlayer();
    });

    audioEl.addEventListener('pause', () => {
      isPlaying = false;
      updateToggle();
      persistState();
    });

    audioEl.addEventListener('play', () => {
      isPlaying = true;
      updateToggle();
      persistState();
    });

    closeBtn?.addEventListener('click', hidePlayer);

    // Previous/Next track buttons
    prevBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      playPreviousTrack();
    });

    nextBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      playNextTrack();
    });

    // Seek functionality (pause on drag, resume on drop, clamp safely)
    let isSeeking = false;
    let wasPlayingBeforeSeek = false;

    const getTimeFromBar = (barEl, clientX) => {
      if (!barEl || !audioEl.duration || Number.isNaN(audioEl.duration)) return null;
      const rect = barEl.getBoundingClientRect();
      const pct = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
      return pct * audioEl.duration;
    };

    const beginSeek = (barEl, clientX, fullscreen = false) => {
      if (!hasTrack || !audioEl.duration) return;
      const newTime = getTimeFromBar(barEl, clientX);
      if (newTime === null) return;
      if (fullscreen) {
        isFullscreenSeeking = true;
        wasPlayingBeforeFullscreenSeek = !audioEl.paused;
      } else {
        isSeeking = true;
        wasPlayingBeforeSeek = !audioEl.paused;
      }
      if (!audioEl.paused) {
        audioEl.pause();
      }
      setCurrentTimeSafe(newTime);
    };

    const moveSeek = (barEl, clientX) => {
      if (!hasTrack || !audioEl.duration) return;
      const newTime = getTimeFromBar(barEl, clientX);
      if (newTime === null) return;
      setCurrentTimeSafe(newTime);
    };

    const endSeek = (wasPlayingFlag) => {
      if (wasPlayingFlag && hasTrack) {
        audioEl.play().catch(() => {});
      }
    };

    progressBar?.addEventListener('mousedown', (e) => {
      e.stopPropagation();
      e.preventDefault();
      beginSeek(progressBar, e.clientX, false);
    });

    document.addEventListener('mousemove', (e) => {
      if (!isSeeking || !hasTrack) return;
      e.stopPropagation();
      e.preventDefault();
      moveSeek(progressBar, e.clientX);
    });

    document.addEventListener('mouseup', (e) => {
      if (isSeeking) {
        e.stopPropagation();
        e.preventDefault();
        endSeek(wasPlayingBeforeSeek);
        isSeeking = false;
        wasPlayingBeforeSeek = false;
      }
      if (isFullscreenSeeking) {
        e.stopPropagation();
        e.preventDefault();
        endSeek(wasPlayingBeforeFullscreenSeek);
        isFullscreenSeeking = false;
        wasPlayingBeforeFullscreenSeek = false;
      }
    });

    progressBar?.addEventListener('click', (e) => {
      if (!hasTrack || !audioEl.duration || Number.isNaN(audioEl.duration)) return;
      // Skip if this was part of a drag operation
      if (isSeeking) return;
      e.stopPropagation();
      e.preventDefault();
      const wasPlaying = !audioEl.paused;
      if (wasPlaying) {
        audioEl.pause();
      }
      const newTime = getTimeFromBar(progressBar, e.clientX);
      if (newTime !== null) {
        setCurrentTimeSafe(newTime);
      }
      if (wasPlaying) {
        setTimeout(() => {
          audioEl.play().catch(() => {});
        }, 40);
      }
    });

    // Volume control with drag support
    let isVolumeDragging = false;
    volumeBar?.addEventListener('mousedown', (e) => {
      e.stopPropagation();
      isVolumeDragging = true;
      const rect = volumeBar.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const percentage = Math.max(0, Math.min(1, clickX / rect.width));
      updateVolume(percentage);
    });

    document.addEventListener('mousemove', (e) => {
      if (!isVolumeDragging || !volumeBar) return;
      e.stopPropagation();
      const rect = volumeBar.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const percentage = Math.max(0, Math.min(1, clickX / rect.width));
      updateVolume(percentage);
    });

    document.addEventListener('mouseup', () => {
      isVolumeDragging = false;
    });

    volumeBar?.addEventListener('click', (e) => {
      e.stopPropagation();
      const rect = volumeBar.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const percentage = Math.max(0, Math.min(1, clickX / rect.width));
      updateVolume(percentage);
    });

    // Mute toggle
    muteBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleMute();
    });

    // Spacebar for play/pause
    document.addEventListener('keydown', (e) => {
      if (e.code === 'Space' && hasTrack && !e.target.matches('input, textarea')) {
        e.preventDefault();
        if (audioEl.paused) {
          audioEl.play().then(() => {
            isPlaying = true;
            updateToggle();
            persistState();
          });
        } else {
          audioEl.pause();
          isPlaying = false;
          updateToggle();
          persistState();
        }
      }
    });

    // Fullscreen elements
    const fullscreenOverlay = document.querySelector('[data-player-fullscreen-overlay]');
    const fullscreenCloseBtn = fullscreenOverlay?.querySelector('[data-player-fullscreen-close]');
    const fullscreenPrevBtn = fullscreenOverlay?.querySelector('[data-player-fullscreen-prev]');
    const fullscreenNextBtn = fullscreenOverlay?.querySelector('[data-player-fullscreen-next]');
    const fullscreenCover = fullscreenOverlay?.querySelector('[data-player-fullscreen-cover]');
    const fullscreenTitle = fullscreenOverlay?.querySelector('[data-player-fullscreen-title]');
    const fullscreenArtist = fullscreenOverlay?.querySelector('[data-player-fullscreen-artist]');
    const fullscreenToggleBtn = fullscreenOverlay?.querySelector('[data-player-fullscreen-toggle]');
    const fullscreenCurrentEl = fullscreenOverlay?.querySelector('[data-player-fullscreen-current]');
    const fullscreenDurationEl = fullscreenOverlay?.querySelector('[data-player-fullscreen-duration]');
    const fullscreenProgressEl = fullscreenOverlay?.querySelector('[data-player-fullscreen-progress]');
    const fullscreenProgressBar = fullscreenOverlay?.querySelector('[data-player-fullscreen-seek]');
    const fullscreenVolumeBar = fullscreenOverlay?.querySelector('[data-player-fullscreen-volume]');
    const fullscreenVolumeFill = fullscreenOverlay?.querySelector('[data-player-fullscreen-volume-fill]');
    const fullscreenMuteBtn = fullscreenOverlay?.querySelector('[data-player-fullscreen-mute]');
    const sidebarToggle = fullscreenOverlay?.querySelector('[data-player-sidebar-toggle]');
    const sidebarContent = fullscreenOverlay?.querySelector('[data-player-sidebar-content]');
    const recommendationsList = fullscreenOverlay?.querySelector('[data-player-recommendations]');

    let isFullscreen = false;
    let sidebarOpen = true;

    const toggleFullscreen = () => {
      if (!hasTrack || !fullscreenOverlay) return;
      
      isFullscreen = !isFullscreen;
      
      if (isFullscreen) {
        fullscreenOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        if (fullscreenCover) fullscreenCover.src = coverEl.src;
        if (fullscreenTitle) fullscreenTitle.textContent = titleEl.textContent;
        if (fullscreenArtist) fullscreenArtist.textContent = artistEl.textContent;
        updateFullscreenProgress();
        loadRecommendations();
        // Sync play/pause button state
        updateToggle();
        // Keep player visible
        player.classList.add('visible');
        pageRoot.classList.add('player-visible');
      } else {
        fullscreenOverlay.style.display = 'none';
        document.body.style.overflow = '';
      }
    };

    const updateFullscreenProgress = () => {
      if (!hasTrack || !isFullscreen) return;
      if (fullscreenCurrentEl) fullscreenCurrentEl.textContent = formatTime(audioEl.currentTime);
      if (fullscreenDurationEl) fullscreenDurationEl.textContent = formatTime(audioEl.duration || 0);
      if (audioEl.duration > 0 && fullscreenProgressEl) {
        const progress = (audioEl.currentTime / audioEl.duration) * 100;
        fullscreenProgressEl.style.width = `${progress}%`;
      }
    };

    const loadRecommendations = async () => {
      if (!hasTrack || !recommendationsList) return;
      
      // Load random tracks
      try {
        const response = await fetch('/api/tracks/recommendations?limit=10');
        if (response.ok) {
          const tracks = await response.json();
          renderRecommendations(tracks);
        } else {
          renderRecommendations([]);
        }
      } catch (e) {
        renderRecommendations([]);
      }
    };

    const renderRecommendations = (tracks) => {
      if (!recommendationsList) return;
      
      if (!tracks || tracks.length === 0) {
        recommendationsList.innerHTML = '<li style="padding: 1rem; color: rgba(255,255,255,0.5);">No recommendations available</li>';
        return;
      }
      
      recommendationsList.innerHTML = tracks.map(track => `
        <li class="player-recommendation-item" data-track-play
          data-track-title="${(track.title || 'Unknown').replace(/"/g, '&quot;')}"
          data-track-artist="${(track.artist || 'Unknown').replace(/"/g, '&quot;')}"
          data-track-cover="${(track.cover || '').replace(/"/g, '&quot;')}"
          data-track-audio="${(track.audio || '').replace(/"/g, '&quot;')}"
          data-track-length="${track.length || '0:00'}">
          <img src="${track.cover || ''}" alt="${(track.title || '').replace(/"/g, '&quot;')}" loading="lazy">
          <div>
            <strong>${track.title || 'Unknown'}</strong>
            <span>${(track.artist || 'Unknown').toUpperCase()}</span>
          </div>
        </li>
      `).join('');
    };

    const toggleSidebar = () => {
      if (!sidebarContent || !fullscreenOverlay) return;
      sidebarOpen = !sidebarOpen;
      sidebarContent.style.display = sidebarOpen ? 'block' : 'none';
      
      // Update sidebar background based on state
      const sidebar = fullscreenOverlay.querySelector('[data-player-sidebar]');
      if (sidebar) {
        if (sidebarOpen) {
          sidebar.classList.remove('sidebar-closed');
        } else {
          sidebar.classList.add('sidebar-closed');
        }
      }
    };

    // Fullscreen close button
    fullscreenCloseBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleFullscreen();
      persistState();
    });

    // Fullscreen previous/next buttons
    fullscreenPrevBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      playPreviousTrack();
      persistState();
    });

    fullscreenNextBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      playNextTrack();
      persistState();
    });

    // Make entire player bar clickable for fullscreen (except buttons and interactive elements)
    player.addEventListener('click', (e) => {
      // Don't trigger if clicking on buttons, progress bar, volume, or close button
      // Also add padding around controls to prevent accidental fullscreen
      if (e.target.closest('button') || 
          e.target.closest('[data-player-seek]') || 
          e.target.closest('[data-player-volume]') ||
          e.target.closest('[data-player-close]') ||
          e.target.closest('.player-seek-area') ||
          e.target.closest('.player-volume-area')) {
        return;
      }
      
      if (hasTrack) {
        toggleFullscreen();
      }
    });

    // Fullscreen toggle button
    fullscreenToggleBtn?.addEventListener('click', () => {
      if (!hasTrack) return;
      if (audioEl.paused) {
        audioEl.play().then(() => {
          isPlaying = true;
          updateToggle();
          persistState();
        });
      } else {
        audioEl.pause();
        isPlaying = false;
        updateToggle();
        persistState();
      }
    });

    // Fullscreen seek (reuse shared helpers)
    let isFullscreenSeeking = false;
    let wasPlayingBeforeFullscreenSeek = false;
    
    fullscreenProgressBar?.addEventListener('mousedown', (e) => {
      e.stopPropagation();
      e.preventDefault();
      beginSeek(fullscreenProgressBar, e.clientX, true);
    });

    document.addEventListener('mousemove', (e) => {
      if (!isFullscreenSeeking || !hasTrack || !fullscreenProgressBar) return;
      e.stopPropagation();
      e.preventDefault();
      moveSeek(fullscreenProgressBar, e.clientX);
    });

    fullscreenProgressBar?.addEventListener('click', (e) => {
      if (!hasTrack || !audioEl.duration || Number.isNaN(audioEl.duration)) return;
      if (isFullscreenSeeking) return;
      e.stopPropagation();
      e.preventDefault();
      const wasPlaying = !audioEl.paused;
      if (wasPlaying) {
        audioEl.pause();
      }
      const newTime = getTimeFromBar(fullscreenProgressBar, e.clientX);
      if (newTime !== null) {
        setCurrentTimeSafe(newTime);
      }
      if (wasPlaying) {
        setTimeout(() => {
          audioEl.play().catch(() => {});
        }, 40);
      }
    });

    // Fullscreen volume control with drag support
    let isFullscreenVolumeDragging = false;
    fullscreenVolumeBar?.addEventListener('mousedown', (e) => {
      e.stopPropagation();
      e.preventDefault();
      isFullscreenVolumeDragging = true;
      const rect = fullscreenVolumeBar.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const percentage = Math.max(0, Math.min(1, clickX / rect.width));
      updateVolume(percentage);
      if (fullscreenVolumeFill) {
        fullscreenVolumeFill.style.width = `${percentage * 100}%`;
      }
    });

    document.addEventListener('mousemove', (e) => {
      if (!isFullscreenVolumeDragging || !fullscreenVolumeBar) return;
      e.stopPropagation();
      e.preventDefault();
      const rect = fullscreenVolumeBar.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const percentage = Math.max(0, Math.min(1, clickX / rect.width));
      updateVolume(percentage);
      if (fullscreenVolumeFill) {
        fullscreenVolumeFill.style.width = `${percentage * 100}%`;
      }
    });

    document.addEventListener('mouseup', (e) => {
      if (isFullscreenVolumeDragging) {
        e.stopPropagation();
      }
      isFullscreenVolumeDragging = false;
    });

    fullscreenVolumeBar?.addEventListener('click', (e) => {
      e.stopPropagation();
      e.preventDefault();
      const rect = fullscreenVolumeBar.getBoundingClientRect();
      const clickX = e.clientX - rect.left;
      const percentage = Math.max(0, Math.min(1, clickX / rect.width));
      updateVolume(percentage);
      if (fullscreenVolumeFill) {
        fullscreenVolumeFill.style.width = `${percentage * 100}%`;
      }
    });

    // Fullscreen mute button
    fullscreenMuteBtn?.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleMute();
    });

    // Sidebar toggle
    sidebarToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleSidebar();
    });

    // Update fullscreen progress on timeupdate
    audioEl.addEventListener('timeupdate', () => {
      syncProgress();
      if (isFullscreen) {
        updateFullscreenProgress();
      }
    });

    // Update fullscreen toggle button
    audioEl.addEventListener('play', () => {
      isPlaying = true;
      updateToggle();
    });

    audioEl.addEventListener('pause', () => {
      isPlaying = false;
      updateToggle();
    });

    // Handle clicks on recommendations in fullscreen
    recommendationsList?.addEventListener('click', (e) => {
      // Don't trigger if we're currently seeking
      if (isSeeking || isFullscreenSeeking) {
        return;
      }
      const item = e.target.closest('[data-track-play]');
      if (!item) return;
      
      const trackData = {
        trackTitle: item.dataset.trackTitle,
        trackArtist: item.dataset.trackArtist,
        trackCover: item.dataset.trackCover,
        trackAudio: item.dataset.trackAudio,
        trackLength: item.dataset.trackLength,
      };
      
      if (trackData.trackAudio) {
        showPlayer(trackData);
        // Update fullscreen display
        if (isFullscreen) {
          fullscreenCover.src = trackData.trackCover || coverEl.dataset.fallback;
          fullscreenTitle.textContent = trackData.trackTitle;
          fullscreenArtist.textContent = trackData.trackArtist.toUpperCase();
        }
      }
    });

    // Initialize volume display
    updateVolume(audioEl.volume);
    restoreState();
    window.addEventListener('beforeunload', persistState);
  });
</script>
