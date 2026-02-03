import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY ?? 'local';
const fallbackScheme = window.location.protocol === 'https:' ? 'https' : 'http';
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? fallbackScheme;
const reverbHost = import.meta.env.VITE_REVERB_HOST ?? window.location.hostname;
const reverbPort = Number(import.meta.env.VITE_REVERB_PORT)
    || (reverbScheme === 'https' ? 443 : 80);

// Conecta usando variables VITE_* para evitar localhost hardcodeado.
if (reverbHost) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
