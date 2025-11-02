<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\IslandGroupService;
use Illuminate\Http\Request;

class IslandGroupController extends Controller
{
    public function __construct(public IslandGroupService $service) {}

    public function index()
    {
        $islandGroups = $this->service->getAll();
        return ApiResponse::success()
            ->data($islandGroups)
            ->response();
    }
}
