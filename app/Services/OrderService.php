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
use App\Models\Sale;
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
                'status' => OrderStatusEnum::PROCESSING->value,
                'admin_accepted' => false,
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
                'notification_type' => 'New Order',
                'notification_to' => 'Store',
                'message' => "{$fullName} has placed a new order. Please review and accept or decline.",
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
        $orders = Order::query()
            ->with([
                'user',
                'productOrders' => function($query) {
                    $query->with(['product']);
                },
                'productOrders.rate' => function($query) {
                    $query->with('images');
                },
                'barangay.municity.province.region.islandGroup', 
                'sale.saleItems.product'
            ])
            ->where('user_id', $data['user_id'])
            ->where('status', '!=', OrderStatusEnum::CANCELLED->value)
            ->latest()
            ->get();
        
        return $orders;
    }

    public function getPaidOrders(array $data) {
        return Order::query()
            ->with(['productOrders' => function($query) {
                $query->with(['product', 'rate']);
            }, 'barangay.municity.province.region.islandGroup', 'sale.saleItems.product'])
            ->where('user_id', $data['user_id'])
            ->whereNotNull('date_paid_confirmed')
            ->latest('date_paid_confirmed')
            ->get();
    }

    public function getAllOrders() {
        return Order::query()
            ->with([
                'productOrders.product:product_id,product_name,description,product_image,product_price,category_id,unit_id',
                'user',
                'barangay.municity.province.region.islandGroup'
            ])
            ->where('payment_method', 'Online Payment')
            ->latest()
            ->get();
    }

    /**
     * Admin accepts order - sets shipping fee (if Delivery) and marks as accepted
     * Order remains in Processing status
     * Pickup orders have $0 shipping fee
     */
    public function acceptOrder(array $data) {
        return DB::transaction(function() use ($data) {
            $order = Order::query()
                ->where('order_id', $data['order_id'])
                ->with(['productOrders.product'])
                ->first();

            abort_if(empty($order), 404, 'Order not found.');
            abort_if($order->status != OrderStatusEnum::PROCESSING->value, 400, 'Order must be in Processing status.');
            abort_if($order->admin_accepted, 400, 'Order has already been reviewed.');

            // Set shipping fee based on order type
            // Pickup orders have $0 shipping fee, Delivery orders require shipping fee
            if ($order->order_type === OrderTypeEnum::PICKUP->value) {
                $order->shipping_fee = 0;
            } else {
                // Delivery order - shipping fee is required
                abort_if(!isset($data['shipping_fee']), 400, 'Shipping fee is required for delivery orders.');
                $order->shipping_fee = $data['shipping_fee'];
            }
            
            // Calculate total amount
            $list = array_map(fn ($item) => $item['price'] * $item['quantity'], $order->productOrders->toArray());
            $order->total_amount = array_sum($list) + $order->shipping_fee;
            
            // Mark as accepted by admin
            $order->admin_accepted = true;
            $order->admin_accepted_at = now();
            $order->save();

            $order->refresh();
            $order->load(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup']);

            // Send notification to customer
            $notification = OrderNotification::create([
                'user_id'=> $order->user_id,
                'notification_type' => 'Order Accepted',
                'notification_to' => 'Customer',
                'message' => "Your order has been accepted. Please proceed with payment.",
            ]);

            CustomerOrderEvent::dispatch($notification->toArray(), $order->user_id);
            DetectOrderChangeEvent::dispatch($notification->toArray(), $order->user_id);

            return $order;
        });
    }

    /**
     * Admin declines order - sets status to Cancelled with reason
     */
    public function declineOrder(array $data) {
        return DB::transaction(function() use ($data) {
            $order = Order::query()
                ->where('order_id', $data['order_id'])
                ->with(['productOrders.product'])
                ->first();

            abort_if(empty($order), 404, 'Order not found.');
            abort_if($order->status != OrderStatusEnum::PROCESSING->value, 400, 'Order must be in Processing status.');
            abort_if($order->admin_accepted, 400, 'Cannot decline an already accepted order.');

            // Set to cancelled with reason
            $order->status = OrderStatusEnum::CANCELLED->value;
            $order->remarks = $data['remarks'];
            $order->save();

            $order->refresh();
            $order->load(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup']);

            // Send notification to customer
            $notification = OrderNotification::create([
                'user_id'=> $order->user_id,
                'notification_type' => 'Order Declined',
                'notification_to' => 'Customer',
                'message' => "Your order has been declined. Reason: {$data['remarks']}",
            ]);

            CustomerOrderEvent::dispatch($notification->toArray(), $order->user_id);
            DetectOrderChangeEvent::dispatch($notification->toArray(), $order->user_id);

            return $order;
        });
    }

    /**
     * Confirm payment for an order in Processing status
     * This transitions the order from Processing to Confirmed
     */
    public function confirmPaymentStatus(array $data) {
        return DB::transaction(function() use ($data) {
            $order = Order::query()
                ->where('order_id', $data['order_id'])
                ->with(['productOrders.product'])
                ->first();

            abort_if(empty($order), 404, 'Order not found.');
            abort_if($order->status != OrderStatusEnum::PROCESSING->value, 'Order must be in Processing status.');
            abort_if(!$order->admin_accepted, 400, 'Order must be accepted by admin first.');
            abort_if(empty($order->proof_of_payment), 400, 'No payment proof uploaded yet.');

            // Transition to Confirmed
            $order->status = OrderStatusEnum::CONFIRMED->value;
            $order->date_paid_confirmed = now();
            $order->save();

            $order->refresh();
            $order->load(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup']);

            // Send notification to customer
            $notification = OrderNotification::create([
                'user_id'=> $order->user_id,
                'notification_type' => 'Payment Confirmed',
                'notification_to' => 'Customer',
                'message' => "Your payment has been confirmed. Order is being prepared for delivery.",
            ]);

            CustomerOrderEvent::dispatch($notification->toArray(), $order->user_id);
            DetectOrderChangeEvent::dispatch($notification->toArray(), $order->user_id);

            return $order;
        });
    }

    /**
     * Cancel order - allowed in two scenarios:
     * 1. Processing status and NOT yet accepted by admin
     * 2. Processing status and accepted by admin (before payment confirmation)
     * Once payment is confirmed (Confirmed status), order cannot be cancelled
     */
    public function cancelOrder(array $data) {
        return DB::transaction(function () use ($data) {
            $order = Order::query();

            if (!empty($data['user_id'])) {
                $order = $order->where('user_id', $data['user_id']);
            }

            $order = $order->where('order_id', $data['order_id'])->first();

            abort_if(empty($order), 404, 'Order not found.');

            // Allow cancellation if:
            // 1. Order is in Processing status (regardless of admin_accepted)
            // 2. Order is NOT in Confirmed, Shipped, or Completed status
            abort_unless(
                $order->status === OrderStatusEnum::PROCESSING->value,
                400,
                'Order cannot be cancelled. It has already been confirmed or is being processed.'
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
                'message' => "{$fullName} has cancelled an order.",
            ]);

            OrderNotificationEvent::dispatch($notification->toArray());

            return $order;
        });
    }

    /**
     * Customer uploads payment proof while order is in Processing status
     * Order must be accepted by admin first
     * Order remains in Processing until admin confirms payment
     */
    public function setPayment(array $data) {
        return DB::transaction(function () use ($data) {
            $order = Order::query();
            $order = $order->where('user_id', $data['user_id']);
            $order = $order->where('order_id', $data['order_id'])->first();

            abort_if(empty($order), 404, 'Order not found.');
            abort_if($order->status != OrderStatusEnum::PROCESSING->value, 400, 'Order must be in Processing status.');
            abort_if(!$order->admin_accepted, 400, 'Order must be accepted by administrator first.');

            $fileService = new ManageFileService();

            $result = $fileService->saveFile($data['payment_proof'], FileDirectoryEnum::PAYMENT_PROOF->value);

            // Keep status as Processing - admin will confirm payment later
            $order->proof_of_payment = $result['file_name'];
            $order->bank_name = $data['bank_name'];
            $order->date_payment_processed = now();

            $order->save();

            $order->refresh();

            $order->load(['productOrders.product', 'barangay.municity.province.region.islandGroup']);

            $fullName = $order->user->full_name;

            $notification = OrderNotification::create([
                'user_id'=> $data['user_id'],
                'notification_type' => 'Payment Uploaded',
                'notification_to' => 'Store',
                'message' => "{$fullName} has uploaded payment proof. Please review and confirm.",
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
        return DB::transaction(function () use ($data) {
            $order = Order::query()
                ->with('productOrders')
                ->where('order_id', $data['order_id'])
                ->first();

            abort_if(empty($order), 404, 'Order not found!');

            abort_if($order->payment_method == PaymentMethodEnum::OnlinePayment->value && $order->status != OrderStatusEnum::CONFIRMED->value, 400, 'Order is not confirmed.');

            abort_if($order->payment_method == PaymentMethodEnum::CashOnDelivery->value && $order->status != OrderStatusEnum::SHIPPED->value, 400, 'Order is not shipped.');
        
            abort_unless(empty($order->date_paid_confirmed), 422, 'This order is already been paid.');

            // Reduce product stock for each product in the order
            foreach ($order->productOrders as $productOrder) {
                Product::query()
                    ->where('product_id', $productOrder->product_id)
                    ->decrement('product_quantity', $productOrder->quantity);
            }

            $order->date_paid_confirmed = now();
            
            // Change status from CONFIRMED to PROCESSING after payment confirmation
            if ($order->payment_method == PaymentMethodEnum::OnlinePayment->value) {
                $order->status = OrderStatusEnum::PROCESSING->value;
            }

            $order->save();

            $order->refresh();

            return $order;
        });
    }

    /**
     * @deprecated This method is deprecated. Use setDeliveryWithProof() instead which requires delivery proof upload.
     * Kept for reference only - should not be called.
     */
    public function setToShipped(array $data) {
        // This method is deprecated and should not be used
        // Use setDeliveryWithProof() instead to ensure delivery proof is uploaded
        throw new \Exception('This method is deprecated. Use setDeliveryWithProof() to upload delivery proof.');
    }

    public function markAsReceived(array $data) {
        $order = Order::query()
            ->where('order_id', $data['order_id'])
            ->first();

        abort_if(empty($order), 404, 'Order not found!');
        abort_if($order->status != OrderStatusEnum::SHIPPED->value, 400, 'Order is not shipped yet.');

        $order->status = OrderStatusEnum::COMPLETED->value;
        $order->customer_received_date = now();

        $order->save();

        // Reload with all relationships including rates
        $order->load([
            'productOrders' => function($query) {
                $query->with(['product']);
            },
            'productOrders.rate' => function($query) {
                $query->with('images');
            },
            'barangay.municity.province.region.islandGroup',
            'sale.saleItems.product'
        ]);

        // Dispatch event to notify via WebSocket
        DetectOrderChangeEvent::dispatch($order->toArray(), $order->user_id);

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

    public function setDeliveryWithProof(array $data) {
        return DB::transaction(function () use ($data) {
            $order = Order::query()
                ->where('order_id', $data['order_id'])
                ->first();

            abort_if(empty($order), 404, 'Order not found!');
            abort_if($order->status != OrderStatusEnum::CONFIRMED->value, 400, 'Order must be in Confirmed status to set delivery.');

            $fileService = new ManageFileService();
            $imagePaths = [];
            $videoPath = null;

            \Log::info('🔵 setDeliveryWithProof - Starting', [
                'order_id' => $data['order_id'],
                'has_images' => !empty($data['images']),
                'images_count' => is_array($data['images']) ? count($data['images']) : 0,
                'has_video' => !empty($data['video'])
            ]);

            // Handle image uploads (max 3)
            if (!empty($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $image) {
                    if (count($imagePaths) >= 3) break;
                    
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $result = $fileService->saveFile($image, FileDirectoryEnum::DELIVERY_PROOF->value, 'public');
                        $imagePaths[] = $result['file_name'];
                        \Log::info('✅ Image saved', ['filename' => $result['file_name']]);
                    }
                }
            }

            // Handle video upload
            if (!empty($data['video']) && $data['video'] instanceof \Illuminate\Http\UploadedFile) {
                $result = $fileService->saveFile($data['video'], FileDirectoryEnum::DELIVERY_PROOF->value, 'public');
                $videoPath = $result['file_name'];
                \Log::info('✅ Video saved', ['filename' => $videoPath]);
            }

            // Update order
            $order->delivery_proof_images = !empty($imagePaths) ? json_encode($imagePaths) : null;
            $order->delivery_proof_video = $videoPath;
            $order->estimated_delivery_date_start = $data['estimated_delivery_date_start'];
            $order->estimated_delivery_date_end = $data['estimated_delivery_date_end'];
            $order->status = OrderStatusEnum::SHIPPED->value;
            
            \Log::info('💾 Saving order with delivery proof', [
                'order_id' => $order->order_id,
                'delivery_proof_images' => $order->delivery_proof_images,
                'delivery_proof_video' => $order->delivery_proof_video,
                'estimated_delivery_date_start' => $order->estimated_delivery_date_start,
                'estimated_delivery_date_end' => $order->estimated_delivery_date_end,
                'status' => $order->status
            ]);
            
            $order->save();

            $order->refresh();
            $order->load(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup']);

            \Log::info('✅ Order saved and refreshed', [
                'order_id' => $order->order_id,
                'delivery_proof_images_after_refresh' => $order->delivery_proof_images,
                'delivery_proof_video_after_refresh' => $order->delivery_proof_video
            ]);

            return $order;
        });
    }

    public function generateInvoiceFromOrder(array $data) {
        return DB::transaction(function () use ($data) {
            $order = Order::query()
                ->with(['productOrders.product', 'user', 'barangay.municity.province.region.islandGroup'])
                ->where('order_id', $data['order_id'])
                ->first();

            abort_if(empty($order), 404, 'Order not found!');
            abort_if($order->status != OrderStatusEnum::CONFIRMED->value, 400, 'Order must be in Confirmed status to generate invoice.');
            abort_if(empty($order->date_paid_confirmed), 400, 'Payment must be confirmed before generating invoice.');

            // Check if invoice already exists
            if (!empty($order->sale_id)) {
                $existingSale = Sale::query()
                    ->with(['saleItems.product', 'user'])
                    ->where('sale_id', $order->sale_id)
                    ->first();
                
                if ($existingSale) {
                    return $existingSale;
                }
            }

            // Get authenticated admin user
            $authService = new AuthService();
            $adminUser = $authService->getAuth();

            // Build customer address
            $customerAddress = $order->order_type === OrderTypeEnum::DELIVERY->value
                ? "{$order->other_details}, {$order->barangay->barangay_name}, {$order->barangay->municity->municity_name}, {$order->barangay->municity->province->province_name}, {$order->postal_code}"
                : 'Pickup Order';

            // Calculate total amount (products + shipping fee)
            $totalAmount = $order->total_amount ?? 0;

            // Map order payment method to sale payment method
            // Order: "Online Payment", "Cash on Delivery"
            // Sale: "Cash", "E-wallet", "Installment"
            $salePaymentMethod = match($order->payment_method) {
                'Cash on Delivery' => 'Cash',
                'Online Payment' => 'E-wallet', // Online payment is via e-wallet (GCash, etc.)
                default => 'Cash'
            };

            // Create Sale record
            $sale = Sale::create([
                'sale_code' => $order->order_public_id, // Use order public ID as sale code
                'user_id' => $adminUser->user_id, // Admin who generated the invoice
                'total_amount' => $totalAmount,
                'customer_name' => $order->user->full_name,
                'customer_address' => $customerAddress,
                'prepared_by' => $adminUser->full_name,
                'payment_method' => $salePaymentMethod
            ]);

            // Create SaleItem records from ProductOrder
            $saleItems = [];
            foreach ($order->productOrders as $productOrder) {
                $saleItems[] = [
                    'sale_id' => $sale->sale_id,
                    'product_id' => $productOrder->product_id,
                    'quantity' => $productOrder->quantity,
                    'price' => $productOrder->price
                ];
            }
            $sale->saleItems()->createMany($saleItems);

            // Link sale to order
            $order->sale_id = $sale->sale_id;
            $order->invoice_generated_at = now();
            $order->save();

            // Reload sale with relationships
            $sale->refresh();
            $sale->load(['saleItems.product', 'user']);

            return $sale;
        });
    }
}
