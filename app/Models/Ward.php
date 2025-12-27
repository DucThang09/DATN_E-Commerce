<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ward extends Model{
    use HasFactory;
    protected $table = 'wards';
    protected $primaryKey = 'wards_id';
    protected $fillable = [
        'wards_id','district_id','name',
    ];
     public $timestamps = false; 

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}