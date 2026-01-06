<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    // Baris ini yang WAJIB ada agar tidak error:
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}