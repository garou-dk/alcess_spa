<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchProduct extends Model
{
    public $primaryKey = 'batch_product_id';

    protected $fillable = [
        'batch_id',
        'product_id',
        'quantity',
    ];
}
