<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    public $primaryKey = 'rate_id';

    protected $fillable = [
        'product_id',
        'user_id',
        'product_order_id',
        'rate',
        'comment',
        'reply',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
