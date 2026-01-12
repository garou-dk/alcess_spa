<?php

namespace App\Services;

use App\Models\OrderNotification;

class OrderNotificationService
{
    public function fetchNotifications(array $data) {
        $query = OrderNotification::query()
            ->with('user')
            ->where('notification_to', $data['notification_to']);

        if ($data['is_customer']) {
            $query->where('user_id', $data['user_id']);
        }

        return $query->latest()
            ->limit(10)
            ->get();
    }

    public function markAsRead(array $data) {
        $query = OrderNotification::query()
            ->where('order_notification_id', $data['order_notification_id'])
            ->where('notification_to', $data['notification_to']);

        if ($data['is_customer']) {
            $query->where('user_id', $data['user_id']);
        }

        $orderNotification = $query->first();

        abort_if(empty($orderNotification), 404, 'Order notification not found!');

        $orderNotification->is_read = true;

        $orderNotification->save();

        return $orderNotification;
    }

    public function markAllAsRead(array $data) {
        $query = OrderNotification::query()
            ->where('is_read', false)
            ->where('notification_to', $data['notification_to']);

        if ($data['is_customer']) {
            $query->where('user_id', $data['user_id']);
        }

        $orderNotification = $query->update(['is_read' => true]);
            
        return $orderNotification;
    }
}
