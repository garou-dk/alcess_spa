<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppConfiguration extends Model
{
    public $primaryKey = 'app_configuration_id';

    protected $fillable = [
        'key',
        'value'
    ];
}
