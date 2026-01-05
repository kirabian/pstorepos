<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Distributor;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.master')]
class UserManager extends Component {
    public $nama_lengkap, $idlogin, $email, $password, $role, $distributor_id;

    public function storeUser() {
        $this->validate([
            'nama_lengkap' => 'required',
            'idlogin' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            // Validasi bersyarat: Wajib jika role adalah distributor
            'distributor_id' => 'required_if:role,distributor', 
        ]);

        User::create([
            'nama_lengkap' => $this->nama_lengkap,
            'idlogin' => $this->idlogin,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            // Simpan distributor_id hanya jika rolenya distributor
            'distributor_id' => ($this->role === 'distributor') ? $this->distributor_id : null,
        ]);

        $this->reset();
        session()->flash('info', 'Akun User Berhasil Dibuat!');
    }

    public function render() {
        return view('livewire.auth.user-manager', [
            'list_distributor' => Distributor::orderBy('nama_distributor', 'asc')->get()
        ]);
    }
}