<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\UnitRequest;
use App\Services\UnitService;

class UnitController extends Controller
{
    public function __construct(public UnitService $service) {}

    public function fetchAll()
    {
        return ApiResponse::success()
            ->data($this->service->fetchUnits())
            ->response();
    }

    public function store(UnitRequest $request)
    {
        $data = $request->validated();

        return ApiResponse::success()
            ->data($this->service->addUnit($data))
            ->message('Unit added successfully')
            ->response();
    }

    public function update(string $id, UnitRequest $request)
    {
        $data = $request->validated() + [
            'unit_id' => $id,
        ];

        return ApiResponse::success()
            ->data($this->service->updateUnit($data))
            ->message('Unit updated successfully')
            ->response();
    }

    public function destroy(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->deleteUnit(['unit_id' => $id]))
            ->message('Unit deleted successfully')
            ->response();
    }

    public function recover(string $id)
    {
        return ApiResponse::success()
            ->data($this->service->recoverUnit(['unit_id' => $id]))
            ->message('Unit recovered successfully')
            ->response();
    }
}
