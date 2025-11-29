<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';

    // Các thuộc tính có thể gán đại trà
    protected $fillable = [
        'user_id',
        'pid',
        'name',
        'price',
        'quantity',
        'image',
    ];
}
