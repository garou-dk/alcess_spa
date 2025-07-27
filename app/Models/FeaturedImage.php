<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedImage extends Model
{
    public $primaryKey = 'featured_image_id';

    public $fillable = [
        'product_id',
        'featured_image',
        'thumbnail',
    ];
}
