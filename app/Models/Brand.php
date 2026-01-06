<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Brand extends Model
{
    use HasUuids;
    
    protected $fillable = ['uuid', 'name'];
    
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}