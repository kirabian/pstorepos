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
        'nama_lengkap', 'idlogin', 'email', 'password', 'tanggal_lahir', 
        'role', 'distributor_id', 'cabang_id', 'last_seen', 'is_active' // <--- Tambah is_active
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen' => 'datetime',
        'is_active' => 'boolean', // <--- Cast ke boolean
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-'.$this->id);
    }

    // Relasi Single (Untuk role umum)
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    // Relasi Multi (Khusus Role Audit)
    public function branches()
    {
        return $this->belongsToMany(Cabang::class, 'branch_user', 'user_id', 'cabang_id');
    }
    
    // Helper: Ambil Akses Cabang
    public function getAccessCabangIdsAttribute()
    {
        if ($this->role === 'superadmin') {
            return Cabang::pluck('id')->toArray();
        }
        if ($this->role === 'audit') {
            return $this->branches()->pluck('cabangs.id')->toArray();
        }
        return $this->cabang_id ? [$this->cabang_id] : [];
    }

    public function getLastSeenFormattedAttribute()
    {
        if (!$this->last_seen) {
            return 'Belum pernah login';
        }

        $timezone = 'Asia/Jakarta'; // Default

        if ($this->cabang) {
            $timezone = $this->cabang->timezone;
        } elseif ($this->role === 'audit' && $this->branches->isNotEmpty()) {
            $timezone = $this->branches->first()->timezone;
        }

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