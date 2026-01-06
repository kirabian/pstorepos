<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Brand extends Model {
    use HasUuids; // Tambahkan ini
    
    protected $guarded = [];
    public $incrementing = false; // Non-aktifkan incrementing
    protected $keyType = 'string'; // Set key type sebagai string
    
    public function products() { return $this->hasMany(Product::class); }
}