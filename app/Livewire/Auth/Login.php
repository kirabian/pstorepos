<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Events\UserLoggedIn; // <--- PASTIKAN EVENT DI-IMPORT
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;

class Login extends Component
{
    public $idlogin, $password;

    #[Layout('layouts.master')]
    public function login()
    {
        // 1. Validasi Input
        $this->validate([
            'idlogin' => 'required',
            'password' => 'required',
        ]);

        // 2. Coba Login
        if (Auth::attempt(['idlogin' => $this->idlogin, 'password' => $this->password])) {
            
            $user = Auth::user();

            // 3. Cek Status Aktif (Security Check)
            if (!$user->is_active) {
                Auth::logout();
                $this->addError('idlogin', 'Akun Anda dinonaktifkan. Hubungi Admin.');
                return;
            }

            // 4. Set Status Online di Cache (Indikator Lampu Hijau)
            $expiresAt = now()->addSeconds(60);
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
            
            // ======================================================
            // 5. TRIGGER EVENT NOTIFIKASI REALTIME
            // ======================================================
            try {
                // Opsional: Jangan kirim notif jika yang login adalah Superadmin/Audit sendiri
                if (!in_array($user->role, ['superadmin', 'audit'])) {
                    UserLoggedIn::dispatch($user);
                }
            } catch (\Exception $e) {
                // Silent fail: Jika Reverb mati, login tetap lanjut tanpa error
                Log::error("Gagal mengirim notifikasi login: " . $e->getMessage());
            }
            // ======================================================

            // 6. Regenerate Session & Redirect
            session()->regenerate();
            session()->flash('info', 'Selamat datang kembali, ' . $user->nama_lengkap);

            return redirect()->intended('/');
        }

        // Jika Login Gagal
        $this->addError('idlogin', 'ID Login atau Password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}