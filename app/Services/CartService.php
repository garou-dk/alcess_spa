<?php

namespace App\Services;

use App\Events\CartCountEvent;
use App\Models\Cart;
use App\Models\Product;

class CartService
{
    public function index() {
        $authService = new AuthService();
        $user = $authService->getAuth();
        $carts = Cart::query()
            ->select('carts.*')
            ->with(['product'])
            ->join('products', 'products.product_id', '=', 'carts.product_id')
            ->where('carts.user_id', $user->user_id)
            ->where('products.product_quantity', '>', 0)
            ->where('products.is_active', true)
            ->where('products.available_online', true)
            ->get();

        return $carts;
    }

    public function addCart(array $data) {
        $authService = new AuthService();
        $user = $authService->getAuth();

        $this->checkProductAvailable($data['product_id']);

        $cart = Cart::query()
            ->where('user_id', $user->user_id)
            ->where('product_id', $data['product_id'])
            ->first();

        $balance = Product::query()
                ->where('product_id', $data['product_id'])
                ->value('product_quantity');

        if (empty($cart)) {
            
            abort_if(
                $balance < $data['quantity'],
                422,
                'Insufficient product quantity.'
            );
            $cart = new Cart;
            $cart->user_id = $user->user_id;
            $cart->product_id = $data['product_id'];
            $cart->quantity = $data['quantity'];
        }
        else {
            $total = $cart->quantity + $data['quantity'];
            abort_if(
                $balance < $total,
                422,
                'Insufficient product quantity.'
            );
            $cart->quantity += $data['quantity'];
        }

        $cart->save();

        CartCountEvent::dispatch($this->cartCount(), $user->user_id);

        return $cart;
    }

    public function removeCart(array $data) {
        $authService = new AuthService();
        $user = $authService->getAuth();

        $cart = Cart::query()
            ->where('user_id', $user->user_id)
            ->where('product_id', $data['product_id'])
            ->first();

        if (!empty($cart)) {
            $cart->delete();
        }

        CartCountEvent::dispatch($this->cartCount(), $user->user_id);

        return $cart;
    }

    public function cartCount() {
        $authService = new AuthService();
        $user = $authService->getAuth();
        return Cart::query()
            ->where('user_id', $user->user_id)
            ->count();
    }

    public function checkProductAvailable($product_id) {
        $found = Product::query()
            ->where('product_id', $product_id)
            ->where('product_quantity', '>', 0)
            ->where('is_active', true)
            ->where('available_online', true)
            ->exists();

        abort_unless($found, 404, 'Product unavailable.');
    }
}
