<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\UserRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(public UserService $service) {}

    public function verifyEmail(string $uuid)
    {
        $result = $this->service->verifyEmail([
            'uuid' => $uuid,
        ]);

        if ($result) {
            return ApiResponse::success()
                ->message('Email verified successfully')
                ->response();
        }

        return ApiResponse::not_found()
            ->message('Email verification expired/invalid')
            ->response();
    }

    public function registerCustomer(UserRequest $request)
    {
        $result = $this->service->createUser($request->validated());

        return ApiResponse::success()
            ->data($result)
            ->message('Verification link has been sent to your email')
            ->response();
    }
}
