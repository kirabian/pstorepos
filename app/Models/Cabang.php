<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $fillable = ['kode_cabang', 'nama_cabang', 'lokasi', 'timezone'];

    public function getLocalTimeAttribute()
    {
        return now($this->timezone);
    }

    /**
     * Relasi ke User (Multi-user & Multi-cabang)
     * Menggunakan tabel pivot branch_user agar role Audit muncul di sini
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_user', 'branch_id', 'user_id');
    }
    
    /**
     * Tambahan: Mendapatkan staf reguler yang terikat langsung via cabang_id 
     * (untuk role selain Audit)
     */
    public function regularStaff()
    {
        return $this->hasMany(User::class, 'cabang_id');
    }
}