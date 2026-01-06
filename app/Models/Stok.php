<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stoks';
    
    protected $fillable = [
        'merk_id', 'tipe_id', 'ram_storage', 'kondisi', 
        'imei', 'harga_modal', 'harga_jual'
    ];

    public function merk()
    {
        return $this->belongsTo(Merk::class, 'merk_id');
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'tipe_id');
    }
}