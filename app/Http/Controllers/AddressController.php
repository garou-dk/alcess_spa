<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\AddressRequest;
use App\Services\AddressService;
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
        $result = $this->service->find(['user_id' => $id]);

        return ApiResponse::success()
            ->data($result)
            ->response();
    }
}
