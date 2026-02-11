import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

console.log('🔌 Echo Config:', {
    broadcaster: 'reverb',
    key: (import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY),
    wsHost: (import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST),
    wsPort: (import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT),
    wssPort: (import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT),
    forceTLS: ((import.meta.env.VITE_REVERB_SCHEME || import.meta.env.VITE_PUSHER_SCHEME) ?? 'https') === 'https',
    scheme: (import.meta.env.VITE_REVERB_SCHEME || import.meta.env.VITE_PUSHER_SCHEME)
});

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: (import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY),
    wsHost: (import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST),
    wsPort: (import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT) ?? 80,
    wssPort: (import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT) ?? 443,
    forceTLS: ((import.meta.env.VITE_REVERB_SCHEME || import.meta.env.VITE_PUSHER_SCHEME) ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
