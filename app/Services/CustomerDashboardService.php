<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CustomerDashboardService
{
    public function getDashboardData($userId)
    {
        return [
            'stats' => $this->getStats($userId),
            'monthly_spend' => $this->getMonthlySpend($userId),
            'recent_orders' => $this->getRecentOrders($userId),
            'featured_products' => $this->getFeaturedProducts(),
            'best_sellers' => $this->getBestSellingProducts(),
            'categories' => \App\Models\Category::where('is_active', true)->limit(8)->get(),
        ];
    }

    private function getBestSellingProducts()
    {
        return Product::query()
            ->with(['category', 'rates'])
            ->withAvg('rates', 'rate')
            ->withCount('productOrders')
            ->where('is_active', true)
            ->where('available_online', true)
            ->orderBy('product_orders_count', 'desc')
            ->limit(6)
            ->get();
    }

    private function getStats($userId)
    {
        // Use direct database queries for accurate counts
        $totalOrders = Order::where('user_id', $userId)->count();
        
        $pendingOrders = Order::where('user_id', $userId)
            ->where('status', OrderStatusEnum::PROCESSING->value)
            ->count();
        
        $completedOrders = Order::where('user_id', $userId)
            ->where('status', OrderStatusEnum::COMPLETED->value)
            ->count();
        
        $totalSpend = Order::where('user_id', $userId)
            ->whereIn('status', [
                OrderStatusEnum::CONFIRMED->value, 
                OrderStatusEnum::SHIPPED->value, 
                OrderStatusEnum::COMPLETED->value
            ])
            ->sum('total_amount');

        return [
            'total_spend' => $totalSpend,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
        ];
    }

    private function getMonthlySpend($userId)
    {
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        $spendData = Order::where('user_id', $userId)
            ->whereIn('status', [OrderStatusEnum::CONFIRMED->value, OrderStatusEnum::SHIPPED->value, OrderStatusEnum::COMPLETED->value])
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill missing months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[now()->subMonths($i)->format('Y-m')] = 0;
        }

        foreach ($spendData as $data) {
            $months[$data->month] = (float)$data->total;
        }

        return [
            'labels' => array_keys($months),
            'data' => array_values($months),
        ];
    }

    private function getRecentOrders($userId)
    {
        return Order::where('user_id', $userId)
            ->with(['productOrders.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getFeaturedProducts()
    {
        // Reuse best selling logic from ProductService if possible, or replicate it
        return Product::query()
            ->with(['category', 'rates'])
            ->withAvg('rates', 'rate')
            ->where('is_active', true)
            ->where('available_online', true)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
    }
}
