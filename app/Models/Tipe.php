<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipe extends Model
{
    use HasFactory;

    protected $table = 'tipes';
    
    // Tambahkan 'jenis' ke fillable
    protected $fillable = ['merk_id', 'nama', 'jenis', 'ram_storage'];

    protected $casts = [
        'ram_storage' => 'array',
    ];

    public function merk()
    {
        return $this->belongsTo(Merk::class, 'merk_id');
    }
}