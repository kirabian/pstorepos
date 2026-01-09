<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

class Login extends Component
{
    public $idlogin = '';
    public $password = '';

    #[Layout('layouts.guest')] 
    public function login()
    {
        // --- DEBUG POINT ---
        // Jika kode ini jalan, berarti Livewire normal.
        // Hapus baris dd() ini jika sudah berhasil melihat pesan ini.
        // dd('MASUK PAK EKO - Livewire Berjalan!'); 
        // -------------------

        $this->validate([
            'idlogin' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['idlogin' => $this->idlogin, 'password' => $this->password])) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $this->addError('idlogin', 'Akun Anda dinonaktifkan.');
                return;
            }

            $expiresAt = now()->addSeconds(60);
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
            
            session()->regenerate();
            
            return redirect()->intended('/');
        }

        $this->addError('idlogin', 'ID Login atau Password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}