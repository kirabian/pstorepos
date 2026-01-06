<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // TAMBAHKAN INI
    public function imeis()
    {
        return $this->hasMany(ProductImei::class, 'product_variant_id');
    }
}