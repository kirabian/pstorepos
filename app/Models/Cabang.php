<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cabang', 
        'nama_cabang', 
        'lokasi', 
        'timezone'
    ];

    /**
     * Helper: Mendapatkan waktu lokal saat ini berdasarkan timezone cabang
     * Contoh penggunaan: $cabang->local_time
     */
    public function getLocalTimeAttribute()
    {
        // Default ke WIB jika timezone kosong
        $tz = $this->timezone ?? 'Asia/Jakarta';
        return now($tz);
    }

    /**
     * Relasi Multi-Cabang (Many-to-Many)
     * KHUSUS UNTUK ROLE AUDIT
     * Mengambil user audit yang ditugaskan ke cabang ini via tabel pivot 'branch_user'
     */
    public function auditUsers()
    {
        // Parameter: RelatedModel, PivotTable, ForeignKunciModelIni, ForeignKunciModelLawan
        return $this->belongsToMany(User::class, 'branch_user', 'cabang_id', 'user_id')
                    ->withTimestamps();
    }
    
    /**
     * Relasi Single-Cabang (One-to-Many)
     * UNTUK STAFF OPERASIONAL (Sales, Kasir, Admin Produk, Gudang)
     * Mengambil user yang terikat langsung via kolom 'cabang_id' di tabel users
     */
    public function regularStaff()
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    /**
     * Opsional: Helper untuk mengambil SEMUA user yang ada di cabang ini
     * (Gabungan Staff Reguler + Audit yang pegang cabang ini)
     */
    public function getAllUsersAttribute()
    {
        return $this->regularStaff->merge($this->auditUsers);
    }
}