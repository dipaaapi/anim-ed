<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = [
        'title',
        'image_url',
        'target_url',
        'type',
        'views',
        'clicks',
        'is_active',
    ];
}
