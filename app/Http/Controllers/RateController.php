<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\RateRequest;
use App\Services\AuthService;
use App\Services\RateService;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function __construct(public RateService $service) {}

    public function addRate(string $id, RateRequest $request) {
        $authService = new AuthService;
        $user = $authService->getAuth();
        $data = $request->validated() + ['user_id' => $user->user_id, 'product_order_id' => $id];
        return ApiResponse::success()
            ->data($this->service->addRate($data))
            ->response();
    }
}
