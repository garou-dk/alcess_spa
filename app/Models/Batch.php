<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    public $primaryKey = 'batch_id';

    protected $fillable = [
        'user_id'
    ];

    public function batchProducts() {
        return $this->hasMany(BatchProduct::class, 'batch_id', 'batch_id');
    }
}
