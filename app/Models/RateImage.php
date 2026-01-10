<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateImage extends Model
{
    protected $primaryKey = 'rate_image_id';

    protected $fillable = [
        'rate_id',
        'image_path',
    ];

    public function rate()
    {
        return $this->belongsTo(Rate::class, 'rate_id', 'rate_id');
    }
}
