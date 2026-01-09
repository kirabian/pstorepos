<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

class Login extends Component
{
    public $idlogin, $password;

    // Pastikan file resources/views/layouts/guest.blade.php sudah ada
    #[Layout('layouts.guest')] 
    public function login()
    {
        // 1. Validasi Input
        $this->validate([
            'idlogin' => 'required',
            'password' => 'required',
        ]);

        // 2. Proses Login
        if (Auth::attempt(['idlogin' => $this->idlogin, 'password' => $this->password])) {
            $user = Auth::user();

            // 3. Cek Status Aktif
            if (!$user->is_active) {
                Auth::logout();
                $this->addError('idlogin', 'Akun Anda dinonaktifkan.');
                return;
            }

            // 4. Set Cache Online (Indikator Lampu Hijau)
            // Disimpan 60 detik agar sinkron dengan middleware
            $expiresAt = now()->addSeconds(60);
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
            
            // 5. Regenerate Session & Redirect
            session()->regenerate();
            
            return redirect()->intended('/');
        }

        // Jika Gagal
        $this->addError('idlogin', 'ID Login atau Password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}