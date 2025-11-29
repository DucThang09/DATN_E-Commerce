<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;
    protected $table = 'admin_notifications';

    protected $fillable = [
    'type',
    'title',
    'message',
    'details',   // <== thêm dòng này
    'is_read',
    'link_url',
];

    protected $casts = [
        'data'    => 'array',
        'is_read' => 'boolean',
    ];
}
