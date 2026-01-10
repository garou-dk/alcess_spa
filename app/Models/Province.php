<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $primaryKey = 'province_id';

    protected $fillable = [
        'province_code',
        'province_name',
        'region_id',
    ];

    public function region() {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }
}
