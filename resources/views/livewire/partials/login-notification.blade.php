<div>
    {{-- Audio Element (Hidden) --}}
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
            const dataEl = document.getElementById('notification-channels-data');
            if (!dataEl) return;

            let activeChannels = [];
            try {
                activeChannels = JSON.parse(dataEl.getAttribute('data-channels'));
            } catch (e) { return; }

            const audioEl = document.getElementById('loginSound');

            if (typeof window.Echo === 'undefined') {
                console.error('Laravel Echo ERROR: Pastikan npm run build sudah dijalankan.');
                return;
            }

            if (activeChannels.length > 0) {
                console.log('Mendengarkan Channel:', activeChannels);

                activeChannels.forEach((channelName) => {
                    window.Echo.leave(channelName);

                    window.Echo.private(channelName)
                        // PENTING: Pakai titik (.) di depan nama class broadcastAs
                        .listen('.UserLoginEvent', (e) => {
                            console.log('🔔 NOTIFIKASI MASUK:', e);

                            // 1. Play Audio
                            if (audioEl) {
                                audioEl.currentTime = 0;
                                // Interaksi user diperlukan browser modern, tapi sweetalert biasanya memicu ini
                                audioEl.play().catch(err => console.warn('Audio blocked:', err));
                            }

                            // 2. SweetAlert
                            Swal.fire({
                                title: 'User Baru Saja Login!',
                                html: `
                                    <div class="d-flex align-items-center gap-3 text-start">
                                        <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                                            <i class="fas fa-user-check fa-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-5 text-dark">${e.user_name}</div>
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
                                background: '#ffffff',
                                color: '#000',
                                customClass: {
                                    popup: 'shadow-lg border-start border-5 border-dark rounded-4'
                                },
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                        });
                });
            }
        }
    </script>
</div>