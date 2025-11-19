<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Events\CustomerOrderEvent;
use App\Events\DetectOrderChangeEvent;
use App\Events\OrderNotificationEvent;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderNotification;
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

            $order->refresh();

            $order->load('user');

            $fullName = $order->user->full_name;

            $notification = OrderNotification::create([
                'user_id'=> $data['user_id'],
                'notification_type' => 'Pending Order',
                'notification_to' => 'Store',
                'message' => "{$fullName} has placed a new order. Please check the order.",
            ]);

            OrderNotificationEvent::dispatch($notification->toArray());

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
            ->with(['productOrders' => function($query) {
                $query->with(['product', 'rate']);
            }, 'barangay.municity.province.region.islandGroup'])
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
        return DB::transaction(function() use ($data) {
            $order = Order::query();

            if (!empty($data['user_id'])) {
                $order = $order->where('user_id', $data['user_id']);
            }

            $order = $order->where('order_id', $data['order_id'])
                ->with(['productOrders.product'])
                ->first();

            abort_if(empty($order), 404, 'Order not found.');

            abort_if($order->status != OrderStatusEnum::PENDING->value, 400, 'Order is not pending.');

            $order->status = $data['status'];
            $order->shipping_fee = $data['shipping_fee'] ?? 0;
            $list = array_map(fn ($item) => $item['price'] * $item['quantity'], $order->productOrders->toArray());
            $order->total_amount = array_sum($list) + $order->shipping_fee;
            if ($data['status'] == OrderStatusEnum::REJECTED->value) {
                $order->remarks = $data['remarks'];
            }
            
            $order->save();

            $order->refresh();

            $order->load(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup']);

            $orderStatus = $data['status'];

            $userId = $order->user_id;

            $notifcationType = $data['status'] == OrderStatusEnum::CONFIRMED->value ? 'Confirmed Order' : 'Rejected Order';

            $notification = OrderNotification::create([
                'user_id'=> $userId,
                'notification_type' => $notifcationType,
                'notification_to' => 'Customer',
                'message' => "Order has been {$orderStatus}",
            ]);

            CustomerOrderEvent::dispatch($notification->toArray(), $userId);
            DetectOrderChangeEvent::dispatch($notification->toArray(), $userId);

            return $order;
        });
    }

    public function cancelOrder(array $data) {
        return DB::transaction(function () use ($data) {
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

            $fullName = $order->user->full_name;

            $notification = OrderNotification::create([
                'user_id'=> $data['user_id'],
                'notification_type' => 'Cancelled Order',
                'notification_to' => 'Store',
                'message' => "{$fullName} has cancelled an order. Please check the order.",
            ]);

            OrderNotificationEvent::dispatch($notification->toArray());

            return $order;
        });
    }

    public function setPayment(array $data) {
        return DB::transaction(function () use ($data) {
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

            $fullName = $order->user->full_name;

            $notification = OrderNotification::create([
                'user_id'=> $data['user_id'],
                'notification_type' => 'Paid',
                'notification_to' => 'Store',
                'message' => "{$fullName} has set payment for an order. Please check the order.",
            ]);

            OrderNotificationEvent::dispatch($notification->toArray());

            return $order;
        });
    }

    public function fetchPaymentImage(array $data) {
        $order = Order::query()
            ->where('order_id', $data['order_id'])
            ->first();

        abort_if(empty($order), 404, 'Order not found!');
        abort_if(empty($order->proof_of_payment), 404, 'No proof of payment found');

        $fileService = new ManageFileService();

        $image = $fileService->getPaymentImage([
            'value' => $order->proof_of_payment
        ]);

        return $image;
    }

    public function confirmPayment(array $data) {
        $order = Order::query()
            ->where('order_id', $data['order_id'])
            ->first();

        abort_if(empty($order), 404, 'Order not found!');

        abort_if($order->payment_method == PaymentMethodEnum::OnlinePayment->value && $order->status != OrderStatusEnum::PROCESSING->value, 400, 'Order is not processing.');

        abort_if($order->payment_method == PaymentMethodEnum::CashOnDelivery->value && $order->status != OrderStatusEnum::SHIPPED->value, 400, 'Order is not pending.');
    
        abort_unless(empty($order->date_paid_confirmed), 422, 'This order is already been paid.');

        $order->date_paid_confirmed = now();

        $order->save();

        $order->refresh();

        return $order;
    }

    public function setToShipped(array $data) {
        return DB::transaction(function () use ($data) {
            $order = Order::query()
                ->with(['productOrders.product'])
                ->where('order_id', $data['order_id'])
                ->first();

            abort_if(empty($order), 404, 'Order not found!');

            $order->status = OrderStatusEnum::SHIPPED->value;
            $order->estimated_delivery_date_start = $data['estimated_delivery_date_start'];
            $order->estimated_delivery_date_end = $data['estimated_delivery_date_end'];
            $order->delivery_courier = $data['delivery_courier'];
            $order->tracking_number = $data['tracking_number'];

            $order->save();

            $order->refresh();

            foreach ($order->productOrders as $key => $value) {
                $product = Product::query()
                    ->where('product_id', $value->product_id)
                    ->first();

                $product->product_quantity = $product->product_quantity - $value->quantity;
                $product->save();
            };

            return $order;
        });
    }

    public function markAsReceived(array $data) {
        $order = Order::query()
            ->where('order_id', $data['order_id'])
            ->first();

        abort_if(empty($order), 404, 'Order not found!');

        $order->status = OrderStatusEnum::DELIVERED->value;
        $order->customer_received_date = now();

        $order->save();

        $order->refresh();

        return $order;
    }

    public function cashOnDeliveryConfirm(array $data) {
        $order = Order::query()
            ->where('order_id', $data['order_id'])
            ->first();

        abort_if(empty($order), 404, 'Order not found!');
        abort_if($order->status != OrderStatusEnum::CONFIRMED->value, 400, 'Order is not confirmed.');
        abort_if($order->payment_method != PaymentMethodEnum::CashOnDelivery->value, 400, 'Order is not cash on delivery.');

        $order->status = OrderStatusEnum::PROCESSING->value;

        $order->save();

        $order->refresh();

        return $order;
    }
}
