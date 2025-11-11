<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    public $primaryKey = "sale_item_id";

    protected $fillable = [
        "sale_id", "product_id", "quantity", "price",
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
