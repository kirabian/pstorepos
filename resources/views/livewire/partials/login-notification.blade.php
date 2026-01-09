<div>
    {{-- Audio Element (Hidden) --}}
    {{-- Menggunakan sound effect notifikasi pendek --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto"></audio>

    @if(count($channels) > 0)
        @script
        <script>
            const channels = @json($channels);
            const audio = document.getElementById('loginSound');

            channels.forEach(channelName => {
                // Subscribe ke Private Channel
                Echo.private(channelName)
                    .listen('.user.logged.in', (e) => {
                        console.log('Login Event Received:', e);

                        // 1. Mainkan Suara
                        try {
                            audio.currentTime = 0;
                            let playPromise = audio.play();
                            if (playPromise !== undefined) {
                                playPromise.catch(error => {
                                    console.log('Audio autoplay prevented by browser policy.');
                                });
                            }
                        } catch (err) {
                            console.error('Error playing sound', err);
                        }

                        // 2. Tampilkan SweetAlert Toast
                        Swal.fire({
                            title: 'User Login Terdeteksi!',
                            html: `
                                <div class="text-start">
                                    <strong>Nama:</strong> ${e.user_name}<br>
                                    <strong>Role:</strong> ${e.user_role}<br>
                                    <strong>Lokasi:</strong> ${e.location}
                                </div>
                            `,
                            icon: 'info',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            background: '#fff',
                            iconColor: '#0d6efd',
                            customClass: {
                                popup: 'shadow-lg border-start border-4 border-primary'
                            },
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    });
            });
        </script>
        @endscript
    @endif
</div>