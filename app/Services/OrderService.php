<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Enums\OrderStatusEnum;
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
                $orderInsert['postal_code'] = $address->postal_code;
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
            ->with(['productOrders.product', 'barangay.municity.province.region.islandGroup'])
            ->where('user_id', $data['user_id'])
            ->latest()
            ->get();
    }

    public function getAllOrders() {
        return Order::query()
            ->with(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup'])
            ->get();
    }

    public function approveOrDecline(array $data) {
        $order = Order::query();

        if (!empty($data['user_id'])) {
            $order = $order->where('user_id', $data['user_id']);
        }

        $order = $order->where('order_id', $data['order_id'])
            ->with(['productOrders.product'])
            ->first();

        abort_if(empty($order), 404, 'Order not found.');

        abort_if($order->status != OrderStatusEnum::PENDING->value, 400, 'Order is not pending.');

        abort_if($order->order_type == OrderTypeEnum::DELIVERY->value && !isset($data['shipping_fee']), 400, 'Shipping fee is required.');

        $order->status = $data['status'];
        $order->shipping_fee = $data['shipping_fee'] ?? 0;
        $list = array_map(fn ($item) => $item['price'] * $item['quantity'], $order->productOrders->toArray());
        $order->total_amount = array_sum($list) + $order->shipping_fee;
        
        $order->save();

        $order->refresh();

        $order->load(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup']);

        return $order;
    }

    public function cancelOrder(array $data) {
        $order = Order::query();

        if (!empty($data['user_id'])) {
            $order = $order->where('user_id', $data['user_id']);
        }

        $order = $order->where('order_id', $data['order_id'])->first();

        abort_if(empty($order), 404, 'Order not found.');

        abort_unless(
            in_array($order->status, [OrderStatusEnum::PENDING->value, OrderStatusEnum::CONFIRMED->value]),
            400,
            'You are not allowed to cancel this order any more.'
        );

        $order->status = OrderStatusEnum::CANCELLED->value;
        $order->save();

        $order->refresh();

        $order->load(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup']);

        return $order;
    }

    public function setPayment(array $data) {
        $order = Order::query();
        $order = $order->where('user_id', $data['user_id']);
        $order = $order->where('order_id', $data['order_id'])->first();

        abort_if(empty($order), 404, 'Order not found.');

        abort_if($order->status != OrderStatusEnum::CONFIRMED->value, 400, 'Order is not confirmed.');

        $fileService = new ManageFileService();

        $result = $fileService->saveFile($data['payment_proof'], FileDirectoryEnum::PAYMENT_PROOF->value);

        $order->status = OrderStatusEnum::PROCESSING->value;
        $order->proof_of_payment = $result['file_name'];
        $order->bank_name = $data['bank_name'];
        $order->transaction_number = $data['transaction_number'];
        $order->date_payment_processed = now();

        $order->save();

        $order->refresh();

        $order->load(['productOrders.product', 'barangay.municity.province.region.islandGroup']);

        return $order;
    }
}
