<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model{
    use HasFactory;
    protected $table = 'status';
    protected $primaryKey = 'status_id';
    protected $fillable = [
        'status_id','status',
    ];
    public function orders()
    {
        return $this->hasMany(Order::class, 'status_id', 'status_id');
    }
}