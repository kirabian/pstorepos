<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Brand extends Model {
    protected $guarded = []; // Mengizinkan semua field diisi
    public function products() { return $this->hasMany(Product::class); }
}