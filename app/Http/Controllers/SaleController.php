<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\SaleRequest;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(public SaleService $service) {}

    public function recordSale(SaleRequest $request) {
        return ApiResponse::success()
            ->data($this->service->recordSale($request->validated()))
            ->response();
    }
}
