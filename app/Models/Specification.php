<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    public $primaryKey = 'specification_id';

    public $fillable = [
        'product_id',
        'specification_name',
        'specification_value',
    ];
}
