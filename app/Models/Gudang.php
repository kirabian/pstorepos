<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    // Pastikan tabelnya bernama 'gudangs' (default Laravel)
    protected $table = 'gudangs'; 
    
    protected $fillable = ['kode_gudang', 'nama_gudang', 'alamat_gudang'];
    
    // Opsional: Relasi balik ke User
    public function users()
    {
        return $this->hasMany(User::class, 'gudang_id');
    }
}