<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $fillable = ['kode_cabang', 'nama_cabang', 'lokasi', 'timezone'];

    public function getLocalTimeAttribute()
    {
        return now($this->timezone ?? 'Asia/Jakarta');
    }

    /**
     * Relasi Multi-Cabang (Role Audit)
     * Diganti nama jadi 'auditUsers' dan key jadi 'cabang_id'
     */
    public function auditUsers()
    {
        return $this->belongsToMany(User::class, 'branch_user', 'cabang_id', 'user_id');
    }
    
    /**
     * Relasi Single-Cabang (Staff Reguler)
     */
    public function regularStaff()
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function stoks()
    {
        return $this->hasMany(Stok::class, 'cabang_id');
    }
}