<div>
    {{-- Audio Element --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>

    {{-- Data Channel --}}
    <div id="notification-channels-data" data-channels="{{ json_encode($channels) }}" style="display: none;"></div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            initLoginNotification();
        });

        document.addEventListener('DOMContentLoaded', () => {
            initLoginNotification();
        });

        function initLoginNotification() {
            // 1. Ambil Data Channel
            const dataEl = document.getElementById('notification-channels-data');
            if (!dataEl) return;

            let activeChannels = [];
            try {
                const rawData = dataEl.getAttribute('data-channels');
                if (rawData) activeChannels = JSON.parse(rawData);
            } catch (e) { return; }

            if (!activeChannels || activeChannels.length === 0) return;

            // 2. Cek Pusher/Echo
            if (typeof window.Echo === 'undefined') {
                console.error('❌ Laravel Echo Error: Echo tidak terload. Cek npm run build.');
                return;
            }

            // 3. AKTIFKAN DEBUG LOGGING (PENTING BUAT CEK)
            if (typeof window.Pusher !== 'undefined') {
                window.Pusher.logToConsole = true; // <-- Ini akan memunculkan log Pusher di Console
            }

            console.log('📡 System Ready. Listening on:', activeChannels);

            const audioEl = document.getElementById('loginSound');

            // 4. Loop Channel & Subscribe
            activeChannels.forEach((channelName) => {
                window.Echo.leave(channelName); // Reset dulu

                window.Echo.private(channelName)
                    .listen('.login-event', (e) => { // <-- Perhatikan titik di depan (.login-event)
                        console.log('🚀 EVENT DITERIMA DARI SERVER:', e);

                        // Mainkan Suara
                        if (audioEl) {
                            audioEl.currentTime = 0;
                            audioEl.play().catch(err => console.warn('Audio blocked:', err));
                        }

                        // Tampilkan Notif
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'User Login Terdeteksi!',
                                html: `
                                    <div class="d-flex align-items-center gap-3 text-start">
                                        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                                            <i class="fas fa-user-check fa-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">${e.user_name}</div>
                                            <div class="badge bg-dark text-white mb-1">${e.user_role}</div>
                                            <div class="text-secondary small fw-bold">
                                                <i class="fas fa-map-marker-alt me-1 text-danger"></i> ${e.location}
                                            </div>
                                        </div>
                                    </div>
                                `,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 6000,
                                timerProgressBar: true,
                                background: '#fff',
                                customClass: {
                                    popup: 'shadow-lg border-start border-5 border-dark rounded-4'
                                },
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                        }
                    })
                    .error((error) => {
                        console.error('❌ Gagal Subscribe Channel:', channelName, error);
                    });
            });
        }
    </script>
</div>