import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: reverbHost,
    wsPort: reverbPort,
    wssPort: reverbPort,
    forceTLS: reverbScheme === 'https',
    enabledTransports: ['ws', 'wss'],
});
