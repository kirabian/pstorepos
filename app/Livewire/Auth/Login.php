<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Events\UserLoggedIn; // <--- IMPORT EVENT
use Livewire\Attributes\Layout;

class Login extends Component
{
    public $idlogin, $password;

    #[Layout('layouts.master')]
    public function login()
    {
        $this->validate([
            'idlogin' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['idlogin' => $this->idlogin, 'password' => $this->password])) {
            
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $this->addError('idlogin', 'Akun Anda dinonaktifkan. Hubungi Admin.');
                return;
            }

            $expiresAt = now()->addSeconds(60);
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
            
            // ======================================================
            // TRIGGER EVENT NOTIFIKASI DISINI
            // ======================================================
            try {
                // Jangan kirim notif jika yang login adalah Superadmin/Audit itu sendiri (opsional)
                // Tapi biasanya admin ingin tau staff login.
                if (!in_array($user->role, ['superadmin', 'audit'])) {
                    UserLoggedIn::dispatch($user);
                }
            } catch (\Exception $e) {
                // Silent fail jika websocket error, agar login tetap jalan
            }
            // ======================================================

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