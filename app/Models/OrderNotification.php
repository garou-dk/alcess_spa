<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNotification extends Model
{
    public $primaryKey = 'order_notification_id';

    protected $fillable = [
        'user_id',
        'notification_type',
        'notification_to',
        'is_read',
        'message',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
