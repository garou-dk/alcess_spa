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
}
