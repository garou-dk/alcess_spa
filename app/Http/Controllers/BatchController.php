<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\BatchRequest;
use App\Services\BatchService;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function __construct(public BatchService $service) {}

    public function store(BatchRequest $request) {
        $result = $this->service->createBatch($request->validated());

        return ApiResponse::success()
            ->data($result)
            ->message('Batch created successfully')
            ->response();
    }

    public function recommendedBatch() {
        $result = $this->service->recommendedBatch();

        return ApiResponse::success()
            ->data($result)
            ->response();
    }
}
