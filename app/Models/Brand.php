<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    // GUNAKAN HANYA FILLABLE, jangan ada guarded
    protected $fillable = ['name', 'uuid'];
    
    // HAPUS guarded
    // protected $guarded = [];
    
    // Boot method sederhana
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Pastikan UUID ada
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}