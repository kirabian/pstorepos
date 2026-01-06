<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    // Cara paling ampuh menonaktifkan mass assignment protection
    public static function boot() {
        parent::boot();
        self::unguard(); 
    }
    
    protected $guarded = [];

    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
}