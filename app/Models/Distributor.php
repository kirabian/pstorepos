<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model {
    protected $fillable = ['nama_distributor', 'kode_distributor', 'alamat'];

    // Relasi: Satu distributor bisa memiliki banyak user (PIC)
    public function users() {
        return $this->hasMany(User::class);
    }
}
