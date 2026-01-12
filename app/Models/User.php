<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_lengkap',
        'idlogin',
        'email',
        'password',
        'tanggal_lahir',
        'role',
        'foto_profile',
        // PASTIKAN KETIGA ID INI ADA
        'distributor_id',
        'cabang_id',
        'gudang_id', 
        'last_seen',
        'is_active',
        'theme_mode',
        'theme_color',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen' => 'datetime',
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Cabang::class, 'branch_user', 'user_id', 'cabang_id');
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-'.$this->id);
    }

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
        if (! $this->last_seen) {
            return 'Belum pernah login';
        }

        $timezone = 'Asia/Jakarta';

        if ($this->cabang) {
            $timezone = $this->cabang->timezone;
        } elseif ($this->role === 'audit' && $this->branches->isNotEmpty()) {
            $timezone = $this->branches->first()->timezone;
        }

        $label = match ($timezone) {
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            'Asia/Jakarta' => 'WIB',
            default => 'WIB'
        };

        return Carbon::parse($this->last_seen)
            ->setTimezone($timezone)
            ->translatedFormat('d M, H:i').' '.$label;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->foto_profile && Storage::disk('public')->exists($this->foto_profile)) {
            return Storage::url($this->foto_profile);
        }

        // Fallback ke inisial nama jika tidak ada foto
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap) . '&background=000&color=fff&bold=true';
    }
}