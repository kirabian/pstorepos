<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $fillable = [
        'merk_id', 
        'tipe_id', 
        'ram_storage', 
        'kondisi', 
        'imei', 
        'jumlah', // <--- Tambahkan ini
        'harga_modal', 
        'harga_jual'
    ];

    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class);
    }
}