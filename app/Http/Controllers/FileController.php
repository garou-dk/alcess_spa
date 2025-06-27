<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\ManageFileService;

class FileController extends Controller
{
    public function __construct(public ManageFileService $service) {}

    public function fetchProfile(string $image)
    {
        return ApiResponse::success()
            ->showFile($this->service->fetchProfile([
                'image' => $image,
            ]));
    }
}
