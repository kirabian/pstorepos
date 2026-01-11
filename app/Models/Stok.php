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
        'nama_barang', // Baru
        'ram_storage', 
        'kondisi', 
        'imei', 
        'jumlah',
        'harga_modal', 
        'harga_jual',
        'status',         // Baru (ready, terjual, dll)
        'cabang_id',      // Baru
        'gudang_id',      // Baru
        'distributor_id'  // Baru
    ];

    public function merk()
    {
        return $this->belongsTo(Merk::class);
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}