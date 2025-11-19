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

    public function getProductStats() {
        return ApiResponse::success()
            ->data($this->service->getProductStats())
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

    public function getCurrentMonthReport() {
        return ApiResponse::success()
            ->data($this->service->getCurrentMonthReport())
            ->response();
    }

    public function getPrevious7DaysSales()  {
        return ApiResponse::success()
            ->data($this->service->getPrevious7DaysSales())
            ->response();
    }

    public function getRevenueByCategories() {
        return ApiResponse::success()
            ->data($this->service->getRevenueByCategories())
            ->response();
    }

    public function getStockInOutReport() {
        return ApiResponse::success()
            ->data($this->service->getStockInOutReport())
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
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        return ApiResponse::success()
            ->data($this->service->getForDeliveryReport($data))
            ->response();
    }
}
