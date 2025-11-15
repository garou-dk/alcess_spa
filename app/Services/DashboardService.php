<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class DashboardService
{
    public function getMonthlyRevenue()
    {
        // Get current month dates
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Get last month dates
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        // Current month revenue (only confirmed paid orders)
        $currentMonthRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');
        
        // Last month revenue (only confirmed paid orders)
        $lastMonthRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
        
        // Calculate percentage increase/decrease
        $percentageChange = 0;
        if ($lastMonthRevenue > 0) {
            $percentageChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($currentMonthRevenue > 0) {
            // If last month was 0 but current month has revenue
            $percentageChange = 100;
        }
        
        return [
            'current_month' => [
                'period' => $currentMonthStart->format('F Y'),
                'revenue' => round($currentMonthRevenue, 2),
                'formatted_revenue' => '₱' . number_format($currentMonthRevenue, 2)
            ],
            'last_month' => [
                'period' => $lastMonthStart->format('F Y'),
                'revenue' => round($lastMonthRevenue, 2),
                'formatted_revenue' => '₱' . number_format($lastMonthRevenue, 2)
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
}
