<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Distributor;
use App\Models\Cabang;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

#[Layout('layouts.master')]
class UserCreate extends Component
{
    public $nama_lengkap, $idlogin, $email, $password, $role, $distributor_id, $cabang_id;
    public $selected_branches = []; // Untuk role audit

    public function store()
    {
        $this->validate([
            'nama_lengkap' => 'required',
            'idlogin'      => 'required|unique:users,idlogin',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'role'         => 'required',
            'distributor_id' => 'required_if:role,distributor',
            'cabang_id'    => 'required_if:role,adminproduk,analis,leader,sales,gudang,security',
            'selected_branches' => 'required_if:role,audit|array',
        ]);

        $user = User::create([
            'nama_lengkap'   => $this->nama_lengkap,
            'idlogin'        => $this->idlogin,
            'email'          => $this->email,
            'password'       => Hash::make($this->password),
            'role'           => $this->role,
            'distributor_id' => ($this->role === 'distributor') ? $this->distributor_id : null,
            'cabang_id'      => ($this->role !== 'superadmin' && $this->role !== 'audit') ? $this->cabang_id : null,
        ]);

        // Jika Audit, simpan ke table pivot
        if ($this->role === 'audit') {
            $user->branches()->attach($this->selected_branches);
        }

        session()->flash('info', 'User ' . $this->nama_lengkap . ' berhasil didaftarkan.');
        return redirect()->route('user.index');
    }

    public function render()
    {
        return view('livewire.auth.user-create', [
            'distributors' => Distributor::orderBy('nama_distributor', 'asc')->get(),
            'cabangs' => Cabang::orderBy('nama_cabang', 'asc')->get(),
        ]);
    }
}