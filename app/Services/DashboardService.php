<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;

class DashboardService
{
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
            ->whereBetween('sale_items.created_at', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('SUM(sale_items.price * sale_items.quantity) as total')
            ->value('total') ?? 0;
        
        $currentMonthRevenue = $currentMonthOrderRevenue + $currentMonthSaleRevenue;
        
        $lastMonthOrderRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
        
        $lastMonthSaleRevenue = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
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

    public function getProductStats()
    {
        $totalProducts = Product::where('is_active', true)->count();
        
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('product_quantity', '<=', 'low_stock_threshold')
            ->count();
        
        return [
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockProducts,
            'healthy_stock_products' => $totalProducts - $lowStockProducts,
            'low_stock_percentage' => $totalProducts > 0 
                ? round(($lowStockProducts / $totalProducts) * 100, 2) 
                : 0
        ];
    }

    public function getPendingOrdersReport(): array
    {
        $totalPending = Order::where('status', 'Pending')->count();
        
        $todayPending = Order::where('status', 'Pending')
            ->whereDate('created_at', today())
            ->count();
        
        return [
            'total_pending' => $totalPending,
            'today_pending' => $todayPending,
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

    public function getCurrentMonthReport(): array
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        $ordersCount = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->count();
        
        $salesCount = Sale::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        
        $productOrdersRevenue = ProductOrder::join('orders', 'product_orders.order_id', '=', 'orders.order_id')
            ->whereNotNull('orders.date_paid_confirmed')
            ->whereBetween('orders.date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->selectRaw('SUM(product_orders.price * product_orders.quantity) as total')
            ->value('total') ?? 0;
        
        $saleItemsRevenue = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
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

    public function getPrevious7DaysSales(): array
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        
        // Get daily revenue from orders (with date_paid_confirmed)
        $ordersSales = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$startDate, $endDate])
            ->selectRaw('DATE(date_paid_confirmed) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');
        
        // Get daily revenue from sale_items
        $saleItemsSales = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->selectRaw('DATE(sales.created_at) as date, SUM(sale_items.price * sale_items.quantity) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');
        
        // Generate array for all 7 days
        $result = [];
        $currentDate = $startDate->copy();
        
        for ($i = 0; $i < 7; $i++) {
            $dateKey = $currentDate->format('Y-m-d');
            
            $ordersRevenue = $ordersSales->get($dateKey, 0);
            $saleItemsRevenue = $saleItemsSales->get($dateKey, 0);
            $totalRevenue = $ordersRevenue + $saleItemsRevenue;
            
            $result[] = [
                'date' => $dateKey,
                'day' => $currentDate->format('l'),
                'orders_revenue' => round($ordersRevenue, 2),
                'sale_items_revenue' => round($saleItemsRevenue, 2),
                'total_revenue' => round($totalRevenue, 2),
            ];
            
            $currentDate->addDay();
        }
        
        return array_reverse($result);
    }
}
