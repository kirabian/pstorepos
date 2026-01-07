<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap', 
        'idlogin', 
        'email', 
        'password', 
        'tanggal_lahir', 
        'role', 
        'distributor_id', 
        'cabang_id', 
        'last_seen'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen' => 'datetime',
    ];

    // --- RELASI UTAMA ---

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    /**
     * Relasi Single Cabang
     * Digunakan untuk role operasional seperti: Sales, Kasir, Admin Produk, Gudang
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    /**
     * Relasi Multi Cabang (Many-to-Many)
     * KHUSUS UNTUK ROLE AUDIT
     * Mengambil data dari tabel pivot 'branch_user'
     */
    public function accessibleBranches()
    {
        return $this->belongsToMany(Cabang::class, 'branch_user', 'user_id', 'cabang_id');
    }

    // --- HELPER METHODS & ACCESSORS ---

    public function isOnline()
    {
        return Cache::has('user-is-online-'.$this->id);
    }

    /**
     * Helper Penting: Mendapatkan Daftar ID Cabang yang Boleh Diakses
     * Digunakan untuk filtering data di Controller
     */
    public function getAccessCabangIdsAttribute()
    {
        // 1. Superadmin: Akses SEMUA cabang
        if ($this->role === 'superadmin') {
            return Cabang::pluck('id')->toArray();
        }

        // 2. Audit: Akses cabang sesuai yang ditugaskan (Tabel Pivot)
        if ($this->role === 'audit') {
            return $this->accessibleBranches()->pluck('cabangs.id')->toArray();
        }

        // 3. User Lain (Sales/Kasir): Akses hanya cabang tempat dia bekerja
        return $this->cabang_id ? [$this->cabang_id] : [];
    }

    /**
     * Cek apakah user punya izin akses ke cabang tertentu
     */
    public function canAccessCabang($cabangId)
    {
        return in_array($cabangId, $this->access_cabang_ids);
    }

    /**
     * Format Last Seen dengan Timezone yang Dinamis
     */
    public function getLastSeenFormattedAttribute()
    {
        if (!$this->last_seen) {
            return 'Belum pernah login';
        }

        // Default Timezone
        $timezone = 'Asia/Jakarta';

        // Logika Penentuan Timezone
        if ($this->cabang) {
            // Jika User punya cabang tetap (Sales/Kasir), ikut timezone cabang
            $timezone = $this->cabang->timezone;
        } elseif ($this->role === 'audit') {
            // Jika Audit (Multi Cabang), coba ambil timezone dari cabang pertama yang dia pegang
            // Atau default ke WIB jika belum pegang cabang
            $firstBranch = $this->accessibleBranches->first();
            if ($firstBranch) {
                $timezone = $firstBranch->timezone;
            }
        }

        // Label WIB/WITA/WIT
        $label = match ($timezone) {
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => 'WIB'
        };

        return Carbon::parse($this->last_seen)
            ->setTimezone($timezone)
            ->translatedFormat('d M, H:i') . ' ' . $label;
    }
}