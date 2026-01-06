<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Brand extends Model {
    use HasUuids;
    
    protected $guarded = [];
    
    // Tentukan kolom UUID
    public function uniqueIds()
    {
        return ['uuid'];
    }
    
    public function products() { 
        return $this->hasMany(Product::class, 'brand_id'); 
    }
}