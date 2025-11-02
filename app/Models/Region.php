<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $primaryKey = 'region_id';

    protected $fillable = [
        'region_code',
        'region_name',
        'region_name_long',
        'island_group_id',
    ];

    public function islandGroup() {
        return $this->belongsTo(IslandGroup::class, 'island_group_id', 'island_group_id');
    }
}
