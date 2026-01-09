<div id="login-notification-component">
    {{-- Audio Element --}}
    <audio id="loginSound" src="{{ asset('images/notif.mp3') }}" preload="auto" style="display: none;"></audio>

    {{-- Data Channel (Hanya ini yang dirender Livewire) --}}
    {{-- Kita beri ID unik agar JS bisa menemukannya --}}
    <div id="notification-channels-data" 
         data-channels="{{ json_encode($channels) }}" 
         style="display: none;">
    </div>
</div>