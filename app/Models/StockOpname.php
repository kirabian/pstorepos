<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_id', 'user_id', 'tipe_id', 
        'stok_sistem', 'stok_fisik', 'selisih', 
        'keterangan', 'tanggal_opname'
    ];

    public function cabang() { return $this->belongsTo(Cabang::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function tipe() { return $this->belongsTo(Tipe::class); }
}