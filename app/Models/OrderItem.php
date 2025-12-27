<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'colorProduct_id',
        'product_name',
        'product_image',
        'quantity',
        'unit_price',
        'cost_price',
        'total_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
    public function color()
    {
        return $this->belongsTo(\App\Models\Color::class, 'colorProduct_id', 'colorProduct_id');
    }
}
