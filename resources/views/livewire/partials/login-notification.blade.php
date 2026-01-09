<div>
    {{-- Audio Element --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto"></audio>

    @if(count($channels) > 0)
        @script
        <script>
            // Gunakan LET agar tidak error re-declaration di Livewire
            let activeChannels = @json($channels);
            let audioEl = document.getElementById('loginSound');

            // Cek apakah Echo sudah terload
            if (typeof window.Echo === 'undefined') {
                console.error('Laravel Echo BELUM dimuat. Cek bootstrap.js & app.js');
            } else {
                console.log('Mendengarkan channel:', activeChannels);

                activeChannels.forEach((channelName) => {
                    // Pastikan unsubscribe dulu jika ada sisa subscription lama (opsional tapi aman)
                    window.Echo.leave(channelName);

                    window.Echo.private(channelName)
                        .listen('.user.logged.in', (e) => {
                            console.log('🔔 NOTIFIKASI LOGIN:', e);

                            // 1. Play Audio
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