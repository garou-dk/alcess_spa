<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;
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

    public function product() {
        return $this->belongsTo(Product::class,'product_id', 'product_id');
    }

    public function likes() {
        return $this->hasMany(RateLike::class, 'rate_id', 'rate_id');
    }

    public function images() {
        return $this->hasMany(RateImage::class, 'rate_id', 'rate_id');
    }
}
