<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model{
    use HasFactory;
    protected $table = 'brand';
    protected $primaryKey = 'brand_id';
    protected $fillable = [
        'brand_id','brand_name',
    ];
     public function products()
    {
        return $this->hasMany(Product::class, 'company', 'brand_name');
    }
}