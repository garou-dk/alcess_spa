<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Events\DashboardStatsUpdateEvent;
use App\Models\BatchProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Rate;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function getMonthlyRevenue(): array
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $currentMonthOrderRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');

        $currentMonthSaleRevenue = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->leftJoin('orders', 'sales.sale_id', '=', 'orders.sale_id')
            ->whereNull('orders.sale_id') // Only sales without linked orders
            ->whereBetween('sale_items.created_at', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('SUM(sale_items.price * sale_items.quantity) as total')
            ->value('total') ?? 0;

        $currentMonthRevenue = $currentMonthOrderRevenue + $currentMonthSaleRevenue;

        $lastMonthOrderRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');

        $lastMonthSaleRevenue = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->leftJoin('orders', 'sales.sale_id', '=', 'orders.sale_id')
            ->whereNull('orders.sale_id') // Only sales without linked orders
            ->whereBetween('sale_items.created_at', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw('SUM(sale_items.price * sale_items.quantity) as total')
            ->value('total') ?? 0;

        $lastMonthRevenue = $lastMonthOrderRevenue + $lastMonthSaleRevenue;

        $percentageChange = 0;
        if ($lastMonthRevenue > 0) {
            $percentageChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($currentMonthRevenue > 0) {
            $percentageChange = 100;
        }

        return [
            'current_month' => [
                'period' => $currentMonthStart->format('F Y'),
                'revenue' => round($currentMonthRevenue, 2),
                'formatted_revenue' => '₱' . number_format($currentMonthRevenue, 2),
                'breakdown' => [
                    'orders' => round($currentMonthOrderRevenue, 2),
                    'sales' => round($currentMonthSaleRevenue, 2)
                ]
            ],
            'last_month' => [
                'period' => $lastMonthStart->format('F Y'),
                'revenue' => round($lastMonthRevenue, 2),
                'formatted_revenue' => '₱' . number_format($lastMonthRevenue, 2),
                'breakdown' => [
                    'orders' => round($lastMonthOrderRevenue, 2),
                    'sales' => round($lastMonthSaleRevenue, 2)
                ]
            ],
            'comparison' => [
                'percentage_change' => round($percentageChange, 2),
                'trend' => $percentageChange > 0 ? 'increase' : ($percentageChange < 0 ? 'decrease' : 'no change'),
                'difference' => round($currentMonthRevenue - $lastMonthRevenue, 2),
                'formatted_difference' => '₱' . number_format(abs($currentMonthRevenue - $lastMonthRevenue), 2)
            ]
        ];
    }



    public function getPendingOrdersReport(): array
    {
        // Count new orders that are Processing and NOT yet accepted by admin
        // Handle both false and NULL values for backward compatibility
        $totalNew = Order::where('status', OrderStatusEnum::PROCESSING->value)
            ->where(function ($query) {
                $query->where('admin_accepted', false)
                    ->orWhereNull('admin_accepted');
            })
            ->count();

        $todayNew = Order::where('status', OrderStatusEnum::PROCESSING->value)
            ->where(function ($query) {
                $query->where('admin_accepted', false)
                    ->orWhereNull('admin_accepted');
            })
            ->whereDate('created_at', today())
            ->count();

        return [
            'total_pending' => $totalNew,
            'today_pending' => $todayNew,
        ];
    }

    public function getTotalInventoryValue(): array
    {
        $totalValue = Product::where('is_active', true)
            ->selectRaw('SUM(product_price * product_quantity) as total_inventory_value')
            ->value('total_inventory_value') ?? 0;

        $totalProducts = Product::where('is_active', true)->count();

        return [
            'total_inventory_value' => $totalValue,
            'total_products' => $totalProducts,
        ];
    }

    public function getTop5MostSoldItems(): array
    {
        $orderQuantities = ProductOrder::join('orders', 'product_orders.order_id', '=', 'orders.order_id')
            ->whereNotNull('orders.date_paid_confirmed')
            ->selectRaw('product_orders.product_id, SUM(product_orders.quantity) as total_quantity')
            ->groupBy('product_orders.product_id')
            ->pluck('total_quantity', 'product_id');

        $saleQuantities = SaleItem::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->pluck('total_quantity', 'product_id');

        $combinedQuantities = [];

        foreach ($orderQuantities as $productId => $quantity) {
            $combinedQuantities[$productId] = ($combinedQuantities[$productId] ?? 0) + $quantity;
        }

        foreach ($saleQuantities as $productId => $quantity) {
            $combinedQuantities[$productId] = ($combinedQuantities[$productId] ?? 0) + $quantity;
        }

        arsort($combinedQuantities);
        $top5ProductIds = array_slice(array_keys($combinedQuantities), 0, 5, true);

        $products = Product::with(['unit', 'category'])
            ->whereIn('product_id', $top5ProductIds)
            ->get()
            ->keyBy('product_id');

        $result = [];
        foreach ($top5ProductIds as $productId) {
            $product = $products->get($productId);
            if ($product) {
                $result[] = [
                    'product_id' => $productId,
                    'product_name' => $product->product_name,
                    'product_image' => $product->product_image,
                    'units_sold' => $combinedQuantities[$productId],
                    'unit' => $product->unit,
                    'category' => $product->category,
                ];
            }
        }

        return $result;
    }

    public function getCurrentMonthReport(?string $startDate = null, ?string $endDate = null): array
    {
        // Use provided dates or default to current month
        if ($startDate && $endDate) {
            $currentMonthStart = Carbon::parse($startDate)->startOfDay();
            $currentMonthEnd = Carbon::parse($endDate)->endOfDay();
        } else {
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
        }

        $ordersCount = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $salesCount = Sale::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $productOrdersRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');

        $saleItemsRevenue = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->leftJoin('orders', 'sales.sale_id', '=', 'orders.sale_id')
            ->whereNull('orders.sale_id') // Only sales without linked orders
            ->whereBetween('sales.created_at', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('SUM(sale_items.price * sale_items.quantity) as total')
            ->value('total') ?? 0;

        return [
            'orders_count' => $ordersCount,
            'sales_count' => $salesCount,
            'product_orders_revenue' => round($productOrdersRevenue, 2),
            'sale_items_revenue' => round($saleItemsRevenue, 2),
            'total_revenue' => round($productOrdersRevenue + $saleItemsRevenue, 2),
        ];
    }

    public function getRevenueByCategories(?string $startDate = null, ?string $endDate = null): array
    {
        // Use provided dates or default to current month
        if ($startDate && $endDate) {
            $currentMonthStart = Carbon::parse($startDate)->startOfDay();
            $currentMonthEnd = Carbon::parse($endDate)->endOfDay();
        } else {
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
        }

        // Orders Revenue - all paid orders (online transactions)
        $orderRevenue = DB::table('product_orders')
            ->join('orders', 'product_orders.order_id', '=', 'orders.order_id')
            ->join('products', 'product_orders.product_id', '=', 'products.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->whereNotNull('orders.date_paid_confirmed')
            ->whereBetween('orders.date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('categories.category_id, categories.category_name, SUM(product_orders.price * product_orders.quantity) as revenue')
            ->groupBy('categories.category_id', 'categories.category_name')
            ->get()
            ->keyBy('category_id');

        // Sales Revenue - only sales that are NOT linked to orders (pure POS/walk-in transactions)
        $saleRevenue = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->leftJoin('orders', 'sales.sale_id', '=', 'orders.sale_id')
            ->whereNull('orders.sale_id') // Only sales without linked orders
            ->whereBetween('sales.created_at', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('categories.category_id, categories.category_name, SUM(sale_items.price * sale_items.quantity) as revenue')
            ->groupBy('categories.category_id', 'categories.category_name')
            ->get()
            ->keyBy('category_id');

        $allCategoryIds = $orderRevenue->keys()->merge($saleRevenue->keys())->unique();

        $result = [];
        foreach ($allCategoryIds as $categoryId) {
            $orderRev = $orderRevenue->get($categoryId);
            $saleRev = $saleRevenue->get($categoryId);

            $orderRevenueAmount = $orderRev ? $orderRev->revenue : 0;
            $saleRevenueAmount = $saleRev ? $saleRev->revenue : 0;
            $totalRevenue = $orderRevenueAmount + $saleRevenueAmount;

            // Skip categories with zero revenue
            if ($totalRevenue <= 0) {
                continue;
            }

            $categoryName = $orderRev ? $orderRev->category_name : $saleRev->category_name;

            $result[] = [
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'orders_revenue' => round($orderRevenueAmount, 2),
                'sales_revenue' => round($saleRevenueAmount, 2),
                'total_revenue' => round($totalRevenue, 2),
            ];
        }



        // Sort by total revenue (highest first)
        usort($result, function ($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        return $result;
    }

    public function getStockInOutReport(?string $startDate = null, ?string $endDate = null): array
    {
        // Use provided dates or default to last 7 days
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // Calculate the number of days in the range
            $daysDiff = $start->diffInDays($end) + 1; // +1 to include both start and end dates

            // Limit to reasonable range (max 30 days for performance)
            $daysDiff = min($daysDiff, 30);
        } else {
            // Default: last 7 days
            $end = Carbon::now()->endOfDay();
            $start = Carbon::now()->subDays(6)->startOfDay();
            $daysDiff = 7;
        }

        $stockOut = ProductOrder::join('orders', 'product_orders.order_id', '=', 'orders.order_id')
            ->whereIn('orders.status', [OrderStatusEnum::SHIPPED->value, OrderStatusEnum::COMPLETED->value])
            ->whereBetween('product_orders.created_at', [$start, $end])
            ->selectRaw('DATE(product_orders.created_at) as date, SUM(product_orders.quantity) as quantity')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('quantity', 'date');

        $stockIn = BatchProduct::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as quantity')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('quantity', 'date');

        $result = [];
        $currentDate = $start->copy();

        for ($i = 0; $i < $daysDiff; $i++) {
            $dateKey = $currentDate->format('Y-m-d');

            $stockInQty = $stockIn->get($dateKey, 0);
            $stockOutQty = $stockOut->get($dateKey, 0);

            $result[] = [
                'date' => $dateKey,
                'day' => $currentDate->format('l'),
                'stock_in' => (int) $stockInQty,
                'stock_out' => (int) $stockOutQty,
                'net_change' => (int) ($stockInQty - $stockOutQty),
            ];

            $currentDate->addDay();
        }

        return $result;
    }

    public function getPendingOrders()
    {
        // Get new orders that are Processing and NOT yet accepted by admin
        // Handle both false and NULL values for backward compatibility
        $newOrders = Order::with('user')
            ->where('status', OrderStatusEnum::PROCESSING->value)
            ->where(function ($query) {
                $query->where('admin_accepted', false)
                    ->orWhereNull('admin_accepted');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return $newOrders;
    }

    public function getRevenueReport($startDate = null, $endDate = null): array
    {
        // Set default date range if not provided
        $startDate = $startDate ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $endDate ?? now()->endOfDay()->format('Y-m-d');

        // Query for product_orders
        $orderRevenue = ProductOrder::join('orders', 'product_orders.order_id', '=', 'orders.order_id')
            ->join('products', 'product_orders.product_id', '=', 'products.product_id')
            ->whereNotNull('orders.date_paid_confirmed')
            ->whereBetween(DB::raw('DATE(orders.date_paid_confirmed)'), [$startDate, $endDate])
            ->select(
                DB::raw('DATE(orders.date_paid_confirmed) as date'),
                'orders.order_public_id as reference_code',
                'products.product_name',
                'product_orders.quantity',
                'product_orders.price',
                DB::raw('(product_orders.quantity * product_orders.price) as total'),
                DB::raw("'Order' as type")
            );

        // Query for sale_items
        $saleRevenue = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.product_id')
            ->whereBetween(DB::raw('DATE(sales.created_at)'), [$startDate, $endDate])
            ->select(
                DB::raw('DATE(sales.created_at) as date'),
                'sales.sale_code as reference_code',
                'products.product_name',
                'sale_items.quantity',
                'sale_items.price',
                DB::raw('(sale_items.quantity * sale_items.price) as total'),
                DB::raw("'Sale' as type")
            );

        // Union both queries and order by date descending
        $results = $orderRevenue
            ->unionAll($saleRevenue)
            ->orderBy('date', 'desc')
            ->get();

        return $results->map(function ($item) {
            return [
                'date' => $item->date,
                'reference_code' => $item->reference_code,
                'product_name' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'price' => round($item->price, 2),
                'total' => round($item->total, 2),
                'type' => $item->type,
            ];
        })->toArray();
    }

    public function getForDeliveryReport(array $data): array
    {
        $query = Order::with(['user', 'productOrders.product', 'barangay.municity.province.region.islandGroup']);

        // Apply date range filter only if both dates are provided
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('created_at', [
                $data['start_date'],
                $data['end_date'],
            ]);
        }

        $forDeliveryOrders = $query->orderBy('created_at', 'desc')->get();

        return $forDeliveryOrders->map(function ($order) {
            $products = $order->productOrders->map(function ($productOrder) {
                return [
                    'product_name' => $productOrder->product->product_name,
                    'quantity' => $productOrder->quantity,
                    'price' => round($productOrder->price, 2),
                    'subtotal' => round($productOrder->quantity * $productOrder->price, 2),
                ];
            });

            return [
                'order_id' => $order->order_id,
                'order_public_id' => $order->order_public_id,
                'customer_name' => $order->user ? $order->user->full_name : 'N/A',
                'contact_number' => $order->contact_number,
                'barangay_id' => $order->barangay_id,
                'other_details' => $order->other_details,
                'postal_code' => $order->postal_code,
                'delivery_courier' => $order->delivery_courier,
                'tracking_number' => $order->tracking_number,
                'estimated_delivery_start' => $order->estimated_delivery_date_start,
                'estimated_delivery_end' => $order->estimated_delivery_date_end,
                'shipping_fee' => round($order->shipping_fee ?? 0, 2),
                'total_amount' => round($order->total_amount, 2),
                'payment_method' => $order->payment_method,
                'date_paid_confirmed' => $order->date_paid_confirmed?->format('Y-m-d H:i:s'),
                'products' => $products->toArray(),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'days_in_delivery' => $order->created_at->diffInDays(now()),
                'customer_received_date' => $order->customer_received_date,
                'address' => $order->barangay
                    ? "{$order->other_details}, {$order->barangay->barangay_name}, {$order->barangay->municity->municity_name}, {$order->barangay->municity->province->province_name}, {$order->barangay->municity->province->region->islandGroup->island_group_name}"
                    : ($order->other_details ?? 'N/A'),
            ];
        })->toArray();
    }

    public function getOnlineSalesData(?string $startDate, ?string $endDate): array
    {
        return $this->reportService->getOnlineSalesData($startDate, $endDate);
    }

    public function getWalkInSalesData(?string $startDate, ?string $endDate): array
    {
        return $this->reportService->getWalkInSalesData($startDate, $endDate);
    }

    public function getCustomerReviewData(array $data = []): array
    {
        return $this->reportService->getCustomerReviewData($data);
    }

    /**
     * Broadcast dashboard stats update event
     * Call this when orders or products change to update dashboard in real-time
     */
    public function broadcastDashboardStatsUpdate(): void
    {
        // Since we removed the statistic cards, we can simplify this
        // or remove it entirely if no longer needed
        event(new DashboardStatsUpdateEvent([]));
    }
}
