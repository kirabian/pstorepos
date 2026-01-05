<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap', 'idlogin', 'email', 'password', 'tanggal_lahir', 'role', 'distributor_id', 'cabang_id', 'last_seen'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen' => 'datetime',
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
        return $this->belongsToMany(Cabang::class, 'branch_user', 'user_id', 'branch_id');
    }

    public function getLastSeenFormattedAttribute()
    {
        if (!$this->last_seen) {
            return 'Belum pernah login';
        }

        $timezone = $this->cabang->timezone ?? 'Asia/Jakarta';

        $label = match ($timezone) {
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => 'WIB'
        };

        return \Carbon\Carbon::parse($this->last_seen)
            ->setTimezone($timezone)
            ->translatedFormat('d M, H:i') . ' ' . $label;
    }
}