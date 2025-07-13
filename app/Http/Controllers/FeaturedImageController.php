<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\FeaturedImageRequest;
use App\Services\FeaturedImageService;

class FeaturedImageController extends Controller
{
    public function __construct(public FeaturedImageService $service) {}

    public function index(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->find(['product_id' => $id]))
            ->response();
    }

    public function store(FeaturedImageRequest $request)
    {
        $data = $request->validated();

        return ApiResponse::success()
            ->data($this->service->store($data))
            ->message('Featured image added successfully')
            ->response();
    }

    public function destroy(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->remove(['featured_image_id' => $id]))
            ->message('Featured image deleted successfully')
            ->response();
    }
}
