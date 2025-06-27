<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(public RoleService $service) {}

    public function index()
    {
        return ApiResponse::success()
            ->data($this->service->fetchRoles())
            ->response();
    }
}
