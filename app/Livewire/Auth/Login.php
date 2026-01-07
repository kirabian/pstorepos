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

        // Cek kredensial
        if (Auth::attempt(['idlogin' => $this->idlogin, 'password' => $this->password])) {
            
            $user = Auth::user();

            // Cek Status Aktif (Double Protection selain Middleware)
            if (!$user->is_active) {
                Auth::logout();
                $this->addError('idlogin', 'Akun Anda dinonaktifkan. Hubungi Admin.');
                return;
            }

            // Set Cache Online Status
            $expiresAt = now()->addSeconds(60); // 60 detik
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
            
            // Update last login (Optional jika kolom ada)
            // $user->update(['last_seen' => now()]);

            session()->regenerate();
            session()->flash('info', 'Selamat datang kembali, ' . $user->nama_lengkap);

            return redirect()->intended('/');
        }

        $this->addError('idlogin', 'ID Login atau Password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}