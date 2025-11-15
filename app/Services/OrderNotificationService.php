<?php

namespace App\Services;

use App\Models\OrderNotification;

class OrderNotificationService
{
    public function fetchNotifications() {
        return OrderNotification::query()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function markAsRead(array $data) {
        $orderNotification = OrderNotification::query()
            ->where('order_notification_id', $data['order_notification_id'])
            ->first();

        abort_if(empty($orderNotification), 404, 'Order notification not found!');

        $orderNotification->is_read = true;

        $orderNotification->save();

        return $orderNotification;
    }

    public function markAllAsRead() {
        $orderNotification = OrderNotification::query()
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return $orderNotification;
    }
}
