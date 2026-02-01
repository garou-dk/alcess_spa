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
        ];
    }

    private function getStats($userId)
    {
        $orders = Order::where('user_id', $userId)->get();

        return [
            'total_spend' => $orders->whereIn('status', [OrderStatusEnum::CONFIRMED->value, OrderStatusEnum::SHIPPED->value, OrderStatusEnum::COMPLETED->value])
                                   ->sum('total_amount'),
            'total_orders' => $orders->count(),
            'pending_orders' => $orders->where('status', OrderStatusEnum::PROCESSING->value)->count(),
            'completed_orders' => $orders->where('status', OrderStatusEnum::COMPLETED->value)->count(),
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
