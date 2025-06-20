<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(public AuthService $service) {}

    public function authenticateCustomer(LoginRequest $request) {
        $data = $request->validated() + [
            'role_id' => 2,
        ];
        $result = $this->service->loginUser($data);

        return ApiResponse::success()
            ->data($result)
            ->response();
    }

    public function authenticateAdmin(LoginRequest $request) {
        $data = $request->validated() + [
            'role_id' => 1,
        ];
        $result = $this->service->loginUser($data);

        return ApiResponse::success()
            ->data($result)
            ->response();
    }

    public function checkAuth() {
        $result = $this->service->getAuth();

        return ApiResponse::success()
            ->data($result)
            ->response();
    }
}
