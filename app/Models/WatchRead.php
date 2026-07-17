<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchRead extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'item_type',
        'title',
        'episode_or_chapter',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
