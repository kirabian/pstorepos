<div>
    {{-- Audio Element (Hidden) --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>

    {{-- Pass PHP data to JS via data attribute --}}
    <div id="login-notification-data" data-channels="{{ json_encode($channels) }}" style="display: none;"></div>

    {{-- Use standard script tag, NOT @script --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            initLoginNotificationSystem();
        });

        // Fallback for hard refresh
        document.addEventListener('DOMContentLoaded', () => {
            initLoginNotificationSystem();
        });

        function initLoginNotificationSystem() {
            const dataEl = document.getElementById('login-notification-data');
            if (!dataEl) return;

            let activeChannels = [];
            try {
                const rawData = dataEl.getAttribute('data-channels');
                if (rawData) activeChannels = JSON.parse(rawData);
            } catch (e) {
                console.error("Error parsing channels:", e);
                return;
            }

            const audioEl = document.getElementById('loginSound');

            if (typeof window.Echo === 'undefined') {
                console.warn('Laravel Echo is not loaded.');
                return;
            }

            if (activeChannels.length > 0) {
                console.log('Listening on Channels:', activeChannels);

                activeChannels.forEach((channelName) => {
                    // Prevent double subscription
                    window.Echo.leave(channelName);

                    window.Echo.private(channelName)
                        .listen('.login-event', (e) => {
                            console.log('🔔 NOTIFICATION RECEIVED:', e);

                            // 1. Play Audio
                            if (audioEl) {
                                audioEl.currentTime = 0;
                                audioEl.play().catch(err => {
                                    console.log('Audio autoplay prevented.');
                                });
                            }

                            // 2. SweetAlert
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'User Login Detected!',
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
        }
    </script>
</div>