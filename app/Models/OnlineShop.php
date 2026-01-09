<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineShop extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_toko',
        'platform',
        'url_toko',
        'deskripsi',
        'is_active',
    ];
}