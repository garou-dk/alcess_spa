<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    public $primaryKey = 'product_order_id';

    protected $fillable = [
        'product_order_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function rate() {
        return $this->hasOne(Rate::class, 'product_order_id', 'product_order_id');
    }
}
