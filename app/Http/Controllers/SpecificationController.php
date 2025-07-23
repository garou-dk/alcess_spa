<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ProductSpecificationRequest;
use App\Http\Requests\UpdateProductSpecificationRequest;
use App\Services\SpecificationService;

class SpecificationController extends Controller
{
    public function __construct(public SpecificationService $service) {}

    public function index(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->find(['product_id' => $id]))
            ->response();
    }

    public function store(string $id, ProductSpecificationRequest $request)
    {
        $data = $request->validated() + ['product_id' => $id];

        return ApiResponse::success()
            ->data($this->service->store($data))
            ->message('Specification added successfully')
            ->response();
    }

    public function update(string $id, UpdateProductSpecificationRequest $request)
    {
        $data = $request->validated() + ['specification_id' => $id];

        return ApiResponse::success()
            ->data($this->service->update($data))
            ->message('Specification updated successfully')
            ->response();
    }

    public function destroy(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->remove(['specification_id' => $id]))
            ->message('Specification deleted successfully')
            ->response();
    }
}
