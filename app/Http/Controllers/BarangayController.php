<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\BarangayService;
use Illuminate\Http\Request;

class BarangayController extends Controller
{
    public function __construct(public BarangayService $service) {}

    public function index(string $id) {
        return ApiResponse::success()
            ->data($this->service->index(['municity_id' => $id]))
            ->response();
    }
}
