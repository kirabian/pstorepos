<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merk extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'deskripsi', 'kategori'];

    // PENTING: Cast kategori ke array agar bisa dibaca Livewire
    protected $casts = [
        'kategori' => 'array',
    ];

    public function tipes()
    {
        return $this->hasMany(Tipe::class);
    }
}