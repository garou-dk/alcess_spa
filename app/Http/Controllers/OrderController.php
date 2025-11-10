<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ApproveDeclineRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SetPaymentRequest;
use App\Services\AuthService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(public OrderService $service) {}

    public function orderProducts(OrderRequest $request) {
        $authService = new AuthService;
        $user = $authService->getAuth();
        $data = $request->validated() + ['user_id' => $user->user_id];

        return ApiResponse::success()
            ->data($this->service->orderProduct($data))
            ->message('Order placed successfully. Please wait for the confirmation.')
            ->response();
    }

    public function getOrder(string $id) {
        $authService = new AuthService;
        $user = $authService->getAuth();
        $data = ['user_id' => $user->user_id, 'order_id' => $id];

        return ApiResponse::success()
            ->data($this->service->getOrder($data))
            ->response();
    }

    public function getCustomerOrders() {
        $authService = new AuthService;
        $user = $authService->getAuth();

        return ApiResponse::success()
            ->data($this->service->getCustomerOrders(['user_id' => $user->user_id]))
            ->response();
    }

    public function getAllOrders() {
        return ApiResponse::success()
            ->data($this->service->getAllOrders())
            ->response();
    }

    public function approveOrDecline(string $id, ApproveDeclineRequest $request) {
        $data = $request->validated() + ['order_id' => $id];

        $result = $this->service->approveOrDecline($data);
        
        return ApiResponse::success()
            ->data($result)
            ->message('Order status changed successfully')
            ->response();
    }

    public function cancelOrder(string $id) {
        $data = ['order_id' => $id];

        return ApiResponse::success()
            ->data($this->service->cancelOrder($data))
            ->message('Order cancelled successfully')
            ->response();
    }

    public function setPayment(string $id, SetPaymentRequest $request) {
        $authService = new AuthService();
        $user = $authService->getAuth();

        $data = $request->validated() + ['user_id' => $user->user_id, 'order_id' => $id];

        return ApiResponse::success()
            ->data($this->service->setPayment($data))
            ->message('Thank you for your payment. Order placed successfully. Please wait for the store to confirm the payment.')
            ->response();
    }

    public function fetchPaymentImage(string $id) {
        $data = ['order_id' => $id];

        return ApiResponse::success()
            ->showFile($this->service->fetchPaymentImage($data));
    }

    public function confirmPayment(string $id) {
        $data = ['order_id' => $id];
        return ApiResponse::success()
            ->data($this->service->confirmPayment($data))
            ->message('Payment confirmed successfully!')
            ->response();
    }
}
