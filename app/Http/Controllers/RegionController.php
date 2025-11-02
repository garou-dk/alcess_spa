<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\RegionService;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(public RegionService $service) {}

    public function index(string $id) {
        return ApiResponse::success()
            ->data($this->service->index(['island_group_id' => $id]))
            ->response();
    }
}
