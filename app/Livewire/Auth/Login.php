<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Import Cache
use Livewire\Attributes\Layout;

class Login extends Component
{
    public $idlogin, $password;

    // Pastikan menggunakan layout master
    #[Layout('layouts.master')]
    public function login()
    {
        $this->validate([
            'idlogin' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['idlogin' => $this->idlogin, 'password' => $this->password])) {
            $user = Auth::user();

            // --- LOGIKA BARU: Set Status Online ke Cache saat Login Berhasil ---
            // Simpan selama 11 detik agar sinkron dengan middleware UserActivity
            $expiresAt = now()->addSeconds(11);
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
            
            // Opsional: Update timestamp last_login_at jika kolomnya tersedia di database
            $user->update(['last_login_at' => now()]);
            // ------------------------------------------------------------------

            session()->regenerate();

            // Flash message untuk memberikan feedback sukses
            session()->flash('info', 'Selamat datang kembali, ' . $user->nama_lengkap);

            return redirect()->intended('/');
        }

        $this->addError('idlogin', 'ID Login atau Password tidak sesuai dengan record kami.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}