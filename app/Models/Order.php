<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public $primaryKey = 'order_id';

    protected $fillable = [
        'user_id',
        'barangay_id',
        'other_details',
        'contact_number',
        'status',
        'admin_accepted',
        'admin_accepted_at',
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
        'delivery_proof_images',
        'delivery_proof_video',
        'remarks',
        'date_payment_processed',
        'sale_id',
        'invoice_generated_at'
    ];

    protected $casts = [
        'delivery_proof_images' => 'array',
        'admin_accepted' => 'boolean',
        'admin_accepted_at' => 'datetime',
        'date_paid_confirmed' => 'datetime',
        'estimated_delivery_date_start' => 'date',
        'estimated_delivery_date_end' => 'date',
        'customer_received_date' => 'date',
        'date_payment_processed' => 'datetime',
        'invoice_generated_at' => 'datetime',
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

    public function sale() {
        return $this->belongsTo(Sale::class, 'sale_id', 'sale_id');
    }
}
