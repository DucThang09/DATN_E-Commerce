<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Cập nhật mảng $fillable để bao gồm các trường mới
    protected $table = 'products';
    protected $fillable = [
        'name',
        'price',
        'purchase_price',
        'qty_sold',
        'details',
        'category',      // cũ – có thể giữ lại
        'company',
        'discount',
        'revenue',
        'category_id',
        'specs',   // mới thêm
    ];
    protected $casts = [
        'specs' => 'array',
    ];
    public function categoryModel()
    {
        // products.category_id -> categories.category_id
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}
