import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Aktifkan Log untuk Debugging
window.Pusher.logToConsole = true; 

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    // --- TAMBAHAN PENTING ---
    authEndpoint: '/broadcasting/auth', // Paksa jalur default Laravel
    auth: {
        headers: {
            // Ambil token dari meta tag di head master.blade.php
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
});