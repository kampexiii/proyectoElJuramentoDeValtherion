const SETTINGS_KEY = 'valtherion.settings';
const DEFAULT_SETTINGS = {
    compact_mode: false,
    reduce_motion: false,
    sound_enabled: true,
};

export function loadSettings() {
    const raw = localStorage.getItem(SETTINGS_KEY);
    if (!raw) {
        return { ...DEFAULT_SETTINGS };
    }

    try {
        const parsed = JSON.parse(raw);
        return { ...DEFAULT_SETTINGS, ...parsed };
    } catch (_) {
        return { ...DEFAULT_SETTINGS };
    }
}

export function saveSettings(settings) {
    const payload = { ...DEFAULT_SETTINGS, ...settings };
    localStorage.setItem(SETTINGS_KEY, JSON.stringify(payload));
    return payload;
}

export function applySettingsToBody(settings) {
    const body = document.body;
    if (!body) {
        return;
    }

    body.classList.toggle('vth-compact', !!settings.compact_mode);
    body.classList.toggle('vth-reduce-motion', !!settings.reduce_motion);
    body.classList.toggle('vth-sound-off', settings.sound_enabled === false);
}

export function bindSettingsForm(settings) {
    const form = document.getElementById('game-settings-form');
    if (!form) {
        return;
    }

    const compact = document.getElementById('compact_mode');
    const reduceMotion = document.getElementById('reduce_motion');
    const sound = document.getElementById('sound_enabled');
    const status = document.getElementById('settings-status');

    if (compact) {
        compact.checked = !!settings.compact_mode;
    }
    if (reduceMotion) {
        reduceMotion.checked = !!settings.reduce_motion;
    }
    if (sound) {
        sound.checked = settings.sound_enabled !== false;
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const next = {
            compact_mode: !!compact?.checked,
            reduce_motion: !!reduceMotion?.checked,
            sound_enabled: sound ? !!sound.checked : true,
        };

        const saved = saveSettings(next);
        applySettingsToBody(saved);

        if (status) {
            status.classList.remove('d-none');
            const prevTimer = status.dataset.timerId;
            if (prevTimer) {
                window.clearTimeout(Number(prevTimer));
            }
            const timerId = window.setTimeout(() => {
                status.classList.add('d-none');
                status.dataset.timerId = '';
            }, 2000);
            status.dataset.timerId = String(timerId);
        }
    });
}

function initSettings() {
    const settings = loadSettings();
    applySettingsToBody(settings);
    bindSettingsForm(settings);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSettings);
} else {
    initSettings();
}
