<div> {{-- 1. Root Element Wajib --}}
    
    {{-- Audio Element --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto"></audio>

    {{-- Data Channel (Hidden) --}}
    <div id="notif-data" data-channels="{{ json_encode($channels) }}"></div>

    {{-- Script JavaScript --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            initializeNotification();
        });

        // Fallback untuk load pertama
        document.addEventListener('DOMContentLoaded', () => {
            initializeNotification();
        });

        function initializeNotification() {
            const dataEl = document.getElementById('notif-data');
            if (!dataEl) return;

            // Mencegah parsing error jika data kosong
            let channels = [];
            try {
                channels = JSON.parse(dataEl.getAttribute('data-channels'));
            } catch (e) {
                console.error("Gagal parse channel notifikasi", e);
                return;
            }

            const audioEl = document.getElementById('loginSound');

            if (!channels || channels.length === 0) return;

            // Cek Echo
            if (typeof window.Echo === 'undefined') {
                console.error('Laravel Echo belum siap.');
                return;
            }

            console.log('Listening Channels:', channels);

            channels.forEach(channelName => {
                // Unsubscribe dulu
                window.Echo.leave(channelName);

                window.Echo.private(channelName)
                    .listen('.user.logged.in', (e) => {
                        console.log('🔔 NOTIF MASUK:', e);

                        // 1. Play Audio
                        if (audioEl) {
                            audioEl.currentTime = 0;
                            audioEl.play().catch(err => console.log('Audio autoplay blocked'));
                        }

                        // 2. SweetAlert
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
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                    });
            });
        }
    </script>

</div> {{-- Penutup Root Element --}}