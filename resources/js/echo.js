import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Determine Reverb configuration dynamically based on runtime environment
const isHttps = window.location.protocol === 'https:';
const hostname = window.location.hostname;
const isLocal = hostname === 'localhost' || hostname === '127.0.0.1' || hostname === 'gp' || hostname.endsWith('.test');

// In production HTTPS, connect through standard secure 443 / WSS via reverse proxy
// In local dev, connect directly to local Reverb port (e.g. 8080)
const metaKey = document.querySelector('meta[name="reverb-key"]')?.getAttribute('content');
const metaHost = document.querySelector('meta[name="reverb-host"]')?.getAttribute('content');
const metaPort = document.querySelector('meta[name="reverb-port"]')?.getAttribute('content');
const metaScheme = document.querySelector('meta[name="reverb-scheme"]')?.getAttribute('content');

const key = metaKey || import.meta.env.VITE_REVERB_APP_KEY || 'apaupvivxik4sfka6dkg';
const host = metaHost || (isLocal ? (import.meta.env.VITE_REVERB_HOST || hostname) : hostname);
const scheme = metaScheme || (isHttps ? 'https' : (import.meta.env.VITE_REVERB_SCHEME || 'http'));

// Port: if HTTPS in production, default to standard HTTPS 443 port so no blocked non-standard ports are hit
let port = isHttps ? 443 : 80;
if (isLocal) {
    port = parseInt(metaPort || import.meta.env.VITE_REVERB_PORT || '8080', 10);
} else if (metaPort && metaPort !== '443' && metaPort !== '80' && !isHttps) {
    port = parseInt(metaPort, 10);
}

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: key,
    wsHost: host,
    wsPort: port,
    wssPort: isHttps ? 443 : port,
    forceTLS: scheme === 'https' || isHttps,
    enabledTransports: ['ws', 'wss'],
});
