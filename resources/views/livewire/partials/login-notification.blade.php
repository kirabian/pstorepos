<div>
    {{-- Audio File --}}
    <audio id="notifSound" src="https://cdn.pixabay.com/audio/2022/03/15/audio_1720d209b0.mp3" preload="auto"></audio>

    @if(count($channels) > 0)
        @script
        <script>
            // Ambil data channel dari backend PHP
            const channels = @json($channels);
            const audio = document.getElementById('notifSound');

            console.log('Listening on channels:', channels); // Cek Console Browser (F12)

            channels.forEach(channelName => {
                
                // Pastikan Echo sudah terload
                if (typeof Echo === 'undefined') {
                    console.error('Laravel Echo belum dimuat! Cek app.js / bootstrap.js');
                    return;
                }

                // Subscribe ke Private Channel
                Echo.private(channelName)
                    .listen('.user.logged.in', (e) => {
                        console.log('EVENT DITERIMA:', e);

                        // 1. Play Audio
                        try {
                            audio.currentTime = 0;
                            audio.play().catch(error => console.log('Audio autoplay blocked:', error));
                        } catch (err) { }

                        // 2. Tampilkan SweetAlert
                        Swal.fire({
                            title: 'User Login!',
                            html: `
                                <div class="d-flex align-items-center gap-3 text-start">
                                    <div class="bg-primary text-white rounded-circle p-2" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
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
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            toast: true,
                            background: '#fff',
                            customClass: {
                                popup: 'shadow-lg border-start border-5 border-primary rounded-3'
                            },
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                    });
            });
        </script>
        @endscript
    @endif
</div>