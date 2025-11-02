<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\MunicityService;
use Illuminate\Http\Request;

class MunicityController extends Controller
{
    public function __construct(public MunicityService $service) {}

    public function index(string $id) {
        return ApiResponse::success()
            ->data($this->service->index(['province_id' => $id]))
            ->response();
    }
}
