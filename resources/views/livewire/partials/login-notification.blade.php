<div>
    {{-- Audio Element (Hidden) --}}
    {{-- Ganti URL CDN Pixabay karena error 403 Forbidden, gunakan asset lokal atau URL lain --}}
    {{-- Jika pakai asset lokal, pastikan file ada di public/audio/notif.mp3 --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto"></audio>

    @if(count($channels) > 0)
        @script
        <script>
            // Ambil data channel
            let activeChannels = @json($channels);
            let audioEl = document.getElementById('loginSound');

            // Cek apakah Echo sudah ada
            if (typeof window.Echo === 'undefined') {
                console.error('Laravel Echo BELUM dimuat. Cek bootstrap.js & app.js');
            } else {
                console.log('Mendengarkan channel:', activeChannels);

                activeChannels.forEach((channelName) => {
                    window.Echo.private(channelName)
                        .listen('.user.logged.in', (e) => {
                            console.log('🔔 NOTIFIKASI MASUK:', e);

                            // 1. Play Audio (User Interaction Policy mungkin memblokir ini)
                            if (audioEl) {
                                audioEl.currentTime = 0;
                                audioEl.play().catch(err => {
                                    console.warn('Autoplay audio diblokir browser:', err);
                                });
                            }

                            // 2. Tampilkan SweetAlert
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
                                icon: 'info',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 6000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                        });
                });
            }
        </script>
        @endscript
    @endif
</div>