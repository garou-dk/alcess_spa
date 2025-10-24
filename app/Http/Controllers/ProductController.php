<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\DeepSearchProductRequest;
use App\Http\Requests\ProductImageRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\SearchProductRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(public ProductService $service) {}

    public function bestSelling()
    {
        return ApiResponse::success()
            ->data($this->service->bestSelling())
            ->response();
    }

    public function index(SearchProductRequest $request)
    {
        $data = $request->validated();

        return ApiResponse::success()
            ->data($this->service->index($data))
            ->response();
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        return ApiResponse::success()
            ->data($this->service->store($data))
            ->message('Product created successfully')
            ->response();
    }

    public function changeImage(string $id, ProductImageRequest $request)
    {
        $data = $request->validated() + ['product_id' => $id];

        return ApiResponse::success()
            ->data($this->service->updateImage($data))
            ->message('Product image updated successfully')
            ->response();
    }

    public function update(string $id, ProductRequest $request)
    {
        $data = $request->validated() + ['product_id' => $id];

        return ApiResponse::success()
            ->data($this->service->update($data))
            ->message('Product updated successfully')
            ->response();
    }

    public function destroy(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->remove(['product_id' => $id]))
            ->message('Product deleted successfully')
            ->response();
    }

    public function changeStatus(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->changeStatus(['product_id' => $id]))
            ->message('Status changed successfully')
            ->response();
    }

    public function countLowStock()
    {
        return ApiResponse::success()
            ->data($this->service->countLowStock())
            ->response();
    }

    public function activeProductCount()
    {
        return ApiResponse::success()
            ->data($this->service->activeProductCount())
            ->response();
    }

    public function outOfStockCount()
    {
        return ApiResponse::success()
            ->data($this->service->outOfStockCount())
            ->response();
    }

    public function fetchAvailableProduct(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->fetchAvailableProduct(['product_id' => $id]))
            ->response();
    }

    public function searchProduct(DeepSearchProductRequest $request)
    {
        return ApiResponse::success()
            ->data($this->service->searchProduct($request->validated()))
            ->response();
    }
}
