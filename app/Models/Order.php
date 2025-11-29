<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', // Liên kết với người dùng
        'name',
        'number',
        'email',  // Thêm email nếu muốn lưu
        'method',
        'address',
        'total_products',
        'total_price',
        'placed_on',
        'payment_status',

    ];
    protected $casts = [
        'placed_on' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');  // 'customer_id' là trường khóa ngoại trong bảng orders
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'order_id', 'id');
    }
}
