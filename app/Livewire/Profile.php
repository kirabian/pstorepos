<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
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
    
    // Variable Foto
    public $photo; 
    public $existingPhoto;

    public function mount()
    {
        $user = Auth::user();
        
        $this->nama_lengkap = $user->nama_lengkap;
        $this->email = $user->email;
        $this->idlogin = $user->idlogin;
        $this->role = $user->role;
        // Mengambil URL foto dari database saat pertama kali load
        $this->existingPhoto = $user->avatar_url; 
    }

    /**
     * FITUR BARU: AUTO-SAVE FOTO
     * Method ini akan OTOMATIS jalan begitu selesai pilih file foto.
     */
    public function updatedPhoto()
    {
        // 1. Validasi Foto
        $this->validate([
            'photo' => 'image|max:10240', // Max 10MB
        ]);

        $user = Auth::user();

        // 2. Hapus Foto Lama (Jika ada dan bukan default)
        if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
            Storage::delete($user->foto_profile);
        }

        // 3. Simpan Foto Baru ke Folder 'profile-photos'
        $path = $this->photo->store('profile-photos', 'public');
        
        // 4. UPDATE DATABASE LANGSUNG DISINI
        $user->foto_profile = $path;
        $user->save();

        // 5. Reset State agar tampilan ter-refresh
        // Kita update existingPhoto dengan URL baru
        $this->existingPhoto = $user->avatar_url; 
        
        // Kosongkan variabel $photo agar preview temporary hilang dan berganti ke existingPhoto yang baru
        $this->photo = null; 

        // 6. Kirim Notifikasi Sukses
        session()->flash('message', 'Foto profile berhasil diperbarui otomatis!');
    }

    /**
     * Method ini sekarang HANYA untuk update Biodata (Nama & Email)
     * Dijalankan saat tombol "Simpan Perubahan" ditekan
     */
    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Update Text
        $user->nama_lengkap = $this->nama_lengkap;
        $user->email = $this->email;
        $user->save();

        session()->flash('message', 'Biodata berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.profile');
    }
}   