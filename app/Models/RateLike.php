<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateLike extends Model
{
    protected $primaryKey = 'rate_like_id';

    protected $fillable = [
        'rate_id',
        'user_id',
    ];

    public function rate()
    {
        return $this->belongsTo(Rate::class, 'rate_id', 'rate_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
