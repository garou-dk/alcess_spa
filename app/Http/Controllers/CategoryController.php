<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryImageRequest;
use App\Http\Requests\UpdateCategoryNameRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(public CategoryService $service) {}

    public function index()
    {
        return ApiResponse::success()
            ->data($this->service->index())
            ->response();
    }

    public function store(CategoryRequest $request)
    {
        return ApiResponse::success()
            ->data($this->service->store($request->validated()))
            ->message('Category created successfully')
            ->response();
    }

    public function updateCategoryName(string $id, UpdateCategoryNameRequest $request)
    {
        $data = $request->validated() + [
            'category_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->updateCategoryName($data))
            ->message('Category name updated successfully')
            ->response();
    }

    public function updateCategoryImage(string $id, UpdateCategoryImageRequest $request)
    {
        $data = $request->validated() + [
            'category_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->updateCategoryImage($data))
            ->message('Category image updated successfully')
            ->response();
    }

    public function destroy(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->destroy(['category_id' => $id]))
            ->message('Category deleted successfully')
            ->response();
    }

    public function restore(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->restore(['category_id' => $id]))
            ->message('Category restored successfully')
            ->response();
    }
}
