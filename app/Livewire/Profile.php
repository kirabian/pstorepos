<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // Wajib untuk upload file
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.master')]
#[Title('Profile Saya')]
class Profile extends Component
{
    use WithFileUploads;

    public $nama_lengkap;
    public $email;
    public $idlogin;
    public $role;
    
    // Variable untuk upload foto
    public $photo; 
    public $existingPhoto;

    public function mount()
    {
        $user = Auth::user();
        
        $this->nama_lengkap = $user->nama_lengkap;
        $this->email = $user->email;
        $this->idlogin = $user->idlogin;
        $this->role = $user->role;
        $this->existingPhoto = $user->avatar_url; // Menggunakan accessor dari Model
    }

    public function updateProfile()
    {
        $user = Auth::user();

        // Validasi
        $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            // Validasi Foto: Image, Max 10MB (10240 KB)
            'photo' => 'nullable|image|max:10240', 
        ], [
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 10MB.',
            'photo.image' => 'File harus berupa gambar.',
        ]);

        // Logic simpan foto jika ada yang diupload
        if ($this->photo) {
            // Hapus foto lama jika bukan null dan file ada
            if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                Storage::delete($user->foto_profile);
            }

            // Simpan foto baru ke folder 'profile-photos' di disk public
            $path = $this->photo->store('profile-photos', 'public');
            $user->foto_profile = $path;
        }

        // Update data text
        $user->nama_lengkap = $this->nama_lengkap;
        $user->email = $this->email;
        $user->save();

        // Reset input file dan refresh foto tampilan
        $this->photo = null;
        $this->existingPhoto = $user->avatar_url;

        // Kirim notifikasi sukses (jika pakai flash message / toaster)
        session()->flash('message', 'Profile berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.profile');
    }
}