// resources/js/login-notification.js

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
            window.Echo.leave(channelName);

            window.Echo.private(channelName)
                .listen('.user.logged.in', (e) => {
                    console.log('🔔 NOTIFIKASI LOGIN:', e);

                    if (audioEl) {
                        audioEl.currentTime = 0;
                        audioEl.play().catch(err => console.log('Autoplay blocked'));
                    }

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
}