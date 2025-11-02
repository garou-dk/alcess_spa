<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municity extends Model
{
    public $primaryKey = 'municity_id';

    protected $fillable = [
        'municity_code',
        'municity_name',
        'province_id',
    ];

    public function province() {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }
}
