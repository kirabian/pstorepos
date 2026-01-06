<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipe extends Model
{
    use HasFactory;

    protected $table = 'tipes';
    
    protected $fillable = ['merk_id', 'nama', 'ram_storage'];

    // PENTING: Agar JSON di database otomatis jadi Array di PHP
    protected $casts = [
        'ram_storage' => 'array',
    ];

    // Relasi ke Merk (Tipe belongsTo Merk)
    public function merk()
    {
        return $this->belongsTo(Merk::class, 'merk_id');
    }
}