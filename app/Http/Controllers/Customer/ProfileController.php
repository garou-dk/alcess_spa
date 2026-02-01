<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeNameRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeProfileRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(public UserService $service) {}

    public function updateName(ChangeNameRequest $request)
    {
        $userId = $request->user()->user_id;
        $data = $request->validated() + ['user_id' => $userId];

        return ApiResponse::success()
            ->data($this->service->changeName($data))
            ->message('Name updated successfully')
            ->response();
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        $userId = $request->user()->user_id;
        $data = $request->validated() + ['user_id' => $userId];

        return ApiResponse::success()
            ->data($this->service->changePassword($data))
            ->message('Password updated successfully')
            ->response();
    }

    public function updateImage(ChangeProfileRequest $request)
    {
        $userId = $request->user()->user_id;
        $data = $request->validated() + ['user_id' => $userId];

        return ApiResponse::success()
            ->data($this->service->changeProfile($data))
            ->message('Profile image updated successfully')
            ->response();
    }
}
