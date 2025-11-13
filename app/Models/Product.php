<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'available_online',
    ];

    protected $casts = [
        'rates_avg_rate' => 'decimal:1',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }

    public function specifications()
    {
        return $this->hasMany(Specification::class, 'product_id', 'product_id');
    }

    public function featuredImages()
    {
        return $this->hasMany(FeaturedImage::class, 'product_id', 'product_id');
    }

    public function rates() {
        return $this->hasMany(Rate::class, 'product_id', 'product_id');
    }
}
