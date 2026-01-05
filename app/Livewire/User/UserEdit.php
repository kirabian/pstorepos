<?php

namespace App\Livewire\User;

use App\Models\Distributor;
use App\Models\User;
use App\Models\Cabang;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.master')]
class UserEdit extends Component
{
    public $userId, $nama_lengkap, $idlogin, $email, $role, $distributor_id, $cabang_id, $password;
    public $selected_branches = [];

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->nama_lengkap = $user->nama_lengkap;
        $this->idlogin = $user->idlogin;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->distributor_id = $user->distributor_id;
        $this->cabang_id = $user->cabang_id;
        $this->selected_branches = $user->branches->pluck('id')->toArray();
    }

    public function update()
    {
        $this->validate([
            'nama_lengkap' => 'required|min:3',
            'role' => 'required',
            'selected_branches' => 'required_if:role,audit|array',
            'password' => 'nullable|min:6',
        ]);

        $user = User::findOrFail($this->userId);

        $data = [
            'nama_lengkap' => $this->nama_lengkap,
            'role' => $this->role,
            'distributor_id' => ($this->role === 'distributor') ? ($this->distributor_id ?: null) : null,
            'cabang_id' => ($this->role !== 'superadmin' && $this->role !== 'audit') ? ($this->cabang_id ?: null) : null,
        ];

        if (! empty($this->password)) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        $user->update($data);

        // Sync Multi Cabang untuk Audit
        if ($this->role === 'audit') {
            $user->branches()->sync($this->selected_branches);
        } else {
            $user->branches()->detach();
        }

        session()->flash('info', 'Data '.$this->nama_lengkap.' berhasil diperbarui.');
        return redirect()->route('user.index');
    }

    public function render()
    {
        return view('livewire.auth.user-edit', [
            'distributors' => Distributor::orderBy('nama_distributor', 'asc')->get(),
            'cabangs' => Cabang::orderBy('nama_cabang', 'asc')->get(),
        ]);
    }
}