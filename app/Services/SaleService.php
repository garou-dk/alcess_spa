<?php

namespace App\Services;

use App\Events\OrderNotificationEvent;
use App\Models\OrderNotification;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class SaleService
{
    public function recordSale(array $data) {
        return DB::transaction(function () use ($data) {
            $authService = new AuthService();
            $user = $authService->getAuth();
            $productIds = collect($data["products"])->pluck("product_id")->toArray();

            // Lock the products for update to prevent race conditions
            // This ensures that concurrent POS sales don't oversell inventory
            $productList = Product::query()
                ->whereIn("product_id", $productIds)
                ->lockForUpdate()
                ->get();

            $outOfStockItems = [];
            $insufficientStockItems = [];

            // Validate stock for each product before proceeding
            foreach ($data["products"] as $item) {
                $product = $productList->where("product_id", $item["product_id"])->first();
                
                if (!$product) {
                    continue; // Product doesn't exist, will be caught by validation
                }
                
                // Check if product is completely out of stock
                if ($product->product_quantity <= 0) {
                    $outOfStockItems[] = [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'requested_quantity' => $item['quantity'],
                        'available_quantity' => 0
                    ];
                    continue;
                }
                
                // Check if requested quantity exceeds available stock
                if ($item['quantity'] > $product->product_quantity) {
                    $insufficientStockItems[] = [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'requested_quantity' => $item['quantity'],
                        'available_quantity' => $product->product_quantity
                    ];
                    continue;
                }
            }

            // If any items are out of stock, abort with detailed error
            if (!empty($outOfStockItems)) {
                $itemNames = collect($outOfStockItems)->pluck('product_name')->implode(', ');
                abort(422, "The following items are sold out: {$itemNames}. Please remove them from the cart and try again.");
            }

            // If any items have insufficient stock, abort with detailed error
            if (!empty($insufficientStockItems)) {
                $itemDetails = collect($insufficientStockItems)->map(function ($item) {
                    return "{$item['product_name']} (only {$item['available_quantity']} available)";
                })->implode(', ');
                abort(422, "Insufficient stock for: {$itemDetails}. Please reduce the quantity and try again.");
            }

            // All stock validation passed, calculate total
            $total_amount = 0;
            foreach ($data["products"] as $item) {
                $total_amount += $item["quantity"] * $productList->where("product_id", $item["product_id"])->value("product_price");
            }

            $sale = Sale::query()->create([
                "sale_code" => Uuid::uuid4()->toString(),
                "user_id" => $user->user_id,
                "total_amount" => $total_amount,
                "customer_name" => $data["customer_name"],
                "customer_address" => $data["customer_address"],
                "customer_phone" => $data["customer_phone"],
                "prepared_by" => $data["prepared_by"],
                "payment_method" => $data["payment_method"]
            ]);

            $products = [];
            foreach ($data["products"] as $item) {
                $products[] = [
                    "sale_id" => $sale->sale_id, 
                    "product_id" => $item["product_id"], 
                    "quantity" => $item["quantity"], 
                    "price" => $productList->where("product_id", $item["product_id"])->value("product_price")
                ];
                // Deduct inventory
                Product::query()
                    ->where("product_id", $item["product_id"])
                    ->decrement("product_quantity", $item["quantity"]);
            }
            $sale->saleItems()->createMany($products);

            // Check stock levels after deduction and notify admin/staff
            foreach ($data["products"] as $item) {
                $product = Product::find($item["product_id"]);
                if ($product) {
                    if ($product->product_quantity <= 0) {
                        $notification = OrderNotification::create([
                            'user_id' => $user->user_id,
                            'notification_type' => 'Out of Stock',
                            'notification_to' => 'Store',
                            'message' => "{$product->product_name} is now out of stock (0 remaining).",
                        ]);
                        OrderNotificationEvent::dispatch($notification->toArray());
                    } elseif ($product->low_stock_threshold && $product->product_quantity <= $product->low_stock_threshold) {
                        $notification = OrderNotification::create([
                            'user_id' => $user->user_id,
                            'notification_type' => 'Low Stock',
                            'notification_to' => 'Store',
                            'message' => "{$product->product_name} is nearly out of stock ({$product->product_quantity} remaining).",
                        ]);
                        OrderNotificationEvent::dispatch($notification->toArray());
                    }
                }
            }

            $sale->refresh();

            $sale->load('saleItems');

            return $sale;
        });
    }

    public function getSale(array $data) {
        $sale = Sale::query()
            ->with(['saleItems.product', 'user'])
            ->where('sale_id', $data['sale_id'])
            ->first();

        abort_if(empty($sale), 404, 'Sale not found');

        return $sale;
    }

    public function getTodayStats() {
        $today = now()->startOfDay();
        
        $stats = Sale::query()
            ->whereDate('created_at', $today)
            ->selectRaw('COUNT(*) as total_transactions, COALESCE(SUM(total_amount), 0) as total_sales')
            ->first();

        return [
            'total_sales' => $stats->total_sales ?? 0,
            'total_transactions' => $stats->total_transactions ?? 0,
        ];
    }

    public function getAllSales() {
        return Sale::query()
            ->with(['saleItems.product', 'user'])
            ->whereDoesntHave('order') // Only show POS sales, not sales generated from online orders
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
