<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Helpers\ApiResponse;
use App\Http\Requests\AddressRequest;
use App\Services\AddressService;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(public AddressService $service) {}

    public function save(AddressRequest $request) {
        $data = $request->validated();

        $result = $this->service->save($data);

        return ApiResponse::success()
            ->data($result)
            ->message('Address saved successfully.')
            ->response();
    }

    public function find(string $id)  {
        $authService = new AuthService();
        $user = $authService->getAuth();
        $user_id = $id;
        if (!in_array($user->role->role_name, RoleEnum::storeUsers())) {
            $user_id = $user->user_id;
        }
        $result = $this->service->find(['user_id' => $user_id]);

        return ApiResponse::success()
            ->data($result)
            ->response();
    }
}
