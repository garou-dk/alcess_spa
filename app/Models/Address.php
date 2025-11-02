<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public $primaryKey = 'address_id';

    protected $fillable = [
        'user_id',
        'barangay_id',
        'other_details',
        'contact_number',
        'postal_code',
    ];

    public function barangay() {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'barangay_id');
    }
}
