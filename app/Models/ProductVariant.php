<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model {
    protected $guarded = [];
    public function product() { return $this->belongsTo(Product::class); }
    public function imeis() { return $this->hasMany(ProductImei::class); }
    
    // Helper untuk nama lengkap varian
    public function getFullNameAttribute() {
        return "{$this->ram}/{$this->storage} {$this->color} ({$this->condition})";
    }
}