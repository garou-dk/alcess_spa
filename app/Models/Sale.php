<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public $primaryKey = "sale_id";

    protected $fillable = [
        "sale_code",
        "user_id",
        "total_amount",
        "customer_name",
        "customer_address",
        "prepared_by",
        "payment_method"
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function saleItems() {
        return $this->hasMany(SaleItem::class,'sale_id','sale_id');
    }

    public function order() {
        return $this->hasOne(Order::class, 'sale_id', 'sale_id');
    }
}
