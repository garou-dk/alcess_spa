<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\ProvinceService;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function __construct(public ProvinceService $service) {}

    public function index(string $id) {
        return ApiResponse::success()
            ->data($this->service->index(['region_id' => $id]))
            ->response();
    }
}
