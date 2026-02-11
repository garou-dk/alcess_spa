<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ChangeNameRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeProfileRequest;
use App\Http\Requests\ChangeRoleRequest;
use App\Http\Requests\ChangeUserStatusRequest;
use App\Http\Requests\FetchUserRequest;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

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
        try {
            $result = $this->service->registerCustomer($request->validated());

            return ApiResponse::success()
                ->data($result)
                ->message('Verification link has been sent to your email')
                ->response();
        } catch (\Throwable $e) {
            return ApiResponse::bad_request()
                ->message($e->getMessage())
                ->response();
        }
    }

    public function createUser(UserRequest $request)
    {
        try {
            $result = $this->service->createUser($request->validated());

            return ApiResponse::success()
                ->data($result)
                ->message('User created successfully')
                ->response();
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate email or other database constraint violations
            if ($e->getCode() === '23000') {
                return ApiResponse::bad_request()
                    ->message('A user with this email already exists.')
                    ->response();
            }
            
            \Illuminate\Support\Facades\Log::error('Database error creating user: ' . $e->getMessage());
            return ApiResponse::bad_request()
                ->message('Failed to create user. Please try again.')
                ->response();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error creating user: ' . $e->getMessage());
            return ApiResponse::bad_request()
                ->message('An unexpected error occurred. Please try again.')
                ->response();
        }
    }

    public function index(FetchUserRequest $request)
    {
        $data = $request->validated();

        return ApiResponse::success()
            ->data($this->service->fetchUserPaginate($data))
            ->response();
    }

    public function changeName(string $id, ChangeNameRequest $request)
    {
        $data = $request->validated() + [
            'user_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->changeName($data))
            ->message('Name changed successfully')
            ->response();
    }

    public function changePassword(string $id, ChangePasswordRequest $request)
    {
        $data = $request->validated() + [
            'user_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->changePassword($data))
            ->message('Password changed successfully')
            ->response();
    }

    public function changeProfile(string $id, ChangeProfileRequest $request)
    {
        $data = $request->validated() + [
            'user_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->changeProfile($data))
            ->message('Profile updated successfully')
            ->response();
    }

    public function changeRole(string $id, ChangeRoleRequest $request)
    {
        $data = $request->validated() + [
            'user_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->changeRole($data))
            ->message('Role changed successfully')
            ->response();
    }

    public function changeStatus(string $id, ChangeUserStatusRequest $request)
    {
        $data = $request->validated() + [
            'user_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->changeStatus($data))
            ->message('Status changed successfully')
            ->response();
    }

    public function customerList(Request $request) {
        $data = $request->validate([
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ]);
        
        return ApiResponse::success()
            ->data($this->service->customerList($data))
            ->response();
    }
}
