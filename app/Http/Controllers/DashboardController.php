<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(public DashboardService $service) {}

    public function getMonthlyRevenue() {
        return ApiResponse::success()
            ->data($this->service->getMonthlyRevenue())
            ->response();
    }


    public function getPendingOrdersReport() {
        return ApiResponse::success()
            ->data($this->service->getPendingOrdersReport())
            ->response();
    }

    public function getTotalInventoryValue() {
        return ApiResponse::success()
            ->data($this->service->getTotalInventoryValue())
            ->response();
    }

    public function getTop5MostSoldItems() {
        return ApiResponse::success()
            ->data($this->service->getTop5MostSoldItems())
            ->response();
    }

    public function getCurrentMonthReport(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getCurrentMonthReport($data['start_date'] ?? null, $data['end_date'] ?? null))
            ->response();
    }



    public function getRevenueByCategories(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getRevenueByCategories($data['start_date'] ?? null, $data['end_date'] ?? null))
            ->response();
    }

    public function getStockInOutReport(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getStockInOutReport($data['start_date'] ?? null, $data['end_date'] ?? null))
            ->response();
    }

    public function getPendingOrders() {
        return ApiResponse::success()
            ->data($this->service->getPendingOrders())
            ->response();
    }

    public function getRevenueReport(Request $request) {
        $data = $request->validate([
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getRevenueReport($data))
            ->response();
    }

    public function getForDeliveryReport(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getForDeliveryReport($data))
            ->response();
    }

    public function getOnlineSalesReport(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getOnlineSalesData($data['start_date'] ?? null, $data['end_date'] ?? null))
            ->response();
    }

    public function getWalkInSalesReport(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getWalkInSalesData($data['start_date'] ?? null, $data['end_date'] ?? null))
            ->response();
    }

    public function getCustomerReviewReport(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);
        
        return ApiResponse::success()
            ->data($this->service->getCustomerReviewData($data))
            ->response();
    }

    /**
     * Get all dashboard data in a single aggregated API call
     * This reduces multiple HTTP requests to one, improving performance
     * 
     * No caching to ensure real-time data display
     */
    public function getAggregatedDashboard() {
        $data = [
            'revenue' => $this->service->getMonthlyRevenue(),
            'pending_orders' => $this->service->getPendingOrdersReport(),
            'inventory_value' => $this->service->getTotalInventoryValue(),
            'top_sellers' => $this->service->getTop5MostSoldItems(),
            'monthly_report' => $this->service->getCurrentMonthReport(),
            'category_sales' => $this->service->getRevenueByCategories(),
            'stock_in_out' => $this->service->getStockInOutReport(),
            'pending_orders_list' => $this->service->getPendingOrders(),
        ];

        return ApiResponse::success()
            ->data($data)
            ->response();
    }

    /**
     * Clear dashboard cache for the authenticated user
     * Call this method when data changes (orders, products, etc.)
     */
    public static function clearDashboardCache($userId = null) {
        $userId = $userId ?? auth()->id();
        \Cache::forget('dashboard_aggregated_' . $userId);
    }
}
