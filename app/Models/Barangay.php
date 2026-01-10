<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    public $primaryKey = 'barangay_id';

    protected $fillable = [
        'barangay_code',
        'barangay_name',
        'old_name',
        'municity_id',
    ];

    public function municity() {
        return $this->belongsTo(Municity::class, 'municity_id', 'municity_id');
    }
}
