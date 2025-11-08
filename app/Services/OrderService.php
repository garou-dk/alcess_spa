<?php

namespace App\Services;

use App\Enums\OrderTypeEnum;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class OrderService
{
    public function orderProduct(array $data)  {
        $authService = new AuthService();
        $user = $authService->getAuth();
        
        return DB::transaction(function () use ($data, $user) {
            $address = Address::query()
                ->where('user_id', $user->user_id)
                ->first();

            abort_if(empty($address), 404, 'Address not found for the user.');

            $products = [];

            $product_ids = collect($data['products'])->pluck('product_id')->all();

            $productSelected = Product::query()
                ->whereIn('product_id', $product_ids)
                ->get();

            foreach ($data['products'] as $value) {
                $products[] = [
                    'product_id' => $value['product_id'],
                    'quantity' => $value['quantity'],
                    'price' => $productSelected->where('product_id', $value['product_id'])->value('product_price')
                ];
            }

            $orderInsert = [
                'user_id' => $data['user_id'],
                'order_type' => $data['order_type'],
                'contact_number' => $address->contact_number,
                'payment_method' => $data['payment_method'],
                'order_public_id' => Uuid::uuid4()->toString(),
            ];

            if ($data['order_type'] == OrderTypeEnum::DELIVERY->value) {
                $orderInsert['barangay_id'] = $address->barangay_id;
                $orderInsert['other_details'] = $address->other_details;
            }
            
            $order = Order::create($orderInsert);

            $order->productOrders()->createMany($products);

            return $order;
        });
    }

    public function getOrder(array $data) {
        $order = Order::query()
            ->with(['productOrders.product'])
            ->where([
                'order_id' => $data['order_id'],
                'user_id' => $data['user_id'],
            ])
            ->first();

        abort_if(empty($order), 404, 'Order not found.');

        return $order;
    }

    public function getCustomerOrders(array $data) {
        return Order::query()
            ->with(['productOrders.product'])
            ->where('user_id', $data['user_id'])
            ->get();
    }

    public function getAllOrders() {
        return Order::query()
            ->with(['productOrders.product', 'user'])
            ->get();
    }
}
