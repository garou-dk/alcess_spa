<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\AuthService;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;

class OrderNotificationController extends Controller
{
    public function __construct(public OrderNotificationService $service) {}

    public function index() {
        $authService = new AuthService();
        $user = $authService->getAuth();
        $data = ['notification_to' => $user->role_id != 3 ? 'Store' : 'Customer'];
        return ApiResponse::success()
            ->data($this->service->fetchNotifications($data))
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
