<div> {{-- Audio Element (Hidden) --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>

    {{-- Data Channel (Hidden Element untuk passing data ke JS) --}}
    <div id="notification-channels-data" data-channels="{{ json_encode($channels) }}" style="display: none;"></div>

    {{-- Script JavaScript --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            initLoginNotification();
        });

        // Fallback untuk load pertama kali (hard refresh)
        document.addEventListener('DOMContentLoaded', () => {
            initLoginNotification();
        });

        function initLoginNotification() {
            // Ambil elemen data
            const dataEl = document.getElementById('notification-channels-data');
            if (!dataEl) return;

            // Parse data channel dari atribut data-channels
            let activeChannels = [];
            try {
                const rawData = dataEl.getAttribute('data-channels');
                if (rawData) {
                    activeChannels = JSON.parse(rawData);
                }
            } catch (e) {
                console.error("Gagal memparsing channel notifikasi:", e);
                return;
            }

            const audioEl = document.getElementById('loginSound');

            // Cek apakah Echo sudah terload
            if (typeof window.Echo === 'undefined') {
                console.warn('Laravel Echo belum siap/terload.');
                return;
            }

            if (activeChannels.length > 0) {
                console.log('Mendengarkan Login Channel:', activeChannels);

                activeChannels.forEach((channelName) => {
                    // Unsubscribe dulu untuk mencegah double listener
                    window.Echo.leave(channelName);

                    // Subscribe ulang
                    window.Echo.private(channelName)
                        .listen('.user.logged.in', (e) => {
                            console.log('🔔 NOTIFIKASI LOGIN:', e);

                            // 1. Play Audio
                            if (audioEl) {
                                audioEl.currentTime = 0;
                                audioEl.play().catch(err => {
                                    console.log('Autoplay audio diblokir browser (interaksi user diperlukan).');
                                });
                            }

                            // 2. Tampilkan SweetAlert
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'User Login Terdeteksi!',
                                    html: `
                                        <div class="d-flex align-items-center gap-3 text-start">
                                            <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold fs-5">${e.user_name}</div>
                                                <div class="text-muted small text-uppercase">${e.user_role}</div>
                                                <div class="text-primary small fw-bold"><i class="fas fa-map-marker-alt me-1"></i> ${e.location}</div>
                                            </div>
                                        </div>
                                    `,
                                    position: 'top-end',
                                    icon: 'info',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 5000,
                                    timerProgressBar: true,
                                    background: '#fff',
                                    customClass: {
                                        popup: 'shadow-lg border-start border-5 border-primary rounded-3'
                                    },
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal.resumeTimer;
                                    }
                                });
                            }
                        });
                });
            }
        }
    </script>

</div> 