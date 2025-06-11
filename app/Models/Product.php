<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    
    public $primaryKey = 'product_id';
    protected $fillable = [
        'product_name',
        'description',
        'category_id',
        'unit_id',
        'product_image',
        'product_price',
        'product_quantity',
        'low_stock_threshold',
        'is_active',
        'sku',
    ];
}
