{{--
    sync_status.blade.php
    ─────────────────────
    Kiosk offline / sync status banner.

    Include this partial inside the kiosk scan page with:
        @include('kiosk.sync_status')

    The component polls /kiosk/queue-status every 10 seconds and updates
    the displayed status badge automatically.  No page reload needed.
--}}
<div id="syncStatusBanner" style="
    display: none;
    position: fixed;
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    min-width: 280px;
    max-width: 420px;
    padding: 12px 20px;
    border-radius: 12px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    transition: background 0.4s, border-color 0.4s;
">
    <span id="syncStatusIcon" style="margin-right: 8px;"></span>
    <span id="syncStatusText"></span>
    <span id="syncPendingBadge" style="
        display: none;
        margin-left: 10px;
        background: rgba(255,255,255,0.25);
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
    "></span>
</div>

<script>
(function () {
    'use strict';

    const banner      = document.getElementById('syncStatusBanner');
    const iconEl      = document.getElementById('syncStatusIcon');
    const textEl      = document.getElementById('syncStatusText');
    const pendingEl   = document.getElementById('syncPendingBadge');
    const POLL_MS     = 10_000;

    const STATES = {
        offline:  { icon: '📴', text: 'Offline Mode — scans are queued locally', bg: 'rgba(239,68,68,0.85)',  border: '#f87171' },
        syncing:  { icon: '🔄', text: 'Syncing to server…',                       bg: 'rgba(234,179,8,0.85)', border: '#fde047' },
        synced:   { icon: '✅', text: 'All synced',                                bg: 'rgba(16,185,129,0.7)', border: '#6ee7b7' },
        failed:   { icon: '⚠️', text: 'Sync failed — check server logs',          bg: 'rgba(239,68,68,0.85)', border: '#f87171' },
    };

    let lastStatus = null;
    let hideSyncedTimer = null;

    function applyState(key, pendingCount, failedCount) {
        const s = STATES[key] || STATES.offline;

        // Avoid unnecessary DOM flicker
        if (key === lastStatus && key === 'synced') return;
        lastStatus = key;

        clearTimeout(hideSyncedTimer);

        banner.style.display    = 'block';
        banner.style.background = s.bg;
        banner.style.border     = '1.5px solid ' + s.border;
        banner.style.color      = '#fff';
        iconEl.textContent      = s.icon;
        textEl.textContent      = s.text;

        if (pendingCount > 0) {
            pendingEl.style.display = 'inline';
            pendingEl.textContent   = pendingCount + ' pending';
        } else {
            pendingEl.style.display = 'none';
        }

        if (failedCount > 0) {
            iconEl.textContent = '⚠️';
            textEl.textContent = failedCount + ' job(s) failed — check logs';
        }

        // Auto-hide the "all synced" banner after 5 s
        if (key === 'synced' && failedCount === 0) {
            hideSyncedTimer = setTimeout(() => {
                banner.style.display = 'none';
                lastStatus = null;
            }, 5000);
        }
    }

    function poll() {
        fetch('/kiosk/queue-status', { credentials: 'same-origin' })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                const pending = data.pending_count || 0;
                const failed  = data.failed_count  || 0;
                let   key     = data.status || 'synced';   // offline | syncing | synced

                if (failed > 0) key = 'failed';
                applyState(key, pending, failed);
            })
            .catch(function () {
                // Cannot reach local server at all — show offline banner
                applyState('offline', 0, 0);
            });
    }

    // First poll on load, then repeat
    poll();
    setInterval(poll, POLL_MS);
})();
</script>
