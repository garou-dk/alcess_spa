<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    public $primaryKey = 'order_id';

    protected $fillable = [
        'user_id',
        'barangay_id',
        'other_details',
        'contact_number',
        'status',
        'date_paid_confirmed',
        'estimated_delivery_date_start',
        'estimated_delivery_date_end',
        'customer_received_date',
        'shipping_fee',
        'total_amount',
        'delivery_courier',
        'tracking_number',
        'order_type',
        'payment_method',
        'bank_name',
        'transaction_number',
        'order_public_id',
        'postal_code',
        'proof_of_payment',
        'remarks',
        'date_payment_processed'
    ];

    public function productOrders() {
        return $this->hasMany(ProductOrder::class, 'order_id', 'order_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function barangay() {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'barangay_id');
    }
}
