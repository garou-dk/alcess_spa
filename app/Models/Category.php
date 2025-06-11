<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    
    public $primaryKey = 'category_id';
    protected $fillable = [
        'category_name',
        'category_slug',
        'category_image',
        'editable',
    ];
}
