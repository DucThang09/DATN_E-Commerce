<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model{
    use HasFactory;
    protected $table = 'district';
    protected $primaryKey = 'district_id';
    protected $fillable = [
        'district_id','province_id','name',
    ];

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}