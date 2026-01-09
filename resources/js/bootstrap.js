import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Cek apakah user terautentikasi
const isAuthenticated = document.querySelector('meta[name="user-id"]') !== null;

if (isAuthenticated) {
    window.axios = axios;
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    
    // Ambil CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const userId = document.querySelector('meta[name="user-id"]')?.content || '';
    
    window.Pusher = Pusher;
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        encrypted: true,
        wsHost: import.meta.env.VITE_PUSHER_HOST || `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
        wsPort: import.meta.env.VITE_PUSHER_PORT || 80,
        wssPort: import.meta.env.VITE_PUSHER_PORT || 443,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Socket-ID': socketId || '',
                'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || '')
            }
        }
    });
    
    // Log untuk debugging
    console.log('✅ Echo initialized for user:', userId);
} else {
    console.log('⏸️ Echo not initialized - user not authenticated');
}