<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_password_changed_at',
        'status',     // nếu có cột status trong bảng users
        // 'role',    // nếu sau này có dùng lại role thì bật lên
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ===== Quan hệ đơn hàng =====
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // ===== Quan hệ tin nhắn =====
    public function messages(): HasMany
    {
        // nếu bảng messages có cột user_id
        return $this->hasMany(Message::class, 'user_id');
    }

    // ===== Quan hệ giỏ hàng =====
    public function cart(): HasMany
    {
        // nếu model Cart dùng bảng carts/cart và có cột user_id
        return $this->hasMany(Cart::class, 'user_id');
    }

    // ===== Quan hệ wishlist =====
    public function wishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }
}
