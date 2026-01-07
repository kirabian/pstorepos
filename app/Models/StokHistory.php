<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StokHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Cabang (PENTING: Pastikan tabel stok_histories punya kolom cabang_id)
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    /**
     * Accessor untuk mengambil Tanggal & Jam sesuai Timezone Cabang
     * Cara panggil di blade: $log->waktu_lokal
     */
    public function getWaktuLokalAttribute()
    {
        // 1. Ambil timezone dari cabang tercatat, jika tidak ada default ke Asia/Jakarta (WIB)
        $timezone = $this->cabang->timezone ?? 'Asia/Jakarta';

        // 2. Tentukan Label (WIB/WITA/WIT)
        $label = match ($timezone) {
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => 'WIB'
        };

        // 3. Konversi created_at (UTC/Server Time) ke Timezone Cabang
        return Carbon::parse($this->created_at)
            ->setTimezone($timezone)
            ->translatedFormat('d F Y, H:i:s') . ' ' . $label;
    }
}