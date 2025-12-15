<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Cập nhật mảng $fillable để bao gồm các trường mới
    protected $fillable = [
        'name',
        'price',
        'purchase_price',
        'qty_sold',
        'details',
        'category',      // cũ – có thể giữ lại
        'company',
        'color',
        'inventory',
        'discount',
        'revenue',
        'image_01',
        'image_02',
        'image_03',
        'category_id',   // mới thêm
    ];

    public function categoryModel()
    {
        // products.category_id -> categories.category_id
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
