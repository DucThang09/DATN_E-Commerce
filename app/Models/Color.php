<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model{
    use HasFactory;
    protected $table = 'color';
    protected $primaryKey = 'colorProduct_id';
    protected $fillable = [
        'colorProduct_id','colorProduct',
    ];
}