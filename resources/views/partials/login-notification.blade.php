@php
    $user = auth()->user();
    $channels = [];

    if ($user) {
        if ($user->role === 'superadmin') {
            $channels[] = 'superadmin-notify';
        }
        if ($user->role === 'audit') {
             // Accessor must be available in User model
            $channels = array_map(fn($id) => 'branch-notify.' . $id, $user->access_cabang_ids ?? []);
        }
    }
@endphp

@if(!empty($channels))
    {{-- Audio Element --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>

    {{-- Data Channel --}}
    <div id="login-notification-data" data-channels="{{ json_encode($channels) }}" style="display: none;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initLoginNotificationSystem();
        });

        // Hook into Livewire navigation if using Livewire SPA
        document.addEventListener('livewire:navigated', () => {
            initLoginNotificationSystem();
        });

        function initLoginNotificationSystem() {
            const dataEl = document.getElementById('login-notification-data');
            if (!dataEl) return;

            let activeChannels = [];
            try {
                activeChannels = JSON.parse(dataEl.getAttribute('data-channels'));
            } catch (e) { return; }

            const audioEl = document.getElementById('loginSound');

            if (typeof window.Echo === 'undefined') return;

            activeChannels.forEach(channelName => {
                window.Echo.leave(channelName);

                window.Echo.private(channelName)
                    .listen('.login-event', (e) => {
                        console.log('🔔 User Logged In:', e);

                        // Play Audio
                        if (audioEl) {
                            audioEl.currentTime = 0;
                            audioEl.play().catch(e => console.log('Audio blocked'));
                        }

                        // SweetAlert
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'User Login Detected!',
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
                                timer: 6000,
                                timerProgressBar: true
                            });
                        }
                    });
            });
        }
    </script>
@endif