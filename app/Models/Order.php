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
        'status_id',
        'number',
        'email',  // Thêm email nếu muốn lưu
        'method',
        'address',
        'total_products',
        'total_price',
        'placed_on',
        'payment_status',

    ];
    protected $appends = ['order_code'];
    protected $casts = [
        'placed_on' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'order_id', 'id');
    }
    // app/Models/Order.php
    public function orderStatus()
    {
        return $this->belongsTo(\App\Models\Status::class, 'status_id', 'status_id');
    }


    public function getOrderCodeAttribute(): string
    {
        return 'DH' . str_pad((string)$this->id, 8, '0', STR_PAD_LEFT);
    }
}
