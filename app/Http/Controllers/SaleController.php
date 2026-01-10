<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\SaleRequest;
use App\Services\DashboardService;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        public SaleService $service,
        public DashboardService $dashboardService
    ) {}

    public function recordSale(SaleRequest $request) {
        $result = $this->service->recordSale($request->validated());
        
        // Broadcast dashboard stats update for new sale
        $this->dashboardService->broadcastDashboardStatsUpdate();
        
        return ApiResponse::success()
            ->data($result)
            ->message("Sale recorded successfully")
            ->response();
    }

    public function getSale(string $id) {
        return ApiResponse::success()
            ->data($this->service->getSale(['sale_id' => $id]))
            ->response();
    }

    public function getTodayStats() {
        return ApiResponse::success()
            ->data($this->service->getTodayStats())
            ->response();
    }

    public function getAllSales() {
        return ApiResponse::success()
            ->data($this->service->getAllSales())
            ->response();
    }
}
