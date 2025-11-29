<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model{
    use HasFactory;
    protected $table = 'province';
    protected $primaryKey = 'province_id';
    protected $fillable = [
        'province_id','name',
    ];

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}