<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SetOrderToDelivery;
use App\Http\Requests\SetPaymentRequest;
use App\Services\AuthService;
use App\Services\DashboardService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        public OrderService $service,
        public DashboardService $dashboardService
    ) {}

    public function orderProducts(OrderRequest $request) {
        $authService = new AuthService;
        $user = $authService->getAuth();
        $data = $request->validated() + ['user_id' => $user->user_id];

        $result = $this->service->orderProduct($data);
        
        // Broadcast dashboard stats update for new order
        $this->dashboardService->broadcastDashboardStatsUpdate();

        return ApiResponse::success()
            ->data($result)
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

    public function getPaidOrders() {
        $authService = new AuthService;
        $user = $authService->getAuth();

        return ApiResponse::success()
            ->data($this->service->getPaidOrders(['user_id' => $user->user_id]))
            ->response();
    }

    public function getAllOrders() {
        return ApiResponse::success()
            ->data($this->service->getAllOrders())
            ->response();
    }

    /**
     * Admin accepts order - sets shipping fee (required for Delivery, $0 for Pickup)
     * Order remains in Processing status
     */
    public function acceptOrder(string $id, Request $request) {
        $validated = $request->validate([
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        $data = $validated + ['order_id' => $id];

        $result = $this->service->acceptOrder($data);
        
        // Broadcast dashboard stats update for order acceptance
        $this->dashboardService->broadcastDashboardStatsUpdate();
        
        return ApiResponse::success()
            ->data($result)
            ->message('Order accepted successfully.')
            ->response();
    }

    /**
     * Admin declines order - sets to Cancelled with reason
     */
    public function declineOrder(string $id, Request $request) {
        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);
        
        $data = $validated + ['order_id' => $id];

        $result = $this->service->declineOrder($data);
        
        // Broadcast dashboard stats update for order decline
        $this->dashboardService->broadcastDashboardStatsUpdate();
        
        return ApiResponse::success()
            ->data($result)
            ->message('Order declined.')
            ->response();
    }

    /**
     * Confirm payment for an order in Processing status
     * Transitions order from Processing to Confirmed
     */
    public function confirmPaymentStatus(string $id) {
        $data = ['order_id' => $id];

        $result = $this->service->confirmPaymentStatus($data);
        
        // Broadcast dashboard stats update for payment confirmation
        $this->dashboardService->broadcastDashboardStatsUpdate();
        
        return ApiResponse::success()
            ->data($result)
            ->message('Payment confirmed successfully. Order is now ready for delivery.')
            ->response();
    }

    /**
     * Admin cancels payment (e.g. scam) - cancels order with reason and notifies customer
     */
    public function cancelPayment(string $id, Request $request) {
        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        $data = $validated + ['order_id' => $id];

        $result = $this->service->cancelPayment($data);

        $this->dashboardService->broadcastDashboardStatsUpdate();

        return ApiResponse::success()
            ->data($result)
            ->message('Payment cancelled. Order has been cancelled.')
            ->response();
    }

    public function cancelOrder(string $id) {
        $authService = new AuthService();
        $user = $authService->getAuth();
        
        $data = ['order_id' => $id, 'user_id' => $user->user_id];

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

    // Old setToShipped method removed - now using setDeliveryWithProof which requires delivery proof upload

    public function markAsReceived(string $id) {
        $data = ['order_id'=> $id];
        return ApiResponse::success()
            ->data($this->service->markAsReceived($data))
            ->message('Order marked as delivered successfully!')
            ->response();
    }

    public function cashOnDeliveryConfirm(string $id) {
        $data = ['order_id'=> $id];
        return ApiResponse::success()
            ->data($this->service->cashOnDeliveryConfirm($data))
            ->message('Order has been finalized successfully!')
            ->response();
    }

    public function setDeliveryWithProof(string $id, Request $request) {
        $validated = $request->validate([
            'estimated_delivery_date_start' => ['required', 'date', 'after_or_equal:today'],
            'estimated_delivery_date_end' => ['required', 'date', 'after_or_equal:estimated_delivery_date_start'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // Max 5MB per image
            'video' => ['nullable', 'file', 'mimes:mp4,mov', 'max:51200'], // Max 50MB video
        ]);
        
        \Log::info('🎯 Controller received request', [
            'order_id' => $id,
            'has_files' => $request->hasFile('images') || $request->hasFile('video'),
            'has_images' => $request->hasFile('images'),
            'has_video' => $request->hasFile('video'),
            'delivery_dates' => [
                'start' => $validated['estimated_delivery_date_start'],
                'end' => $validated['estimated_delivery_date_end']
            ]
        ]);
        
        $data = [
            'order_id' => $id,
            'images' => $request->file('images', []),
            'video' => $request->file('video'),
            'estimated_delivery_date_start' => $validated['estimated_delivery_date_start'],
            'estimated_delivery_date_end' => $validated['estimated_delivery_date_end'],
        ];
        
        return ApiResponse::success()
            ->data($this->service->setDeliveryWithProof($data))
            ->message('Order set to delivery successfully!')
            ->response();
    }

    public function generateInvoice(string $id) {
        $data = ['order_id' => $id];
        
        return ApiResponse::success()
            ->data($this->service->generateInvoiceFromOrder($data))
            ->message('Sales invoice generated successfully!')
            ->response();
    }
}
