<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\CustomerDashboardService;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function __construct(protected CustomerDashboardService $service) {}

    public function index(Request $request)
    {
        $userId = $request->user()->user_id;
        $data = $this->service->getDashboardData($userId);

        return ApiResponse::success()
            ->data($data)
            ->response();
    }
}
