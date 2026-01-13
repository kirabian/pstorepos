<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() // Sales
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function stok()
    {
        return $this->belongsTo(Stok::class, 'stok_id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'audited_by');
    }
}