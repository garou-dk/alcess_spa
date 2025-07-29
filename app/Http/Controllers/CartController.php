<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(public CartService $service) {}

    public function index() {
        return ApiResponse::success()
            ->data($this->service->index())
            ->response();
    }

    public function store(CartRequest $request) {
        return ApiResponse::success()
            ->data($this->service->addCart($request->validated()))
            ->message('Product added to cart successfully')
            ->response();
    }

    public function remove(string $id) {
        return ApiResponse::success()
            ->data($this->service->removeCart(['cart_id' => $id]))
            ->response();
    }

    public function cartCount() {
        return ApiResponse::success()
            ->data($this->service->cartCount())
            ->response();
    }
}
