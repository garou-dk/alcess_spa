<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IslandGroup extends Model
{
    protected $primaryKey = 'island_group_id';

    protected $fillable = [
        'island_group_code',
        'island_group_name',
    ];
}
