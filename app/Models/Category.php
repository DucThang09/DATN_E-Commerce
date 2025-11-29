<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'category_id',
        'category_name',
    ];
    public function products()
    {
        // category (product) -> category_name (category)
        return $this->hasMany(Product::class, 'category', 'category_name');
    }
}
