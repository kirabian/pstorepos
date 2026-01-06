<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $guarded = [];

    // Relationship tetap pakai brand_id (integer)
    public function brand() { 
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    
    public function category() { 
        return $this->belongsTo(Category::class); 
    }
    
    public function variants() { 
        return $this->hasMany(ProductVariant::class); 
    }
}