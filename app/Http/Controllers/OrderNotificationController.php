<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\AuthService;
use App\Services\OrderNotificationService;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;

class OrderNotificationController extends Controller
{
    public function __construct(public OrderNotificationService $service) {}

    public function index() {
        $authService = new AuthService();
        $user = $authService->getAuth();
        $isCustomer = $user->role->role_name === RoleEnum::CUSTOMER->value;
        $data = [
            'notification_to' => $isCustomer ? 'Customer' : 'Store',
            'user_id' => $user->user_id,
            'is_customer' => $isCustomer
        ];
        return ApiResponse::success()
            ->data($this->service->fetchNotifications($data))
            ->response();
    }

    public function markAsRead(string $id) {
        $authService = new AuthService();
        $user = $authService->getAuth();
        $isCustomer = $user->role->role_name === RoleEnum::CUSTOMER->value;
        return ApiResponse::success()
            ->data($this->service->markAsRead([
                "order_notification_id" => $id,
                "user_id" => $user->user_id,
                "is_customer" => $isCustomer,
                "notification_to" => $isCustomer ? 'Customer' : 'Store'
            ]))
            ->response();
    }

    public function markAllAsRead() {
        $authService = new AuthService();
        $user = $authService->getAuth();
        $isCustomer = $user->role->role_name === RoleEnum::CUSTOMER->value;
        $data = [
            'notification_to' => $isCustomer ? 'Customer' : 'Store',
            'user_id' => $user->user_id,
            'is_customer' => $isCustomer
        ];
        return ApiResponse::success()
            ->data($this->service->markAllAsRead($data))
            ->response();
    }
}
