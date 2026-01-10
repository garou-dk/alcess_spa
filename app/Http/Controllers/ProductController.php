<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\AddStockRequest;
use App\Http\Requests\DeepSearchProductRequest;
use App\Http\Requests\ProductImageRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\SearchProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function createWithMedia(Request $request)
    {
        $data = $request->all();

        return ApiResponse::success()
            ->data($this->service->createWithMedia($data))
            ->message('Product created successfully')
            ->response();
    }

    public function validateProductInfo(ProductRequest $request)
    {
        // If validation passes, return success
        return ApiResponse::success()
            ->message('Product information is valid')
            ->response();
    }

    public function validateProductName(Request $request)
    {
        $data = $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'product_name'),
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->category_id) {
                        $category = \App\Models\Category::find($request->category_id);
                        if ($category && strtolower($category->category_name) === strtolower($value)) {
                            $fail('The product name cannot be the same as the category name.');
                        }
                    }
                    if ($request->unit_id) {
                        $unit = \App\Models\Unit::find($request->unit_id);
                        if ($unit && strtolower($unit->unit_name) === strtolower($value)) {
                            $fail('The product name cannot be the same as the unit name.');
                        }
                    }
                },
            ],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'category_id')],
            'unit_id' => ['nullable', 'integer', Rule::exists('units', 'unit_id')],
        ]);

        return ApiResponse::success()
            ->message('Product name is valid')
            ->response();
    }

    public function validateSku(Request $request)
    {
        $data = $request->validate([
            'sku' => [
                'required',
                'string',
                'regex:/^\d+$/',
                Rule::unique('products', 'sku'),
            ],
        ]);

        return ApiResponse::success()
            ->message('SKU is valid')
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
        $data = ['product_id' => $id];
        
        // Add user_id if authenticated
        if (auth('sanctum')->check()) {
            $data['user_id'] = auth('sanctum')->user()->user_id;
        }
        
        return ApiResponse::success()
            ->data($this->service->fetchAvailableProduct($data))
            ->response();
    }

    public function searchProduct(DeepSearchProductRequest $request)
    {
        return ApiResponse::success()
            ->data($this->service->searchProduct($request->validated()))
            ->response();
    }

    public function searchBySku(string $id) {
        return ApiResponse::success()
            ->data($this->service->searchBySku(['sku' => $id]))
            ->response();
    }

    public function inventoryCount(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);
        
        return ApiResponse::success()
            ->data($this->service->inventoryCount($data))
            ->response();
    }

    public function getProductByCategory(string $id) {
        return ApiResponse::success()
            ->data($this->service->getProductByCategory(['category'=> $id]))
            ->response();
    }

    public function searchProductName(Request $request) {
        $data = $request->validate([
            'search' => ['required', 'string', 'max:255']
        ]);

        return ApiResponse::success()
            ->data($this->service->searchProductName($data))
            ->response();
    }

    public function addStock(string $id, AddStockRequest $request)
    {
        $data = $request->validated() + ['product_id' => $id];

        return ApiResponse::success()
            ->data($this->service->addStock($data))
            ->message('Stock added successfully')
            ->response();
    }
}
