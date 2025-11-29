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
        'category',  // Thêm trường category
        'company',   // Thêm trường company
        'color',     // Thêm trường color
        'inventory',
        'discount',  
        'revenue',   
        'image_01', 
        'image_02', 
        'image_03',
              
    ];
}
