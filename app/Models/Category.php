<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product; 
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'slug',
        // nếu muốn có thêm show_on_home thì thêm vào đây nữa
    ];

    public function products()
    {
        // categories.category_id -> products.category_id
        return $this->hasMany(Product::class, 'category_id', 'category_id');    
    }
    protected static function boot()
{
    parent::boot();

    static::saving(function ($category) {
        if (empty($category->slug) || $category->isDirty('category_name')) {
            $category->slug = Str::slug($category->category_name);
        }
    });
}
}
