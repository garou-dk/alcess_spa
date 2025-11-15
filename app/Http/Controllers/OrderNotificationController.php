<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;

class OrderNotificationController extends Controller
{
    public function __construct(public OrderNotificationService $service) {}

    public function index() {
        return ApiResponse::success()
            ->data($this->service->fetchNotifications())
            ->response();
    }

    public function markAsRead(string $id) {
        return ApiResponse::success()
            ->data($this->service->markAsRead(["order_notification_id"=> $id]))
            ->response();
    }

    public function markAllAsRead() {
        return ApiResponse::success()
            ->data($this->service->markAllAsRead())
            ->response();
    }
}
