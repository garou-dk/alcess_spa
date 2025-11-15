<?php

namespace App\Services;

use App\Models\Order;
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
    
    // Alternative method if you want to display in a view
    public function showMonthlyRevenue()
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $currentMonthRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');
        
        $lastMonthRevenue = Order::whereNotNull('date_paid_confirmed')
            ->whereBetween('date_paid_confirmed', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
        
        $percentageChange = 0;
        if ($lastMonthRevenue > 0) {
            $percentageChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } elseif ($currentMonthRevenue > 0) {
            $percentageChange = 100;
        }
        
        return [
            'currentMonthRevenue' => $currentMonthRevenue,
            'lastMonthRevenue' => $lastMonthRevenue,
            'percentageChange' => $percentageChange,
            'currentMonthStart' => $currentMonthStart,
            'lastMonthStart' => $lastMonthStart
        ];
    }
}
