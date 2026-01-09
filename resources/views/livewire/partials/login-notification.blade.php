<div>
    {{-- Elemen Audio & Data Wajib ada di dalam root div --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>
    <div id="notification-channels-data" data-channels="{{ json_encode($channels) }}" style="display: none;"></div>
</div>

@push('scripts')
<script>
    // Fungsi inisialisasi yang aman dipanggil berulang kali
    window.initLoginNotification = function() {
        const dataEl = document.getElementById('notification-channels-data');
        if (!dataEl) return;

        let activeChannels = [];
        try {
            const rawData = dataEl.getAttribute('data-channels');
            if (rawData) activeChannels = JSON.parse(rawData);
        } catch (e) {
            console.error("Gagal parse channel notifikasi:", e);
            return;
        }

        const audioEl = document.getElementById('loginSound');

        if (typeof window.Echo === 'undefined') {
            console.warn('Laravel Echo belum siap.');
            return;
        }

        if (activeChannels.length > 0) {
            console.log('Mendengarkan Channel:', activeChannels);

            activeChannels.forEach((channelName) => {
                window.Echo.leave(channelName); // Hindari duplikasi listener

                window.Echo.private(channelName)
                    .listen('.user.logged.in', (e) => {
                        console.log('🔔 NOTIFIKASI LOGIN:', e);

                        // Play Audio
                        if (audioEl) {
                            audioEl.currentTime = 0;
                            audioEl.play().catch(err => console.log('Autoplay blocked'));
                        }

                        // Tampilkan SweetAlert
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
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                        }
                    });
            });
        }
    };

    // Jalankan saat navigasi Livewire (SPA)
    document.addEventListener('livewire:navigated', window.initLoginNotification);
    
    // Jalankan saat load pertama kali
    document.addEventListener('DOMContentLoaded', window.initLoginNotification);
</script>
@endpush