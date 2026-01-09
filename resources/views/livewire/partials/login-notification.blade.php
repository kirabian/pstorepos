<div>
    {{-- Audio Element (Hidden) --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>

    {{-- Script Livewire 3 (Wajib di dalam div root) --}}
    @script
    <script>
        // Ambil data channels langsung dari property komponen PHP ($this->channels)
        // Livewire 3 otomatis mengizinkan akses ke properti public di dalam @script
        const channels = @json($channels);
        const audioEl = document.getElementById('loginSound');

        // Fungsi Debugging
        // console.log('Init Login Notification. Channels:', channels);

        if (typeof window.Echo === 'undefined') {
            console.error('Laravel Echo belum dimuat. Pastikan bootstrap.js & app.js terload.');
        } else if (channels.length > 0) {
            
            channels.forEach(channelName => {
                // Hindari double subscription dengan leave dulu
                window.Echo.leave(channelName);

                // Subscribe ke Private Channel
                window.Echo.private(channelName)
                    .listen('.user.logged.in', (e) => {
                        console.log('🔔 NOTIFIKASI LOGIN:', e);

                        // 1. Play Audio
                        if (audioEl) {
                            audioEl.currentTime = 0;
                            audioEl.play().catch(err => {
                                console.log('Audio autoplay blocked by browser.');
                            });
                        }

                        // 2. Tampilkan SweetAlert
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'User Login Terdeteksi!',
                                html: `
                                    <div class="d-flex align-items-center gap-3 text-start">
                                        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                                            <i class="fas fa-user fa-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5">${e.user_name}</div>
                                            <div class="text-muted small text-uppercase fw-bold">${e.user_role}</div>
                                            <div class="text-primary small fw-bold mt-1">
                                                <i class="fas fa-map-marker-alt me-1"></i> ${e.location}
                                            </div>
                                        </div>
                                    </div>
                                `,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 6000,
                                timerProgressBar: true,
                                toast: true,
                                background: '#fff',
                                customClass: {
                                    popup: 'shadow-lg border-start border-5 border-primary rounded-4'
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
    </script>
    @endscript
</div>